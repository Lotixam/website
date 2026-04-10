<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\ValidationException;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterFill(): void
    {
        $user = $this->getRecord();
        $user->loadMissing('profile');

        $first = $user->profile?->first_name;
        $last = $user->profile?->last_name;

        if (filled($first) || filled($last)) {
            return;
        }

        $raw = trim((string) $user->name);
        if ($raw === '') {
            return;
        }

        $parts = preg_split('/\s+/u', $raw, 2, PREG_SPLIT_NO_EMPTY) ?: [];

        $this->form->fillPartially([
            'profile' => [
                'first_name' => $parts[0] ?? '',
                'last_name' => $parts[1] ?? '',
            ],
        ], ['profile.first_name', 'profile.last_name']);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $role = (string) ($data['role'] ?? '');

        if ($role === 'admin') {
            $data['partner_id'] = null;
        } elseif (in_array($role, ['client', 'collaborator', 'seller'], true)) {
            if (empty($data['partner_id'])) {
                throw ValidationException::withMessages([
                    'partner_id' => 'Une entreprise est obligatoire pour un client, un vendeur ou un collaborateur.',
                ]);
            }
        }

        $profile = is_array($data['profile'] ?? null) ? $data['profile'] : [];
        $data['name'] = UserResource::displayNameFromProfile(
            $profile,
            isset($data['email']) ? (string) $data['email'] : $this->record->email,
        );

        return $data;
    }

    protected function afterSave(): void
    {
        $this->record->syncRoles([$this->data['role']]);
    }
}
