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
use Zenstruck\ImageFileInfo;
use Zenstruck\TempFile;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
abstract class FilterObjectTransformerTestCase extends TransformerTestCase
{
    /**
     * @test
     */
    public function can_transform_into_temp_image_with_filter_object(): void
    {
        $image = new ImageFileInfo(__DIR__.'/../Fixture/files/symfony.jpg');

        $resized = $image->transform($this->filterCallback());

        $this->assertSame(100, $resized->dimensions()->width());
        $this->assertSame(120, $resized->dimensions()->height());
        $this->assertSame('jpg', $resized->getExtension());
        $this->assertSame('/tmp', \dirname($resized));

        $resized = $image->transform($this->filterObject(), ['format' => 'png']);

        $this->assertSame(100, $resized->dimensions()->width());
        $this->assertSame(120, $resized->dimensions()->height());
        $this->assertSame('png', $resized->getExtension());
        $this->assertSame('/tmp', \dirname($resized));
    }

    /**
     * @test
     */
    public function can_transform_to_specific_file_with_filter_object(): void
    {
        $output = TempFile::new();
        $image = new ImageFileInfo(__DIR__.'/../Fixture/files/symfony.jpg');

        $resized = $image->transform($this->filterObject(), ['output' => $output]);

        $this->assertSame((string) $output, (string) $resized);
        $this->assertSame(100, $resized->dimensions()->width());
        $this->assertSame(120, $resized->dimensions()->height());
        $this->assertSame('image/jpeg', $resized->mimeType());
    }

    /**
     * @test
     */
    public function can_transform_in_place_with_filter_object(): void
    {
        $image = ImageFileInfo::from(new \SplFileInfo(__DIR__.'/../Fixture/files/symfony.jpg'));

        $this->assertSame(678, $image->dimensions()->height());
        $this->assertSame(563, $image->dimensions()->width());

        $resized = $image->transformInPlace($this->filterObject());

        $this->assertSame($image, $resized);
        $this->assertSame((string) $image, (string) $resized);
        $this->assertSame(100, $resized->dimensions()->width());
        $this->assertSame(120, $resized->dimensions()->height());
        $this->assertSame(100, $image->dimensions()->width());
        $this->assertSame(120, $image->dimensions()->height());
    }

    abstract protected function filterObject(): object;
}
