<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestMailCommand extends Command
{
    protected $signature = 'test:mail {to? : Adresse email de destination}';

    protected $description = 'Envoie un email de test pour vérifier la configuration mail';

    public function handle(): int
    {
        $to = $this->argument('to');

        if (! $to) {
            $to = $this->ask('Adresse email de destination ?');
        }

        if (! filter_var($to, FILTER_VALIDATE_EMAIL)) {
            $this->error("Adresse invalide : {$to}");

            return self::FAILURE;
        }

        $mailer = config('mail.default');
        $from = config('mail.from.address');

        $this->info('Configuration actuelle :');
        $this->table(
            ['Paramètre', 'Valeur'],
            [
                ['Mailer', $mailer],
                ['From', $from],
                ['Host', config("mail.mailers.{$mailer}.host", '-')],
                ['Port', config("mail.mailers.{$mailer}.port", '-')],
                ['Encryption', config("mail.mailers.{$mailer}.encryption", '-')],
            ]
        );

        $this->newLine();
        $this->info("Envoi d'un email de test à {$to}...");

        try {
            Mail::raw('Ceci est un email de test envoyé depuis la commande artisan test:mail.', function ($message) use ($to) {
                $message->to($to)
                    ->subject('[Test] Email de diagnostic - '.config('app.name'));
            });

            $this->newLine();
            $this->components->info('Email envoyé avec succès !');

            if ($mailer === 'log') {
                $this->warn("Le mailer est configuré sur 'log'. L'email a été écrit dans le fichier de log.");
                $this->line('→ storage/logs/laravel.log');
            }

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->newLine();
            $this->components->error("Échec de l'envoi : {$e->getMessage()}");

            return self::FAILURE;
        }
    }
}
