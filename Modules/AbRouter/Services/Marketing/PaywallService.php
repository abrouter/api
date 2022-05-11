<?php
declare(strict_types=1);

namespace Modules\AbRouter\Services\Marketing;

use GuzzleHttp\Client as Guzzle;

class PaywallService
{
    /**
     * @var Guzzle
     */
    private $guzzle;

    public function __construct(Guzzle $guzzle)
    {
        $this->guzzle = $guzzle;
    }

    public function isExperimentRunAllowed(int $userId): bool
    {
        if (env('IS_PAYWALL_ENABLED') !== 'enabled') {
            return true;
        }

        try {
            $json = $this->guzzle->request(
                'GET',
                $this->buildUrl('/api/v1/paywallCheck?userId=' . $userId)
            )->getBody()->getContents();
            $json = json_decode($json, true);
            return $json['runningExperiments'] === 'allowed';
        } catch (\Throwable $e) {
        }

        return true;
    }

    private function buildUrl(string $url): string
    {
        return env('MARKETING_API_URL') . $url;
    }
}
