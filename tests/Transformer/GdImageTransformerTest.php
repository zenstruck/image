<?php

namespace Zenstruck\Image\Tests\Transformer;

use Zenstruck\Image\Tests\TransformerTest;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class GdImageTransformerTest extends TransformerTest
{
    protected function manipulator(): callable
    {
        return fn(\GdImage $i) => \imagescale($i, 100);
    }

    protected function invalidManipulator(): callable
    {
        return fn(\GdImage $i) => null;
    }

    protected function objectClass(): string
    {
        return \GdImage::class;
    }
}
