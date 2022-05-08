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

    /**
     * @param int $id
     * @param string $entityName
     * @return string
     */
    public function encode(int $id, string $entityName): string
    {
        $entityHash = $this->getEntityHash($entityName);

        $idHex = dechex($id);
        $idHex = str_pad($idHex, self::ENTITY_LENGTH, self::ENTITY_BODY_CHAR, STR_PAD_LEFT);

        $idHex = strrev($idHex);
        $base = join(self::ENTITY_SEPARATOR, str_split($idHex, self::BLOCK_LENGTH));

        return strtoupper(join('', [
            $this->getCheckSum($idHex, $entityHash),
            $base,
            $entityHash
        ]));
    }

    /**
     * @param string $entityId
     * @param string $entityName
     * @return int
     * @throws Exception
     */
    public function decode(string $entityId, string $entityName): int
    {
        $entityId = strtolower($entityId);

        $checkSum = substr($entityId, self::BLOCK_STARTING_POINT, self::BLOCK_LENGTH);
        $entityHash = substr($entityId, self::NEGATIVE_BLOCK_LENGTH);

        if ($this->getEntityHash($entityName) !== $entityHash) {
            throw new Exception($entityId);
        }

        $idHex = substr($entityId, self::BLOCK_LENGTH, self::NEGATIVE_BLOCK_LENGTH);
        $idHex = str_replace(self::ENTITY_SEPARATOR, '', $idHex);
        $idHex = strrev($idHex);

        if ($this->getCheckSum($idHex, $entityHash) !== $checkSum) {
            throw new Exception($entityId);
        }

        return hexdec($idHex);
    }

    /**
     * @param string $entityName
     * @return string
     */
    private function getEntityHash(string $entityName): string
    {
        return substr(md5($entityName), self::BLOCK_STARTING_POINT, self::BLOCK_LENGTH);
    }

    /**
     * @param string $entityIdHex
     * @param string $entityHash
     * @return string
     */
    private function getCheckSum(string $entityIdHex, string $entityHash): string
    {
        $hash = md5(join(self::ENTITY_SEPARATOR, [$entityIdHex, $entityHash, self::SECRET]));
        return substr($hash, self::BLOCK_LENGTH, self::BLOCK_LENGTH);
    }
}
