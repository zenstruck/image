<?php

namespace Zenstruck;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
interface Image
{
    /**
     * @template T of object
     *
     * @param object|callable(T):T $filter
     */
    public function transform(object|callable $filter, array $options = []): self;

    /**
     * @template T of object
     *
     * @param class-string<T> $class
     *
     * @return T
     */
    public function transformer(string $class): object;

    public function height(): int;

    public function width(): int;

    public function aspectRatio(): float;

    public function pixels(): int;

    public function isSquare(): bool;

    public function isPortrait(): bool;

    public function isLandscape(): bool;

    public function exif(): array;

    public function iptc(): array;

    public function refresh(): static;
}
