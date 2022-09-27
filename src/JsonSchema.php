<?php

declare(strict_types=1);

namespace KnpLabs\JsonSchema;

use JsonSerializable;

/**
 * @template T of mixed
 */
abstract class JsonSchema implements JsonSerializable
{
    /**
     * @param JsonSchema<E> $schema
     * @template E of mixed
     *
     * @return JsonSchema<null|E>
     */
    public static function nullable(self $schema): self
    {
        return self::create(
            '',
            '',
            [...$schema->getExamples(), null],
            ['oneOf' => [self::null(), $schema->jsonSerialize()]],
        );
    }

    /**
     * @param iterable<int, E>     $examples
     * @param array<string, mixed> $schema
     * @template E of mixed
     *
     * @return JsonSchema<E>
     */
    public static function create(
        string $title,
        string $description,
        iterable $examples,
        $schema
    ): self {
        return new class($title, $description, $examples, $schema) extends JsonSchema {
            /**
             * @var iterable<int, E>
             */
            private readonly iterable $examples;

            /**
             * @param iterable<int, E>     $examples
             * @param array<string, mixed> $schema
             */
            public function __construct(
                private string $title,
                private string $description,
                iterable $examples,
                private $schema
            ) {
                $this->examples = [...$examples];
            }

            public function getTitle(): string
            {
                return $this->title;
            }

            public function getDescription(): string
            {
                return $this->description;
            }

            /**
             * @return iterable<int, E>
             */
            public function getExamples(): iterable
            {
                yield from $this->examples;
            }

            protected function getSchema(): array
            {
                return $this->schema;
            }
        };
    }

    /**
     * @template I
     *
     * @param JsonSchema<I> $jsonSchema
     *
     * @return JsonSchema<array<int, I>>
     */
    public static function collection(self $jsonSchema): self
    {
        return self::create(
            sprintf('Collection<%s>', $jsonSchema->getTitle()),
            '',
            [[...$jsonSchema->getExamples()]],
            [
                'type' => 'array',
                'items' => $jsonSchema,
            ]
        );
    }

    /**
     * @return array<string, mixed>&array{title: string, description: string, examples: array<T>}
     */
    public function jsonSerialize(): mixed
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

    /**
     * @return iterable<int, T>
     */
    abstract public function getExamples(): iterable;

    public function getTitle(): string
    {
        return '';
    }

    public function getDescription(): string
    {
        return '';
    }

    /**
     * @param scalar $value
     *
     * @return array<string, mixed>
     */
    protected static function constant($value): array
    {
        return [
            'const' => $value,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected static function null(): array
    {
        return [
            'type' => 'null',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected static function text(): array
    {
        return [
            'type' => 'string',
            'minLength' => 1,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected static function boolean(): array
    {
        return [
            'type' => 'boolean',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected static function string(?string $format = null): array
    {
        $result = [
            ...self::text(),
            'maxLength' => 255,
        ];

        if (null !== $format) {
            $result['format'] = $format;
        }

        return $result;
    }

    /**
     * @return array<string, mixed>
     */
    protected static function integer(): array
    {
        return [
            'type' => 'integer',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected static function number(): array
    {
        return [
            'type' => 'number',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected static function date(): array
    {
        return [
            'type' => 'string',
            'format' => 'date',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected static function positiveInteger(): array
    {
        return [
            ...self::integer(),
            'exclusiveMinimum' => 0,
        ];
    }

    /**
     * @param array<string, mixed> ...$schemas
     *
     * @return array{oneOf: array<array<string, mixed>>}
     */
    protected static function oneOf(...$schemas): array
    {
        return [
            'oneOf' => $schemas,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    abstract protected function getSchema(): array;
}
