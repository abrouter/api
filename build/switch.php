<?php

define('REQUIRED_FLAGS', ['mode']);
define('REQUIRED_FLAGS_VALIDATION', [
    'mode' => ['dev', 'test'],
]);


/**
 * FUNCTIONS
 */
$criticalFunction = function ($message) {
    echo $message . "\n";
    exit;
};
$successfulExitFunction = function ($message) {
    echo $message . "\n";
    exit;
};

$ensureArgumentsPresent = function (array $current) use ($criticalFunction)
{
    $diff = array_diff(array_keys($current), REQUIRED_FLAGS);
    if (!empty($diff)) {
        $criticalFunction('The following flags should be passed: ' . join($diff));
    }
    
    foreach (REQUIRED_FLAGS_VALIDATION as $key => $values) {
        if (!in_array($current[$key], $values)) {
            $criticalFunction("Argument {$key} is invalid");
        }
    }
    
};


/**
 * FLAGS
 */
$prepareArgsFunction = function (array $args)
{
    $preparedArgs = [];
    unset($args[0]);
    foreach ($args as $arg) {
        list($argK, $argV) = explode('=', $arg);
        $argK = substr($argK, 2);
        $preparedArgs[$argK] = $argV;
    }
    return $preparedArgs;
};


$getArgVFunction = static function ($key, $def = null) use ($prepareArgsFunction, $argv, $ensureArgumentsPresent) {
    static $preparedArgs;
    if (!empty($preparedArgs)) {
        return isset($preparedArgs[$key]) ? $preparedArgs[$key] : $def;
    }
    
    $preparedArgs = $prepareArgsFunction($argv);
    $ensureArgumentsPresent($preparedArgs);
    return isset($preparedArgs[$key]) ? $preparedArgs[$key] : $def;
};

function pattern(string $str) {
    return  "DB_DATABASE=$str\n";
}

$mode = $getArgVFunction('mode');

$dbname = $mode === 'dev' ? 'abr' : 'abr_test';
$dbnameOpposite = $mode !== 'dev' ? 'abr' : 'abr_test';
$envContents = file_get_contents('/app/.env');

if (strtr($envContents, [pattern($dbname) => pattern($dbnameOpposite)]) !== $envContents) {
    $successfulExitFunction('Already switched!');
};

$envContents = strtr($envContents, [
    pattern($dbnameOpposite) => pattern($dbname),
]);

file_put_contents('/app/.env', $envContents);

$successfulExitFunction('Database switched to ' . $dbname);

