<?php
declare(strict_types=1);

namespace Modules\Core\EntityId;

use Exception;

class Encoder
{
    const SALT = 'pm-app';

    /**
     * @param int $id
     * @param string $entityName
     * @return string
     */
    public function encode(int $id, string $entityName): string
    {
        $idHex = dechex($id);
        $idHex = str_pad($idHex, 16, '0', STR_PAD_LEFT);

        $entityHash = $this->getEntityHash($entityName);
        $checkSum = $this->getCheckSum($idHex, $entityHash);

        $idHex = strrev($idHex);
        $base = implode('-', str_split($idHex, 4));

        return strtoupper($checkSum . $base . $entityHash);
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

        $checkSum = substr($entityId, 0, 4);
        $entityHash = substr($entityId, -4);

        if ($this->getEntityHash($entityName) !== $entityHash) {
            throw new Exception($entityId);
        }

        $idHex = substr($entityId, 4, -4);
        $idHex = str_replace('-', '', $idHex);
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
        $entityHash = md5($entityName);
        return substr($entityHash, 0, 4);
    }

    /**
     * @param string $entityIdHex
     * @param string $entityHash
     * @return string
     */
    private function getCheckSum(string $entityIdHex, string $entityHash): string
    {
        $hash = md5($entityIdHex . '-' . $entityHash . '-' . self::SALT);
        return substr($hash, 4, 4);
    }
}
