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

use Zenstruck\Image\Tests\TransformerTestCase;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class GdImageTransformerTest extends TransformerTestCase
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
}
