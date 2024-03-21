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

use Imagine\Filter\FilterInterface;
use Imagine\Image\ImageInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class ImagineTransformerTest extends FilterObjectTransformerTestCase
{
    protected function filterCallback(): callable
    {
        return fn(ImageInterface $image) => $image->thumbnail($image->getSize()->widen(100));
    }

    protected function filterInvokable(): object
    {
        return new class() {
            public function __invoke(ImageInterface $image): ImageInterface
            {
                return $image->thumbnail($image->getSize()->widen(100));
            }
        };
    }

    protected function filterObject(): FilterInterface
    {
        return new class() implements FilterInterface {
            public function apply(ImageInterface $image): ImageInterface
            {
                return $image->thumbnail($image->getSize()->widen(100));
            }
        };
    }

    protected function invalidFilterCallback(): callable
    {
        return fn(ImageInterface $image) => null;
    }

    protected function objectClass(): string
    {
        return ImageInterface::class;
    }

    protected function objectDimensionsCallback(): callable
    {
        return fn(ImageInterface $i) => [$i->getSize()->getHeight(), $i->getSize()->getWidth()];
    }
}
