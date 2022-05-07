<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

include '/app/vendor/autoload.php';

use SensioLabs\Consul\Exception\ClientException;
use SensioLabs\Consul\Exception\ServerException;
use SensioLabs\Consul\ServiceFactory;
use SensioLabs\Consul\Services\KV;

if (!function_exists('yaml_parse')) {
    function yaml_parse(string $content) {
        $lines = explode("\n", $content);
        $keys = [];
        foreach ($lines as $line) {
            $explode = explode(':', $line);
            $key = $explode[0];
            unset($explode[0]);
            $value = join(':', $explode);
            if (!empty($key) && !empty($value)) {
                $keys[$key] = trim($value);
            }
        }

        return $keys;
    }
}

function addConsulKeys(string $content) {
    $keys = yaml_parse($content);
    $serviceFactory = new ServiceFactory(['base_uri' => $_SERVER['CONSUL_HTTP_ADDR']]);

    /** @var KV $kv */
    $kv = $serviceFactory->get('kv');

    foreach ($keys as $key => $value) {
        try {
            $kv->get($key);
        } catch (ServerException $e) {
            fprintf(STDERR, $e->getMessage() . PHP_EOL);
            exit(1);
        } catch (ClientException $e) {
            if ($e->getCode() == 404) {
                $kv->put($key, $value);
            } else {
                fprintf(STDERR, $e->getMessage() . PHP_EOL);
                exit(1);
            }
        } catch (\Throwable $e) {
            fprintf(STDERR, $e->getMessage() . PHP_EOL);
            exit(1);
        }
    }
}

$content = file_get_contents('/app/docker/config/consul/consul-keys.yml');
addConsulKeys($content);
