<?php

namespace Zenstruck\Image\Tests;

use PHPUnit\Framework\TestCase;
use Zenstruck\Image;
use Zenstruck\TempFile;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
abstract class TransformerTest extends TestCase
{
    /**
     * @test
     */
    public function can_transform_into_temp_image(): void
    {
        $image = new Image(__DIR__.'/Fixture/files/symfony.jpg');

        $resized = $image->transform($this->manipulator());

        $this->assertSame(100, $resized->width());
        $this->assertSame(120, $resized->height());
        $this->assertSame('jpg', $resized->getExtension());
        $this->assertSame('/tmp', \dirname($resized));

        $resized = $image->transform($this->manipulator(), ['format' => 'png']);

        $this->assertSame(100, $resized->width());
        $this->assertSame(120, $resized->height());
        $this->assertSame('png', $resized->getExtension());
        $this->assertSame('/tmp', \dirname($resized));
    }

    /**
     * @test
     */
    public function can_transform_to_specific_file(): void
    {
        $output = TempFile::new();
        $image = new Image(__DIR__.'/Fixture/files/symfony.jpg');

        $resized = $image->transform($this->manipulator(), ['output' => $output]);

        $this->assertSame((string) $output, (string) $resized);
        $this->assertSame(100, $resized->width());
        $this->assertSame(120, $resized->height());
        $this->assertSame('image/jpeg', $resized->mimeType());
    }

    /**
     * @test
     */
    public function can_transform_in_place(): void
    {
        $image = Image::from(new \SplFileInfo(__DIR__.'/Fixture/files/symfony.jpg'));

        $this->assertSame(678, $image->height());
        $this->assertSame(563, $image->width());

        $resized = $image->transformInPlace($this->manipulator());

        $this->assertSame($image, $resized);
        $this->assertSame((string) $image, (string) $resized);
        $this->assertSame(100, $resized->width());
        $this->assertSame(120, $resized->height());
        $this->assertSame(100, $image->width());
        $this->assertSame(120, $image->height());
    }

    /**
     * @test
     */
    public function manipulator_must_return_proper_object(): void
    {
        $image = new Image(__DIR__.'/Fixture/files/symfony.jpg');

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Manipulator callback must return');

        $image->transform($this->invalidManipulator());
    }

    /**
     * @test
     */
    public function can_get_transformer_object(): void
    {
        $image = new Image(__DIR__.'/Fixture/files/symfony.jpg');

        $this->assertInstanceOf($this->objectClass(), $image->transformer($this->objectClass()));
    }

    abstract protected function invalidManipulator(): callable;

    abstract protected function manipulator(): callable;

    abstract protected function objectClass(): string;
}
