<?php

declare(strict_types=1);

namespace KnpLabs\JsonSchema;

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

    protected function getSchema(): array
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
