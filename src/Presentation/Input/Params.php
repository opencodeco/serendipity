<?php

declare(strict_types=1);

namespace Serendipity\Presentation\Input;

class Params extends Resolver
{
    public function resolve(array $data): array
    {
        $keys = array_keys($this->input->rules());
        foreach ($keys as $key) {
            if (array_key_exists($key, $data)) {
                continue;
            }
            $param = $this->input->route($key);
            if ($param === null) {
                continue;
            }
            $data[$key] = $param;
        }
        return parent::resolve($data);
    }
}
