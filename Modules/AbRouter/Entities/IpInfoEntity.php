<?php
declare(strict_types=1);

namespace Modules\AbRouter\Entities;

use Modules\Core\EntityId\ResourceIdInterface;

class IpInfoEntity
{
    /**
     * @var string
     */
    private $countryCode;

    /**
     * @var string
     */
    private $countryName;

    /**
     * @var string
     */
    private $city;

    /**
     * @var string
     */
    private $ip;

    /**
     * @var string
     */
    private $ipHash;

    public function __construct(
        string $ip,
        string $ipHash,
        string $countryCode,
        string $countryName,
        string $city
    ) {
        $this->countryCode = $countryCode;
        $this->countryName = $countryName;
        $this->city = $city;
        $this->ip = $ip;
        $this->ipHash = $ipHash;
    }

    /**
     * @return string
     */
    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    /**
     * @return string
     */
    public function getCountryName(): string
    {
        return $this->countryName;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    public function getEntityId(): string
    {
        return $this->ipHash;
    }

    public static function getType(): string
    {
        return 'ip-info';
    }

    /**
     * @return string
     */
    public function getIp(): string
    {
        return $this->ip;
    }
}
