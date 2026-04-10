<?php

namespace App\Console\Commands;

use App\Enums\WorkflowNodeStatus;
use App\Enums\WorkflowParticipantVisibility;
use App\Enums\WorkflowValidationPolicy;
use App\Models\Operation;
use App\Models\OperationWorkflowNode;
use App\Services\Workflow\WorkflowEngine;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class WorkflowMaterializeFromStagesCommand extends Command
{
    protected $signature = 'workflow:materialize-from-stages {operation_id? : ID operation ciblée} {--force : Réinstancier même si un workflow existe}';

    protected $description = 'Matérialise un workflow linéaire à partir des stages legacy (operation_stage).';

    public function handle(WorkflowEngine $engine): int
    {
        $operationId = $this->argument('operation_id');
        $force = (bool) $this->option('force');

        $operations = Operation::query()
            ->when($operationId, fn ($q) => $q->whereKey($operationId))
            ->with(['stages' => fn ($q) => $q->orderBy('stages.order')])
            ->get();

        if ($operations->isEmpty()) {
            $this->warn('Aucune opération trouvée.');

            return self::SUCCESS;
        }

        $materialized = 0;
        $skipped = 0;

        foreach ($operations as $operation) {
            if (! $force && $operation->workflowNodes()->exists()) {
                $this->line("Opération {$operation->id} ignorée : workflow déjà présent.");
                $skipped++;

                continue;
            }

            if ($operation->stages->isEmpty()) {
                $this->line("Opération {$operation->id} ignorée : aucun stage legacy.");
                $skipped++;

                continue;
            }

            DB::transaction(function () use ($operation, $engine, $force): void {
                if ($force) {
                    $operation->workflowNodes()->delete();
                }

                $parentId = null;
                $activeNode = null;

                foreach ($operation->stages as $index => $stage) {
                    $legacyStatus = (string) ($stage->pivot->status ?? WorkflowNodeStatus::Pending->value);
                    $nodeStatus = WorkflowNodeStatus::tryFrom($legacyStatus) ?? WorkflowNodeStatus::Pending;

                    $node = OperationWorkflowNode::create([
                        'operation_id' => $operation->id,
                        'workflow_template_node_id' => null,
                        'parent_id' => $parentId,
                        'parallel_group' => null,
                        'is_merge_node' => false,
                        'sort_order' => $index,
                        'title' => $stage->name,
                        'description' => $stage->description,
                        'validation_policy' => WorkflowValidationPolicy::LotixamOnly,
                        'participant_visibility' => WorkflowParticipantVisibility::AllAssigned,
                        'status' => $nodeStatus,
                        'started_at' => $nodeStatus === WorkflowNodeStatus::InProgress ? now() : null,
                        'completed_at' => $stage->pivot->completed_at,
                        'metadata' => [
                            'legacy_stage_id' => $stage->id,
                            'legacy_source' => $stage->pivot->source ?? null,
                        ],
                    ]);

                    if ($nodeStatus === WorkflowNodeStatus::InProgress) {
                        $activeNode = $node;
                    }

                    $parentId = $node->id;
                }

                if (! $activeNode) {
                    $candidate = $operation->workflowNodes()
                        ->where('status', WorkflowNodeStatus::Pending)
                        ->orderBy('sort_order')
                        ->first();

                    if ($candidate) {
                        $engine->startNode($candidate);
                    }
                }
            });

            $this->info("Opération {$operation->id} matérialisée.");
            $materialized++;
        }

        $this->newLine();
        $this->info("Terminé : {$materialized} matérialisée(s), {$skipped} ignorée(s).");

        return self::SUCCESS;
    }
}
