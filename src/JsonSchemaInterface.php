<?php

declare(strict_types=1);

namespace Knp\JsonSchema;

use JsonSerializable;
use Knp\JsonSchema\Validator\Errors;

/**
 * @template T
 */
interface JsonSchemaInterface extends JsonSerializable
{
    /**
     * @return array<string, mixed>&array{title: string, description: string, examples: array<T>}
     */
    public function jsonSerialize(): array;

    public function getTitle(): string;

    public function getDescription(): string;

    /**
     * @return iterable<int, T>
     */
    public function getExamples(): iterable;

    /**
     * @return array<string, mixed>
     */
    public function getSchema(): array;
}
