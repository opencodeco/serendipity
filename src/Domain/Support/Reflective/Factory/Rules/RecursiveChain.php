<?php

declare(strict_types=1);

namespace Serendipity\Domain\Support\Reflective\Factory\Rules;

use ReflectionParameter;
use Serendipity\Domain\Support\Reflective\Factory\Chain;
use Serendipity\Domain\Support\Reflective\Factory\Ruleset;

class RecursiveChain extends Chain
{
    public function resolve(ReflectionParameter $parameter, Ruleset $rules): Ruleset
    {
        return parent::resolve($parameter, $rules);
    }
}
