<?php

declare(strict_types=1);

namespace Knp\JsonSchema;

/**
 * @template T of array<string, mixed>
 * @extends JsonSchema<T>
 */
abstract class ObjectSchema implements JsonSchemaInterface
{
    /**
     * @var array<string, JsonSchema<mixed>>
     */
    private array $properties = [];

    /**
     * @var array<string>
     */
    private array $required = [];

    public function getExamples(): iterable
    {
        /**
         * @var T
         */
        $object = [];

        foreach ($this->properties as $name => $property) {
            foreach ($property->getExamples() as $example) {
                $object[$name] = $example;

                continue 2;
            }
        }

        yield $object;
    }

    protected function hasAdditionalProperties(): bool
    {
        return false;
    }

    /**
     * @template S
     *
     * @param JsonSchema<S> $schema
     */
    protected function addProperty(string $name, JsonSchema $schema, bool $required = true): void
    {
        $this->properties[$name] = $schema;

        if ($required) {
            $this->required[] = $name;
        } else {
            $this->required = array_diff($this->required, [$name]);
        }
    }

    public function getSchema(): array
    {
        return [
            'type' => 'object',
            'additionalProperties' => $this->hasAdditionalProperties(),
            'properties' => $this->properties,
            'required' => $this->required,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize(): array
    {
        $schema = $this->getSchema();

        /**
         * @var array<string, mixed>&array{title: string, description: string, examples: array<T>}
         */
        return array_merge(
            $schema,
            [
                'title' => $this->getTitle(),
                'description' => $this->getDescription(),
                'examples' => [...$this->getExamples()],
            ],
        );
    }
}
