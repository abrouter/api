<?php
declare(strict_types =1);

namespace Modules\AbRouter\Services\Events\DTO;

class EventDTO
{
    /**
     * @var int
     */
    private $ownerId;

    /**
     * @var string
     */
    private $temporaryUserId;
    
    /**
     * @var string
     */
    private $userId;
    
    /**
     * @var string
     */
    private $event;

    /**
     * @var string
     */
    private $value;
    
    /**
     * @var string
     */
    private $referrer;
    
    /**
     * @var string
     */
    private $tag;
    
    /**
     * @var array
     */
    private $meta;
    
    /**
     * @var string
     */
    private $ip;

    /**
     * @var string|null
     */
    private $created_at;
    
    /**
     * @var string
     */
    private $countryCode;
    
    /**
     * EventDTO constructor.
     *
     * @param int         $ownerId
     * @param string|null $temporaryUserId
     * @param string      $userId
     * @param string      $event
     * @param string      $value
     * @param string      $tag
     * @param string      $referrer
     * @param array       $meta
     * @param string      $ip
     * @param string|null $created_at
     * @param string      $countryCode
     */
    public function __construct(
        int $ownerId,
        ?string $temporaryUserId,
        string $userId,
        string $event,
        string $value,
        string $tag,
        string $referrer,
        array $meta,
        string $ip,
        ?string $created_at,
        string $countryCode
    ) {
        $this->ownerId = $ownerId;
        $this->temporaryUserId = $temporaryUserId;
        $this->userId = $userId;
        $this->event = $event;
        $this->value = $value;
        $this->tag = $tag;
        $this->referrer = $referrer;
        $this->meta = $meta;
        $this->ip = $ip;
        $this->created_at = $created_at;
        $this->countryCode = $countryCode;
    }
    
    /**
     * @return int
     */
    public function getOwnerId(): int
    {
        return $this->ownerId;
    }

    /**
     * @return string
     */
    public function getTemporaryUserId(): ?string
    {
        return $this->temporaryUserId;
    }
    
    /**
     * @return string
     */
    public function getUserId(): string
    {
        return $this->userId;
    }
    
    /**
     * @return string
     */
    public function getEvent(): string
    {
        return $this->event;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getReferrer(): string
    {
        return $this->referrer;
    }
    
    /**
     * @return string
     */
    public function getTag(): string
    {
        return $this->tag;
    }
    
    /**
     * @return array
     */
    public function getMeta(): array
    {
        return $this->meta;
    }
    
    /**
     * @return string
     */
    public function getIp(): string
    {
        return $this->ip;
    }

    /**
     * @return string|null
     */
    public function getCreatedAt(): ?string
    {
        return $this->created_at;
    }

    /**
     * @return string
     */
    public function getCountryCode(): string
    {
        return $this->countryCode;
    }
}
