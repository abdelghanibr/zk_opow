<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ZKBioApiService
{
    protected string $baseUrl;
    protected ?string $token = null;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.zkbio.url'), '/');
    }

    /* =========================
       1️⃣ Récupérer le token
       ========================= */
    public function authenticate(): string
    {
        if ($this->token) {
            return $this->token;
        }

        $response = Http::post(
            $this->baseUrl . '/api-token-auth/',
            [
                'username' => config('services.zkbio.username'),
                'password' => config('services.zkbio.password'),
            ]
        );

        if (! $response->successful()) {
            throw new \Exception('❌ ZKBio Auth failed');
        }

        $this->token = $response->json('token');

        return $this->token;
    }

    /* =========================
       2️⃣ Appels API sécurisés
       ========================= */
    protected function client()
    {
        return Http::withHeaders([
            'Authorization' => 'Token ' . $this->authenticate(),
            'Content-Type'  => 'application/json',
        ])->timeout(30);
    }

    public function post(string $endpoint, array $data = [])
    {
        return $this->client()->post($this->baseUrl . $endpoint, $data);
    }

    public function get(string $endpoint, array $query = [])
    {
        return $this->client()->get($this->baseUrl . $endpoint, $query);
    }

    public function patch(string $endpoint, array $data = [])
    {
        return $this->client()->patch($this->baseUrl . $endpoint, $data);
    }

    public function delete(string $endpoint)
    {
        return $this->client()->delete($this->baseUrl . $endpoint);
    }

    /* =========================
       3️⃣ Employés (licence respectée :
          tout passe par ZKBio, jamais par SQL)
       ========================= */
    private const EMPLOYEES = '/personnel/api/employees/';

    /** Retourne la fiche ZKBio (tableau) ou null si introuvable. */
    public function findEmployeeByCode(string $empCode): ?array
    {
        $response = $this->get(self::EMPLOYEES, ['emp_code' => $empCode]);

        if (! $response->successful()) {
            throw new \Exception('ZKBio: lecture employé impossible (' . $response->status() . ')');
        }

        $body = $response->json();

        // Le filtre emp_code peut être "contains" : on exige l'égalité exacte.
        foreach (($body['data'] ?? $body['results'] ?? []) as $row) {
            if ((string) ($row['emp_code'] ?? '') === $empCode) {
                return $row;
            }
        }

        return null;
    }

    public function createEmployee(array $payload): array
    {
        $response = $this->post(self::EMPLOYEES, $payload);

        return $this->assertEmployeeResponse($response, 'création');
    }

    public function updateEmployee(int $id, array $payload): array
    {
        $response = $this->patch(self::EMPLOYEES . $id . '/', $payload);

        return $this->assertEmployeeResponse($response, 'mise à jour');
    }

    public function deleteEmployee(int $id): void
    {
        $response = $this->delete(self::EMPLOYEES . $id . '/');

        if (! $response->successful()) {
            throw new \Exception('ZKBio a refusé la suppression : ' . $response->body());
        }
    }

    /**
     * Crée ou met à jour selon emp_code.
     * @return array{0: string, 1: array} ['inserted'|'updated', fiche]
     */
    public function upsertEmployee(string $empCode, array $payload): array
    {
        $existing = $this->findEmployeeByCode($empCode);

        if ($existing) {
            unset($payload['emp_code']);
            return ['updated', $this->updateEmployee((int) $existing['id'], $payload)];
        }

        return ['inserted', $this->createEmployee(['emp_code' => $empCode] + $payload)];
    }

    /**
     * IDs réels (clés primaires ZKBio) du département, de la zone et de la société.
     * Priorité : ZKBIO_DEPARTMENT_ID / ZKBIO_AREA_ID / ZKBIO_COMPANY_ID dans .env,
     * sinon le premier élément existant dans ZKBio.
     */
    public function defaults(): array
    {
        $pick = function (string $endpoint, ?string $configured, string $label, bool $required = true): ?int {
            if ($configured !== null && $configured !== '') {
                return (int) $configured;
            }
            $r = $this->get($endpoint, ['page_size' => 1]);
            $first = ($r->json('data') ?? $r->json('results') ?? [])[0]['id'] ?? null;
            if (! $first && ! $required) {
                return null; // ex. société : facultative selon la configuration ZKBio
            }
            if (! $first) {
                throw new \Exception("ZKBio : aucun(e) {$label} trouvé(e). Créez-en un dans ZKBio ou renseignez son ID dans .env");
            }
            return (int) $first;
        };

        return \Illuminate\Support\Facades\Cache::remember('zkbio.defaults', 600, fn () => [
            'department' => $pick('/personnel/api/departments/', config('services.zkbio.department'), 'département'),
            'area'       => $pick('/personnel/api/areas/',       config('services.zkbio.area'),       'zone'),
            'company'    => $pick('/personnel/api/companies/',   config('services.zkbio.company'),    'société', false),
        ]);
    }

    private function assertEmployeeResponse($response, string $action): array
    {
        $body = $response->json() ?? [];

        // ZKBio renvoie parfois 200 avec un message d'erreur : on vérifie le corps.
        if (! $response->successful() || ! isset($body['id'])) {
            throw new \Exception("ZKBio a refusé la {$action} : " . $response->body());
        }

        return $body;
    }
}
