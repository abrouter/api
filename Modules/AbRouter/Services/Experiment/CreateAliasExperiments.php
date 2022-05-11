<?php
declare(strict_types=1);

namespace Modules\AbRouter\Services\Experiment;

class CreateAliasExperiments 
{
    public function create(string $name): string
    {
        $name = strtolower($name);
        $alias = preg_replace('/\s+/', '_', $name);

        return mb_substr($alias, 0, 199);
    }
}
