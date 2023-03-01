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

    protected function filterInvokable(): object
    {
        return new class() {
            public function __invoke(\Imagick $image): \Imagick
            {
                $image->scaleImage(100, 0);

                return $image;
            }
        };
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
}
