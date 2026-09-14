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

use Alto\Image\Image;
use Alto\Image\Operation\Resize;
use Alto\Image\Transform;
use Zenstruck\ImageFileInfo;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class AltoImageTransformerTest extends FilterObjectTransformerTestCase
{
    /**
     * @test
     */
    public function can_transform_with_transform_object(): void
    {
        $image = new ImageFileInfo(__DIR__.'/../Fixture/files/symfony.jpg');

        $resized = $image->transform(Transform::parse('inside=100x'));

        $this->assertSame(100, $resized->dimensions()->width());
        $this->assertSame(120, $resized->dimensions()->height());
        $this->assertInTempDir($resized);
    }

    protected function invalidFilterCallback(): callable
    {
        return static fn(Image $i) => null;
    }

    protected function filterObject(): object
    {
        return new Resize(width: 100);
    }

    protected function filterInvokable(): object
    {
        return new class {
            public function __invoke(Image $image): Image
            {
                return $image->scale(width: 100);
            }
        };
    }

    protected function filterCallback(): callable
    {
        return static fn(Image $i) => $i->scale(width: 100);
    }

    protected function objectClass(): string
    {
        return Image::class;
    }

    protected function objectDimensionsCallback(): callable
    {
        return static fn(Image $i) => [$i->size()->height, $i->size()->width];
    }
}
