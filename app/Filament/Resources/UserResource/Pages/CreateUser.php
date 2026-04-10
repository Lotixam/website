<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $role = (string) ($data['role'] ?? '');

        if (in_array($role, ['client', 'collaborator', 'seller'], true)) {
            if (empty($data['partner_id'])) {
                throw ValidationException::withMessages([
                    'partner_id' => 'Une entreprise est obligatoire pour un client, un vendeur ou un collaborateur.',
                ]);
            }
        } else {
            $data['partner_id'] = null;
        }

        $data['name'] = UserResource::displayNameFromProfile(
            is_array($data['profile'] ?? null) ? $data['profile'] : [],
            isset($data['email']) ? (string) $data['email'] : null,
        );

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->record->syncRoles([$this->data['role']]);
    }
}
