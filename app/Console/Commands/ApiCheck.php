<?php

namespace App\Console\Commands;

use App\Services\ZKBioApiService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ApiCheck extends Command
{
    protected $signature   = 'api:check {complex=13}';
    protected $description = 'Teste l\'API distante et l\'API ZKBio (lecture seule, aucune écriture)';

    public function handle(ZKBioApiService $zk): int
    {
        $ok = true;

        // 1) API distante (liste des personnes)
        $complex = $this->argument('complex');
        $ts  = now()->timestamp;
        $sig = hash_hmac('sha256', $complex . '|' . $ts, (string) env('REMOTE_API_SECRET'));
        $url = rtrim(env('REMOTE_API_URL'), '/') . "/api/complex/{$complex}/persons";

        try {
            $r = Http::timeout(15)->withHeaders([
                'Accept' => 'application/json', 'X-TIMESTAMP' => $ts, 'X-SIGNATURE' => $sig,
            ])->get($url);
            $n = count($r->json('data') ?? []);
            $this->line(($r->successful() ? '✅' : '❌') . " API distante : HTTP {$r->status()}, {$n} personnes");
            $ok = $ok && $r->successful();
        } catch (\Throwable $e) {
            $this->error('❌ API distante : ' . $e->getMessage());
            $ok = false;
        }

        // 2) Authentification ZKBio
        $this->line('ZKBio : ' . config('services.zkbio.url') . ' (compte ' . config('services.zkbio.username') . ')');
        try {
            $zk->authenticate();
            $this->line('✅ ZKBio : authentification OK');
        } catch (\Throwable $e) {
            $this->error('❌ ZKBio : authentification refusée (vérifiez ZKBIO_USERNAME / ZKBIO_PASSWORD dans .env)');
            return self::FAILURE;
        }

        // 3) Lecture ZKBio
        foreach (['/personnel/api/employees/', '/personnel/api/departments/', '/personnel/api/areas/', '/personnel/api/companies/'] as $ep) {
            $r = $zk->get($ep, ['page_size' => 1]);
            $this->line(($r->successful() ? '✅' : '❌') . " GET {$ep} : HTTP {$r->status()}"
                . ($r->successful() ? ', total=' . ($r->json('count') ?? '?') : ''));
            $ok = $ok && $r->successful();
        }

        return $ok ? self::SUCCESS : self::FAILURE;
    }
}