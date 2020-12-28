<?php
declare(strict_types=1);

namespace Modules\Auth\Integration;

use Lcobucci\JWT\Parser;
use Modules\Auth\Entities\AccessToken\JwtToken;

class JwtParser extends Parser
{
    public function parse($jwt): JwtToken
    {
        $data = $this->splitJwt($jwt);
        $header = $this->parseHeader($data[0]);
        $claims = $this->parseClaims($data[1]);
        $signature = $this->parseSignature($header, $data[2]);
        foreach ($claims as $name => $value) {
            if (isset($header[$name])) {
                $header[$name] = $value;
            }
        }

        if ($signature === null) {
            unset($data[2]);
        }

        return new JwtToken($header, $claims, $signature, $data);
    }
}
