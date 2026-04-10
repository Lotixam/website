<?php

namespace Database\Seeders;

use App\Enums\WorkflowParticipantVisibility;
use App\Enums\WorkflowValidationPolicy;
use App\Models\WorkflowTemplate;
use App\Models\WorkflowTemplateNode;
use Illuminate\Database\Seeder;

class WorkflowTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedTerrainVendeurParticulier();
        $this->seedDivisionParcellaire();
        $this->seedImmeubleDeRapport();
        $this->seedMaisonIndividuelle();
        $this->seedRenovationRevente();
        $this->seedOperationB2bPartenaire();
        $this->seedInvestissementLocatif();
    }

    private function seedTerrainVendeurParticulier(): void
    {
        $t = WorkflowTemplate::query()->firstOrCreate(
            ['key' => 'terrain_vendeur_particulier'],
            [
                'name' => 'Terrain — Vendeur particulier',
                'description' => 'Achat direct d\'un terrain à un particulier : négociation, notaire, validation, cession.',
                'is_active' => true,
            ]
        );

        if ($t->nodes()->exists()) {
            return;
        }

        $this->linearSteps($t, [
            ['title' => 'Propositions, prix et accord', 'validation' => WorkflowValidationPolicy::BothAll],
            ['title' => 'Compromis de vente', 'validation' => WorkflowValidationPolicy::BothAll],
            ['title' => 'Documents chez le notaire', 'validation' => WorkflowValidationPolicy::BothAll],
            ['title' => 'Validation de l\'achat (Lotixam)', 'validation' => WorkflowValidationPolicy::LotixamOnly],
            ['title' => 'Signature acte authentique', 'validation' => WorkflowValidationPolicy::BothAll],
            ['title' => 'Preuve de cession', 'validation' => WorkflowValidationPolicy::BothAll],
        ]);
    }

    private function seedDivisionParcellaire(): void
    {
        $t = WorkflowTemplate::query()->firstOrCreate(
            ['key' => 'division_parcellaire'],
            [
                'name' => 'Division parcellaire',
                'description' => 'Achat terrain, branches parallèles (financeur / apporteur / constructeur), fusion, revente lots.',
                'is_active' => true,
            ]
        );

        if ($t->nodes()->exists()) {
            return;
        }

        $start = $this->node($t, null, 'Démarrage du projet', 0, policy: WorkflowValidationPolicy::LotixamOnly, visibility: WorkflowParticipantVisibility::HideFromSeller, description: 'Notes, collaborateurs, financeurs, apporteurs.');

        $fork = 'parcel_fork_1';
        $this->node($t, $start->id, 'Branche financeur', 0, group: $fork, policy: WorkflowValidationPolicy::BothAll, visibility: WorkflowParticipantVisibility::HideFromSeller, description: 'Acceptation projet, documents, montant.');
        $this->node($t, $start->id, 'Branche apporteur d\'affaires', 1, group: $fork, policy: WorkflowValidationPolicy::LotixamOnly, visibility: WorkflowParticipantVisibility::AdminOnly, description: 'Visible admin uniquement (marge, négociation).');
        $this->node($t, $start->id, 'Branche constructeur', 2, group: $fork, policy: WorkflowValidationPolicy::BothAll, visibility: WorkflowParticipantVisibility::HideFromSeller, description: 'Plans, documents techniques.');

        $merge = $this->node(
            $t,
            $start->id,
            'Point de fusion',
            3,
            merge: true,
            visibility: WorkflowParticipantVisibility::HideFromSeller,
            description: 'Débloqué lorsque toutes les branches parallèles sont terminées.',
        );

        $this->node($t, $merge->id, 'Permis d\'aménager', 0, policy: WorkflowValidationPolicy::LotixamOnly, visibility: WorkflowParticipantVisibility::HideFromSeller);
        $this->node($t, $merge->id, 'Bornage / Géomètre', 1, policy: WorkflowValidationPolicy::LotixamOnly, visibility: WorkflowParticipantVisibility::HideFromSeller);
        $this->node($t, $merge->id, 'Mise en vente des lots', 2, policy: WorkflowValidationPolicy::BothAll);
        $this->node($t, $merge->id, 'Validation conjointe & clôture', 3, policy: WorkflowValidationPolicy::BothAll);
    }

    private function seedImmeubleDeRapport(): void
    {
        $t = WorkflowTemplate::query()->firstOrCreate(
            ['key' => 'immeuble_rapport'],
            [
                'name' => 'Immeuble de rapport',
                'description' => 'Achat d\'un immeuble complet, travaux, découpe en lots, revente ou location.',
                'is_active' => true,
            ]
        );

        if ($t->nodes()->exists()) {
            return;
        }

        $this->linearSteps($t, [
            ['title' => 'Identification du bien', 'validation' => WorkflowValidationPolicy::LotixamOnly],
            ['title' => 'Étude de rentabilité', 'validation' => WorkflowValidationPolicy::LotixamOnly],
            ['title' => 'Offre d\'achat', 'validation' => WorkflowValidationPolicy::LotixamOnly],
            ['title' => 'Compromis de vente', 'validation' => WorkflowValidationPolicy::BothAll],
            ['title' => 'Financement & montage bancaire', 'validation' => WorkflowValidationPolicy::LotixamOnly],
            ['title' => 'Acte authentique', 'validation' => WorkflowValidationPolicy::BothAll],
            ['title' => 'Travaux / Rénovation', 'validation' => WorkflowValidationPolicy::LotixamOnly],
            ['title' => 'Découpe en lots (copropriété)', 'validation' => WorkflowValidationPolicy::LotixamOnly],
            ['title' => 'Commercialisation', 'validation' => WorkflowValidationPolicy::BothAll],
            ['title' => 'Clôture de l\'opération', 'validation' => WorkflowValidationPolicy::LotixamOnly],
        ]);
    }

    private function seedMaisonIndividuelle(): void
    {
        $t = WorkflowTemplate::query()->firstOrCreate(
            ['key' => 'maison_individuelle'],
            [
                'name' => 'Maison individuelle',
                'description' => 'Achat / construction / revente d\'une maison individuelle.',
                'is_active' => true,
            ]
        );

        if ($t->nodes()->exists()) {
            return;
        }

        $this->linearSteps($t, [
            ['title' => 'Prospection terrain', 'validation' => WorkflowValidationPolicy::LotixamOnly],
            ['title' => 'Étude de faisabilité', 'validation' => WorkflowValidationPolicy::LotixamOnly],
            ['title' => 'Offre d\'achat terrain', 'validation' => WorkflowValidationPolicy::LotixamOnly],
            ['title' => 'Compromis de vente terrain', 'validation' => WorkflowValidationPolicy::BothAll],
            ['title' => 'Permis de construire', 'validation' => WorkflowValidationPolicy::LotixamOnly],
            ['title' => 'Sélection constructeur', 'validation' => WorkflowValidationPolicy::LotixamOnly],
            ['title' => 'Construction', 'validation' => WorkflowValidationPolicy::LotixamOnly],
            ['title' => 'Réception des travaux', 'validation' => WorkflowValidationPolicy::BothAll],
            ['title' => 'Mise en vente', 'validation' => WorkflowValidationPolicy::BothAll],
            ['title' => 'Acte authentique & livraison', 'validation' => WorkflowValidationPolicy::BothAll],
        ]);
    }

    private function seedRenovationRevente(): void
    {
        $t = WorkflowTemplate::query()->firstOrCreate(
            ['key' => 'renovation_revente'],
            [
                'name' => 'Rénovation — Revente',
                'description' => 'Achat d\'un bien ancien, rénovation puis revente (flip).',
                'is_active' => true,
            ]
        );

        if ($t->nodes()->exists()) {
            return;
        }

        $this->linearSteps($t, [
            ['title' => 'Identification du bien', 'validation' => WorkflowValidationPolicy::LotixamOnly],
            ['title' => 'Chiffrage travaux', 'validation' => WorkflowValidationPolicy::LotixamOnly],
            ['title' => 'Offre d\'achat', 'validation' => WorkflowValidationPolicy::LotixamOnly],
            ['title' => 'Compromis de vente', 'validation' => WorkflowValidationPolicy::BothAll],
            ['title' => 'Acte authentique (achat)', 'validation' => WorkflowValidationPolicy::BothAll],
            ['title' => 'Travaux de rénovation', 'validation' => WorkflowValidationPolicy::LotixamOnly],
            ['title' => 'Mise en vente', 'validation' => WorkflowValidationPolicy::BothAll],
            ['title' => 'Compromis de vente (revente)', 'validation' => WorkflowValidationPolicy::BothAll],
            ['title' => 'Acte authentique (revente)', 'validation' => WorkflowValidationPolicy::BothAll],
        ]);
    }

    private function seedOperationB2bPartenaire(): void
    {
        $t = WorkflowTemplate::query()->firstOrCreate(
            ['key' => 'operation_b2b_partenaire'],
            [
                'name' => 'Opération B2B / Partenaire',
                'description' => 'Montage avec financeur ou apporteur d\'affaires externe : accord, contractualisation, suivi, clôture.',
                'is_active' => true,
            ]
        );

        if ($t->nodes()->exists()) {
            return;
        }

        $start = $this->node($t, null, 'Accord de principe', 0, policy: WorkflowValidationPolicy::BothAll);

        $fork = 'b2b_fork_1';
        $this->node($t, $start->id, 'Contractualisation Lotixam', 0, group: $fork, policy: WorkflowValidationPolicy::LotixamOnly, visibility: WorkflowParticipantVisibility::AdminOnly);
        $this->node($t, $start->id, 'Engagement partenaire', 1, group: $fork, policy: WorkflowValidationPolicy::BothAll);

        $merge = $this->node($t, $start->id, 'Fusion', 2, merge: true);

        $this->linearSteps($t, [
            ['title' => 'Suivi opérationnel', 'validation' => WorkflowValidationPolicy::LotixamOnly],
            ['title' => 'Bilan & redistribution', 'validation' => WorkflowValidationPolicy::BothAll],
            ['title' => 'Clôture partenariat', 'validation' => WorkflowValidationPolicy::BothAll],
        ], $merge->id);
    }

    private function seedInvestissementLocatif(): void
    {
        $t = WorkflowTemplate::query()->firstOrCreate(
            ['key' => 'investissement_locatif'],
            [
                'name' => 'Investissement locatif',
                'description' => 'Achat d\'un bien pour mise en location : montage, acquisition, gestion locative, arbitrage.',
                'is_active' => true,
            ]
        );

        if ($t->nodes()->exists()) {
            return;
        }

        $this->linearSteps($t, [
            ['title' => 'Recherche du bien', 'validation' => WorkflowValidationPolicy::LotixamOnly],
            ['title' => 'Étude de rendement', 'validation' => WorkflowValidationPolicy::LotixamOnly],
            ['title' => 'Offre d\'achat', 'validation' => WorkflowValidationPolicy::LotixamOnly],
            ['title' => 'Montage financement', 'validation' => WorkflowValidationPolicy::LotixamOnly],
            ['title' => 'Compromis de vente', 'validation' => WorkflowValidationPolicy::BothAll],
            ['title' => 'Acte authentique', 'validation' => WorkflowValidationPolicy::BothAll],
            ['title' => 'Travaux éventuels', 'validation' => WorkflowValidationPolicy::LotixamOnly],
            ['title' => 'Mise en location', 'validation' => WorkflowValidationPolicy::LotixamOnly],
            ['title' => 'Gestion locative', 'validation' => WorkflowValidationPolicy::LotixamOnly],
            ['title' => 'Arbitrage / Revente', 'validation' => WorkflowValidationPolicy::BothAll],
        ]);
    }

    // ─── Helpers ────────────────────────────────────────────

    private function linearSteps(WorkflowTemplate $template, array $steps, ?int $parentId = null): void
    {
        foreach ($steps as $i => $step) {
            $n = $this->node(
                $template,
                $parentId,
                $step['title'],
                $i,
                policy: $step['validation'] ?? WorkflowValidationPolicy::LotixamOnly,
            );
            $parentId = $n->id;
        }
    }

    private function node(
        WorkflowTemplate $template,
        ?int $parentId,
        string $title,
        int $sort,
        ?string $group = null,
        bool $merge = false,
        WorkflowValidationPolicy $policy = WorkflowValidationPolicy::LotixamOnly,
        WorkflowParticipantVisibility $visibility = WorkflowParticipantVisibility::AllAssigned,
        ?string $description = null,
    ): WorkflowTemplateNode {
        return WorkflowTemplateNode::create([
            'workflow_template_id' => $template->id,
            'parent_id' => $parentId,
            'parallel_group' => $group,
            'is_merge_node' => $merge,
            'sort_order' => $sort,
            'title' => $title,
            'description' => $description,
            'validation_policy' => $policy,
            'participant_visibility' => $visibility,
        ]);
    }
}
