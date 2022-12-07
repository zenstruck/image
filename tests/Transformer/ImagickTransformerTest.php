<?php

namespace Zenstruck\Image\Tests\Transformer;

use Zenstruck\Image\Tests\TransformerTest;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class ImagickTransformerTest extends TransformerTest
{
    protected function setUp(): void
    {
        if (!\class_exists(\Imagick::class)) {
            $this->markTestSkipped('Imagick not available.');
        }
    }

    protected function filterCallback(): callable
    {
        return function(\Imagick $image) {
            $image->scaleImage(100, 0);

            return $image;
        };
    }

    protected function invalidFilterCallback(): callable
    {
        return fn(\Imagick $image) => null;
    }

    protected function objectClass(): string
    {
        return \Imagick::class;
    }
}
