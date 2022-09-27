<?php

declare(strict_types=1);

namespace Knp\JsonSchema;

use Knp\JsonSchema\Validator\Errors;

interface Validator
{
    /**
     * @template T
     *
     * @param T                                         $data
     * @param class-string<JsonSchema<T>>|JsonSchema<T> $schema
     */
    public function validate($data, $schema): ?Errors;
}
