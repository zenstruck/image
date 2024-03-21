<?php

/*
 * This file is part of the zenstruck/image package.
 *
 * (c) Kevin Bond <kevinbond@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zenstruck\Image\Tests\Transformer;

use Spatie\Image\Image;
use Zenstruck\Image\Tests\TransformerTestCase;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class SpatieImageTransformerTest extends TransformerTestCase
{
    protected function invalidFilterCallback(): callable
    {
        return fn(Image $i) => null;
    }

    protected function filterInvokable(): object
    {
        return new class() {
            public function __invoke(Image $image): Image
            {
                return $image->width(100);
            }
        };
    }

    protected function filterCallback(): callable
    {
        return fn(Image $i) => $i->width(100);
    }

    protected function objectClass(): string
    {
        return Image::class;
    }

    protected function objectDimensionsCallback(): callable
    {
        return fn(Image $i) => [$i->getHeight(), $i->getWidth()];
    }
}
