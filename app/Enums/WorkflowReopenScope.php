<?php

namespace App\Enums;

enum WorkflowReopenScope: string
{
    case NodeOnly = 'node_only';
    case BranchSubtree = 'branch_subtree';
    case FromRoot = 'from_root';
    case SingleDocument = 'single_document';

    public function label(): string
    {
        return match ($this) {
            self::NodeOnly => 'Cette étape uniquement',
            self::BranchSubtree => 'Toute la branche',
            self::FromRoot => 'Depuis le début',
            self::SingleDocument => 'Un document uniquement',
        };
    }
}
