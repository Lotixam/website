<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Queue;

class TestQueueCommand extends Command
{
    protected $signature = 'test:queue {connection? : Nom de la connexion queue à tester}';

    protected $description = 'Teste la connexion au système de queues';

    public function handle(): int
    {
        $connection = $this->argument('connection') ?? config('queue.default');

        $this->info("Test de la queue '{$connection}'...");
        $this->newLine();

        $config = config("queue.connections.{$connection}");

        if (! $config) {
            $this->components->error("Connexion queue '{$connection}' introuvable.");

            return self::FAILURE;
        }

        $driver = $config['driver'] ?? 'inconnu';

        $this->table(
            ['Paramètre', 'Valeur'],
            array_filter([
                ['Driver', $driver],
                ['Queue', $config['queue'] ?? '-'],
                ['Host', $config['host'] ?? null],
            ], fn ($row) => $row[1] !== null)
        );

        $this->newLine();

        try {
            $startTime = microtime(true);
            $size = Queue::connection($connection)->size();
            $elapsed = round((microtime(true) - $startTime) * 1000, 2);

            $this->components->info("Queue accessible ! ({$elapsed} ms)");
            $this->line("  Jobs en attente : {$size}");

            if ($driver === 'sync') {
                $this->warn("  Le driver 'sync' exécute les jobs immédiatement (pas de worker nécessaire).");
            }

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->components->error("Échec de la queue : {$e->getMessage()}");

            return self::FAILURE;
        }
    }
}
