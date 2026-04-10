<?php

namespace App\Http\Resources\Api;

use App\Models\Operation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Operation
 */
class OperationResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'address' => $this->address,
            'city' => $this->city,
            'postal_code' => $this->postal_code,
            'type' => $this->type?->value,
            'status' => $this->status?->value,
            'mission' => $this->mission?->value,
            'total_surface' => $this->total_surface,
            'purchase_price' => $this->purchase_price,
            'purchase_date' => $this->purchase_date?->format('Y-m-d'),
            'estimated_resale_total' => $this->estimated_resale_total,
            'internal_objective' => $this->internal_objective,
            'participant_label' => $this->participant_label,
            'notes' => $this->notes,
            'seller_contact_id' => $this->seller_contact_id,
            'parent_operation_id' => $this->parent_operation_id,
            'workflow_template_id' => $this->workflow_template_id,
            'closed_at' => $this->closed_at?->toIso8601String(),
            'closed_by_user_id' => $this->closed_by_user_id,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
