<?php

namespace Zenstruck\Image\Tests\Transformer;

use Zenstruck\Image;
use Zenstruck\Image\Tests\TransformerTest;
use Zenstruck\TempFile;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
abstract class FilterObjectTransformerTest extends TransformerTest
{
    /**
     * @test
     */
    public function can_transform_into_temp_image_with_filter_object(): void
    {
        $image = new Image(__DIR__.'/../Fixture/files/symfony.jpg');

        $resized = $image->transform($this->filterCallback());

        $this->assertSame(100, $resized->width());
        $this->assertSame(120, $resized->height());
        $this->assertSame('jpg', $resized->getExtension());
        $this->assertSame('/tmp', \dirname($resized));

        $resized = $image->transform($this->filterObject(), ['format' => 'png']);

        $this->assertSame(100, $resized->width());
        $this->assertSame(120, $resized->height());
        $this->assertSame('png', $resized->getExtension());
        $this->assertSame('/tmp', \dirname($resized));
    }

    /**
     * @test
     */
    public function can_transform_to_specific_file_with_filter_object(): void
    {
        $output = TempFile::new();
        $image = new Image(__DIR__.'/../Fixture/files/symfony.jpg');

        $resized = $image->transform($this->filterObject(), ['output' => $output]);

        $this->assertSame((string) $output, (string) $resized);
        $this->assertSame(100, $resized->width());
        $this->assertSame(120, $resized->height());
        $this->assertSame('image/jpeg', $resized->mimeType());
    }

    /**
     * @test
     */
    public function can_transform_in_place_with_filter_object(): void
    {
        $image = Image::from(new \SplFileInfo(__DIR__.'/../Fixture/files/symfony.jpg'));

        $this->assertSame(678, $image->height());
        $this->assertSame(563, $image->width());

        $resized = $image->transformInPlace($this->filterObject());

        $this->assertSame($image, $resized);
        $this->assertSame((string) $image, (string) $resized);
        $this->assertSame(100, $resized->width());
        $this->assertSame(120, $resized->height());
        $this->assertSame(100, $image->width());
        $this->assertSame(120, $image->height());
    }

    abstract protected function filterObject(): object;
}
