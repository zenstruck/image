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

use Intervention\Image\Filters\FilterInterface;
use Intervention\Image\Image as InterventionImage;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class InterventionTransformerTest extends FilterObjectTransformerTest
{
    protected function filterCallback(): callable
    {
        return fn(InterventionImage $image) => $image->widen(100);
    }

    protected function filterInvokable(): object
    {
        return new class() {
            public function __invoke(InterventionImage $image): InterventionImage
            {
                return $image->widen(100);
            }
        };
    }

    protected function filterObject(): FilterInterface
    {
        return new class() implements FilterInterface {
            public function applyFilter(InterventionImage $image): InterventionImage
            {
                return $image->widen(100);
            }
        };
    }

    protected function invalidFilterCallback(): callable
    {
        return fn(InterventionImage $image) => null;
    }

    protected function objectClass(): string
    {
        return InterventionImage::class;
    }
}
