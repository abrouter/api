<?php
declare(strict_types=1);

namespace Modules\Core\Http\Requests;

use Illuminate\Foundation\Http\FormRequest as BaseFormRequest;

abstract class FormRequest extends BaseFormRequest
{
    protected const PREFIX_ATTRIBUTE = 'data.attributes';

    protected const PREFIX_META = 'meta';

    /**
     * @var array List of attributes that allowed to be assigned to model
     */
    protected $allowedAttributes = [];

    /**
     * Returns values that can be assigned to model.
     *
     * @return array
     */
    public function getModelAttributes(): array
    {
        $values = [];

        foreach ($this->getAllowedAttributes() as $attribute) {
            if (!$this->hasAttribute($attribute)) {
                continue;
            }

            $values[$attribute] = $this->getAttribute($attribute);
        }

        return $values;
    }

    /**
     * @param string $name
     * @param null $default
     *
     * @return mixed
     */
    public function getAttribute(string $name, $default = null)
    {
        return $this->input(self::PREFIX_ATTRIBUTE . '.' . $name, $default);
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasAttribute(string $name): bool
    {
        return $this->has(self::PREFIX_ATTRIBUTE . '.' . $name);
    }

    /**
     * @param string $name
     * @param null $default
     *
     * @return mixed
     */
    public function getMeta(string $name, $default = null)
    {
        return $this->input(self::PREFIX_META . '.' . $name, $default);
    }

    /**
     * Get attributes that are allowed to be assigned to model.
     *
     * @return array
     */
    protected function getAllowedAttributes(): array
    {
        return [];
    }

    /**
     * Get attributes that should be encoded though lookup table.
     *
     * @return array
     */
    protected function getLookupAttributes(): array
    {
        return [];
    }
}
