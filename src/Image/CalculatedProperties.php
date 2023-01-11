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
trait CalculatedProperties
{
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
}
