<?php
declare(strict_types=1);

namespace Modules\AbRouter\Services\IpInfo;

class IpInfoRequesterService
{
    public function getInfoByIp(string $ip): array
    {
        $requestUri = 'http://api.ipstack.com/'. $ip .'?access_key='. $this->getApiKey() .'&format=1';
        $response = json_decode(file_get_contents($requestUri), true);
        return $response;
    }

    private function getApiKey(): string
    {
        return env('IPSTACK_API_KEY');
    }
}
