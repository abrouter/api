<?php

namespace Modules\AbRouter\Services\Experiment;

class CreateAliasExperiments 
{
    public function create(string $name) {
        $name = strtolower($name);
        $alias = preg_replace('/\s+/', '_', $name);
        $alias = mb_substr($alias, 0, 199);

        return $alias;
    }
}
