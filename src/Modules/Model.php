<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules;

use Saloon\Repositories\ArrayStore;
use Saloon\Traits\Makeable;

class Model extends ArrayStore
{
    use Makeable;

    /** @var array<string, mixed> */
    protected array $defaults = [];

    /**
     * @param array<string, mixed> $data
     */
    public function __construct(array $data = [])
    {
        parent::__construct($data);

        if (!empty($this->defaults)) {
            // Explicit data wins; defaults only fill missing keys (no recursive merge
            // that would turn an overridden scalar into an array).
            $this->data = $data + $this->defaults;
        }
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $array = $this->all();
        $keys = explode('.', $key);

        foreach ($keys as $k) {
            if (is_array($array) && array_key_exists($k, $array)) {
                $array = $array[$k];
            } else {
                return $default;
            }
        }

        return $array;
    }
}
