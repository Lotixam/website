<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class TestCacheCommand extends Command
{
    protected $signature = 'test:cache {store? : Nom du store de cache à tester}';

    protected $description = 'Teste le fonctionnement du cache (read/write/delete)';

    public function handle(): int
    {
        $store = $this->argument('store') ?? config('cache.default');

        $this->info("Test du cache '{$store}'...");
        $this->newLine();

        $driver = config("cache.stores.{$store}.driver", 'inconnu');

        $this->table(
            ['Paramètre', 'Valeur'],
            [
                ['Store', $store],
                ['Driver', $driver],
            ]
        );

        $this->newLine();

        $key = '_test_cache_diagnostic_'.Str::random(8);
        $value = 'test_'.now()->timestamp;

        try {
            $startTime = microtime(true);

            Cache::store($store)->put($key, $value, 30);
            $writeTime = round((microtime(true) - $startTime) * 1000, 2);

            $startTime = microtime(true);
            $retrieved = Cache::store($store)->get($key);
            $readTime = round((microtime(true) - $startTime) * 1000, 2);

            if ($retrieved !== $value) {
                $this->components->error("Valeur lue incohérente (attendu: {$value}, reçu: {$retrieved})");

                return self::FAILURE;
            }

            $startTime = microtime(true);
            Cache::store($store)->forget($key);
            $deleteTime = round((microtime(true) - $startTime) * 1000, 2);

            $confirm = Cache::store($store)->get($key);

            if ($confirm !== null) {
                $this->components->warn("La suppression n'a pas fonctionné correctement.");
            }

            $this->components->info('Cache opérationnel !');
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
            Cache::store($store)->forget($key);
            $this->components->error("Échec du cache : {$e->getMessage()}");

            return self::FAILURE;
        }
    }
}
