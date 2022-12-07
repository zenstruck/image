<?php

namespace Zenstruck\Image\Tests\Transformer;

use Imagine\Image\ImageInterface;
use Zenstruck\Image\Tests\TransformerTest;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class ImagineTransformerTest extends TransformerTest
{
    protected function filterCallback(): callable
    {
        return fn(ImageInterface $image) => $image->thumbnail($image->getSize()->widen(100));
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
