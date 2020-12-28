<?php
declare(strict_types=1);

namespace Modules\ProxiedMail\Entities\ReceivedEmail;

class AttachmentEntity
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var int
     */
    private $size;
    /**
     * @var string
     */
    private $content;
    /**
     * @var string
     */
    private $mime;

    public function __construct(string $name, int $size, string $content, string $mime)
    {
        $this->name = $name;
        $this->size = $size;
        $this->content = $content;
        $this->mime = $mime;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return string
     */
    public function getMime(): string
    {
        return $this->mime;
    }
}
