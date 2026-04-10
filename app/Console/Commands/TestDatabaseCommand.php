<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TestDatabaseCommand extends Command
{
    protected $signature = 'test:db {connection? : Nom de la connexion à tester}';

    protected $description = 'Teste la connexion à la base de données';

    public function handle(): int
    {
        $connection = $this->argument('connection') ?? config('database.default');

        $this->info("Test de la connexion '{$connection}'...");
        $this->newLine();

        $config = config("database.connections.{$connection}");

        if (! $config) {
            $this->components->error("Connexion '{$connection}' introuvable dans la configuration.");

            return self::FAILURE;
        }

        $driver = $config['driver'] ?? 'inconnu';

        $this->table(
            ['Paramètre', 'Valeur'],
            array_filter([
                ['Driver', $driver],
                ['Host', $config['host'] ?? '-'],
                ['Port', $config['port'] ?? '-'],
                ['Database', $config['database'] ?? '-'],
                ['Username', $config['username'] ?? '-'],
            ])
        );

        $this->newLine();

        try {
            $startTime = microtime(true);
            DB::connection($connection)->getPdo();
            $elapsed = round((microtime(true) - $startTime) * 1000, 2);

            $this->components->info("Connexion réussie ! ({$elapsed} ms)");

            $version = match ($driver) {
                'mysql', 'mariadb' => DB::connection($connection)->selectOne('SELECT VERSION() as v')?->v,
                'pgsql' => DB::connection($connection)->selectOne('SHOW server_version')?->server_version,
                'sqlite' => DB::connection($connection)->selectOne('SELECT sqlite_version() as v')?->v,
                default => null,
            };

            if ($version) {
                $this->line("  Version serveur : {$version}");
            }

            $tables = $this->getTableCount($connection, $driver);
            if ($tables !== null) {
                $this->line("  Nombre de tables : {$tables}");
            }

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->components->error("Échec de la connexion : {$e->getMessage()}");

            return self::FAILURE;
        }
    }

    private function getTableCount(string $connection, string $driver): ?int
    {
        try {
            $result = match ($driver) {
                'mysql', 'mariadb' => DB::connection($connection)->selectOne('SELECT COUNT(*) as cnt FROM information_schema.tables WHERE table_schema = DATABASE()'),
                'pgsql' => DB::connection($connection)->selectOne("SELECT COUNT(*) as cnt FROM information_schema.tables WHERE table_schema = 'public'"),
                'sqlite' => DB::connection($connection)->selectOne("SELECT COUNT(*) as cnt FROM sqlite_master WHERE type = 'table'"),
                default => null,
            };

            return $result?->cnt;
        } catch (\Throwable) {
            return null;
        }
    }
}
