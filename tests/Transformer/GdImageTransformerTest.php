<?php

namespace Zenstruck\Image\Tests\Transformer;

use Zenstruck\Image\Tests\TransformerTest;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class GdImageTransformerTest extends TransformerTest
{
    protected function filterInvokable(): object
    {
        return new class() {
            public function __invoke(\GdImage $image): \GdImage
            {
                return \imagescale($image, 100);
            }
        };
    }

    protected function filterCallback(): callable
    {
        return fn(\GdImage $i) => \imagescale($i, 100);
    }

    protected function invalidFilterCallback(): callable
    {
        return fn(\GdImage $i) => null;
    }

    protected function objectClass(): string
    {
        return \GdImage::class;
    }
}
