<?php

declare(strict_types=1);

namespace Knp\JsonSchema;

/**
 * @template I
 *
 * @phpstan-type CollectionSchemaData array<I>
 *
 * @extends JsonSchema<CollectionSchemaData>
 */
abstract class CollectionSchema extends JsonSchema
{
    /**
     * @param JsonSchema<I> $itemSchema
     */
    public function __construct(private JsonSchema $itemSchema)
    {
    }

    public function getExamples(): iterable
    {
        yield [...$this->itemSchema->getExamples()];
    }

    public function getTitle(): string
    {
        return sprintf('Collection<%s>', $this->itemSchema->getTitle());
    }

    protected function getUniqueItems(): ?bool
    {
        return null;
    }

    protected function getMinLength(): ?int
    {
        return null;
    }

    protected function getMaxLength(): ?int
    {
        return null;
    }

    protected function getRange(): ?int
    {
        return null;
    }

    public function getSchema(): array
    {
        $schema = [
            'type' => 'array',
            'items' => $this->itemSchema->jsonSerialize(),
        ];

        if (null !== $uniqueItems = $this->getUniqueItems()) {
            $schema['uniqueItems'] = $uniqueItems;
        }

        if (null !== $minLength = $this->getMinLength()) {
            $schema['minLength'] = $minLength;
        }

        if (null !== $maxLength = $this->getMaxLength()) {
            $schema['maxLength'] = $maxLength;
        }

        return $schema;
    }
}
