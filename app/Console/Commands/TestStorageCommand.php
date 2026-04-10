<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TestStorageCommand extends Command
{
    protected $signature = 'test:storage {disk? : Nom du disque à tester}';

    protected $description = 'Teste les opérations de lecture/écriture sur un disque de stockage';

    public function handle(): int
    {
        $disk = $this->argument('disk') ?? config('filesystems.default');

        $this->info("Test du disque '{$disk}'...");
        $this->newLine();

        $config = config("filesystems.disks.{$disk}");

        if (! $config) {
            $this->components->error("Disque '{$disk}' introuvable dans la configuration.");

            return self::FAILURE;
        }

        $this->table(
            ['Paramètre', 'Valeur'],
            array_filter([
                ['Driver', $config['driver'] ?? 'inconnu'],
                ['Root', $config['root'] ?? '-'],
                ['URL', $config['url'] ?? null],
                ['Bucket', $config['bucket'] ?? null],
            ], fn ($row) => $row[1] !== null)
        );

        $this->newLine();

        $filename = '_diagnostic_test_'.Str::random(8).'.txt';
        $content = 'Test de stockage - '.now()->toDateTimeString();

        try {
            $startTime = microtime(true);
            Storage::disk($disk)->put($filename, $content);
            $writeTime = round((microtime(true) - $startTime) * 1000, 2);

            $startTime = microtime(true);
            $read = Storage::disk($disk)->get($filename);
            $readTime = round((microtime(true) - $startTime) * 1000, 2);

            if ($read !== $content) {
                $this->components->error('Le contenu lu ne correspond pas au contenu écrit.');
                Storage::disk($disk)->delete($filename);

                return self::FAILURE;
            }

            $startTime = microtime(true);
            Storage::disk($disk)->delete($filename);
            $deleteTime = round((microtime(true) - $startTime) * 1000, 2);

            $this->components->info('Disque opérationnel !');
            $this->table(
                ['Opération', 'Temps'],
                [
                    ['Écriture', "{$writeTime} ms"],
                    ['Lecture', "{$readTime} ms"],
                    ['Suppression', "{$deleteTime} ms"],
                ]
            );

            return self::SUCCESS;
        } catch (\Throwable $e) {
            Storage::disk($disk)->delete($filename);
            $this->components->error("Échec du stockage : {$e->getMessage()}");

            return self::FAILURE;
        }
    }
}
