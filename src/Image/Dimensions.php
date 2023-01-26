<?php

/*
 * This file is part of the zenstruck/image package.
 *
 * (c) Kevin Bond <kevinbond@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zenstruck\Image;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class Dimensions
{
    /**
     * @param array{0:int,1:int}|callable():array{0:int,1:int} $values
     */
    public function __construct(private $values)
    {
    }

    public function width(): int
    {
        return $this->values()[0];
    }

    public function height(): int
    {
        return $this->values()[1];
    }

    public function aspectRatio(): float
    {
        return $this->width() / $this->height();
    }

    public function pixels(): int
    {
        return $this->width() * $this->height();
    }

    public function isSquare(): bool
    {
        return $this->width() === $this->height();
    }

    public function isPortrait(): bool
    {
        return $this->height() > $this->width();
    }

    public function isLandscape(): bool
    {
        return $this->width() > $this->height();
    }

    /**
     * @return array{0:int,1:int}
     */
    private function values(): array
    {
        if (\is_callable($this->values)) {
            return $this->values = ($this->values)();
        }

        return $this->values;
    }
}
