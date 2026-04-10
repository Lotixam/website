<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestEnvCommand extends Command
{
    protected $signature = 'test:env';

    protected $description = 'Vérifie les variables d\'environnement critiques de l\'application';

    /** @var array<string, array{required: bool, sensitive: bool}> */
    private array $checks = [
        'APP_NAME' => ['required' => true, 'sensitive' => false],
        'APP_ENV' => ['required' => true, 'sensitive' => false],
        'APP_KEY' => ['required' => true, 'sensitive' => true],
        'APP_DEBUG' => ['required' => true, 'sensitive' => false],
        'APP_URL' => ['required' => true, 'sensitive' => false],
        'DB_CONNECTION' => ['required' => true, 'sensitive' => false],
        'DB_HOST' => ['required' => false, 'sensitive' => false],
        'DB_DATABASE' => ['required' => false, 'sensitive' => false],
        'MAIL_MAILER' => ['required' => false, 'sensitive' => false],
        'MAIL_HOST' => ['required' => false, 'sensitive' => false],
        'MAIL_FROM_ADDRESS' => ['required' => false, 'sensitive' => false],
        'CACHE_STORE' => ['required' => false, 'sensitive' => false],
        'QUEUE_CONNECTION' => ['required' => false, 'sensitive' => false],
        'SESSION_DRIVER' => ['required' => false, 'sensitive' => false],
    ];

    public function handle(): int
    {
        $this->info('Vérification des variables d\'environnement...');
        $this->newLine();

        $rows = [];
        $errors = 0;
        $warnings = 0;

        foreach ($this->checks as $key => $opts) {
            $value = env($key);
            $status = '✓';

            if ($value === null || $value === '') {
                if ($opts['required']) {
                    $status = '✗ MANQUANTE';
                    $errors++;
                } else {
                    $status = '⚠ Non définie';
                    $warnings++;
                }
            }

            $displayValue = match (true) {
                $value === null, $value === '' => '-',
                $opts['sensitive'] => str_repeat('•', min(strlen((string) $value), 12)),
                default => (string) $value,
            };

            $rows[] = [$key, $displayValue, $status];
        }

        $this->table(['Variable', 'Valeur', 'Statut'], $rows);
        $this->newLine();

        if (config('app.env') === 'production' && config('app.debug')) {
            $this->components->warn('APP_DEBUG est activé en production !');
            $warnings++;
        }

        if (! config('app.key')) {
            $this->components->error('APP_KEY est vide. Lancez : php artisan key:generate');
            $errors++;
        }

        if ($errors > 0) {
            $this->components->error("{$errors} erreur(s) détectée(s).");

            return self::FAILURE;
        }

        if ($warnings > 0) {
            $this->components->warn("OK avec {$warnings} avertissement(s).");

            return self::SUCCESS;
        }

        $this->components->info('Toutes les variables sont correctement définies.');

        return self::SUCCESS;
    }
}
