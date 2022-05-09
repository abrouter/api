<?php
declare(strict_types=1);

namespace Modules\Core\Http\Transformers;

use Illuminate\Http\Request;

abstract class BaseTransformer
{
    /**
     * @param Request $request
     */
    abstract public function transform($request);
}
