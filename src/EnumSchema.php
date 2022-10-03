<?php

declare(strict_types=1);

namespace Knp\JsonSchema;

/**
 * @template E
 * @extends JsonSchema<E>
 */
abstract class EnumSchema extends JsonSchema
{
    public function getExamples(): iterable
    {
        return $this->getEnum();
    }

    public function getSchema(): array
    {
        return [
            'enum' => [...$this->getEnum()],
        ];
    }

    /**
     * @return iterable<int, E>
     */
    abstract protected function getEnum(): iterable;
}
