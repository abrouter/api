<?php
declare(strict_types=1);

namespace Modules\Core\EntityId;

use Exception;

class EntityEncoder
{
    private const SECRET = 'abr-app';
    private const ENTITY_LENGTH = 16;
    private const ENTITY_BODY_CHAR = '0';
    private const ENTITY_SEPARATOR = '-';
    private const BLOCK_LENGTH = 4;
    private const NEGATIVE_BLOCK_LENGTH = -4;
    private const BLOCK_STARTING_POINT = 0;
    private const EMPTY = '';

    public function encode(int $id, string $name): string
    {
        $hex = str_pad(dechex($id), self::ENTITY_LENGTH, self::ENTITY_BODY_CHAR, STR_PAD_LEFT);
        $entityHash = $this->getHashOfEntity($name);
        $entityId = join(self::EMPTY, [
            $this->getCheckSum($hex, $entityHash),
            join(self::ENTITY_SEPARATOR, str_split(strrev($hex), self::BLOCK_LENGTH)),
            $entityHash
        ]);

        return strtoupper($entityId);
    }

    public function decode(string $entity, string $name): int
    {
        $entity = strtolower($entity);

        $checkSum = substr($entity, self::BLOCK_STARTING_POINT, self::BLOCK_LENGTH);
        $entityHash = substr($entity, self::NEGATIVE_BLOCK_LENGTH);
        $hex = strrev(strtr(substr($entity, self::BLOCK_LENGTH, self::NEGATIVE_BLOCK_LENGTH), [
            self::ENTITY_SEPARATOR => self::EMPTY,
        ]));

        $checks = [
            $this->getHashOfEntity($name) === $entityHash,
            $this->getCheckSum($hex, $entityHash) === $checkSum,
        ];

        if (in_array(false, $checks, true)) {
            throw new Exception($entity);
        }

        return hexdec($hex);
    }

    private function getHashOfEntity(string $name): string
    {
        return substr(sha1($name), self::BLOCK_STARTING_POINT, self::BLOCK_LENGTH);
    }

    private function getCheckSum(string $hex, string $entityHash): string
    {
        return substr(
            sha1(join(self::ENTITY_SEPARATOR, [
                $entityHash,
                self::SECRET,
                $hex,
            ])),
            self::BLOCK_LENGTH,
            self::BLOCK_LENGTH
        );
    }
}
