<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules;

use Saloon\Repositories\ArrayStore;
use Saloon\Traits\Makeable;

class Model extends ArrayStore
{
    use Makeable;

    protected array $defaults = [];

    public function __construct(array $data = [])
    {
        parent::__construct($data);

        if (!empty($this->defaults)) {
            $this->data = array_merge_recursive($data, $this->defaults);
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