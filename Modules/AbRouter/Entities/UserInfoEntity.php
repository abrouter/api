<?php
declare(strict_types=1);

namespace Modules\AbRouter\Entities;

use Carbon\Carbon;

class UserInfoEntity
{
    /**
     * @var array
     */
    private $experimentsIds;

    /**
     * @var Carbon|null
     */
    private $created_at;

    /**
     * @var string|null
     */
    private $browser;

    /**
     * @var string|null
     */
    private $platform;

    /**
     * @var string|null
     */
    private $countryName;

    public function __construct(
        array $experimentsIds,
        ?Carbon $created_at,
        ?string $browser,
        ?string $platform,
        ?string $countryName
    ) {
        $this->experimentsIds = $experimentsIds;
        $this->created_at = $created_at;
        $this->browser = $browser;
        $this->platform = $platform;
        $this->countryName = $countryName;
    }

    /**
     * @return array
     */
    public function getExperimentsIds(): array
    {
        return $this->experimentsIds;
    }

    /**
     * @return Carbon|null
     */
    public function getCreatedAt(): ?Carbon
    {
        return $this->created_at;
    }

    /**
     * @return string|null
     */
    public function getBrowser(): ?string
    {
        return $this->browser;
    }

    /**
     * @return string|null
     */
    public function getPlatform(): ?string
    {
        return $this->platform;
    }

    /**
     * @return string|null
     */
    public function getCountryName(): ?string
    {
        return $this->countryName;
    }
}

