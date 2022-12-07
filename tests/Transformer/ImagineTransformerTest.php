<?php

namespace Zenstruck\Image\Tests\Transformer;

use Imagine\Filter\FilterInterface;
use Imagine\Image\ImageInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class ImagineTransformerTest extends FilterObjectTransformerTest
{
    protected function filterCallback(): callable
    {
        return fn(ImageInterface $image) => $image->thumbnail($image->getSize()->widen(100));
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
}
