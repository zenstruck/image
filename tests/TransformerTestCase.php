<?php

/*
 * This file is part of the zenstruck/image package.
 *
 * (c) Kevin Bond <kevinbond@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zenstruck\Image\Tests;

use PHPUnit\Framework\TestCase;
use Zenstruck\ImageFileInfo;
use Zenstruck\TempFile;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
abstract class TransformerTestCase extends TestCase
{
    /**
     * @test
     */
    public function can_transform_into_temp_image(): void
    {
        $image = new ImageFileInfo(__DIR__.'/Fixture/files/symfony.jpg');

        $resized = $image->transform($this->filterCallback());

        $this->assertSame(100, $resized->dimensions()->width());
        $this->assertSame(120, $resized->dimensions()->height());
        $this->assertSame('jpg', $resized->getExtension());
        $this->assertSame('/tmp', \dirname($resized));

        $resized = $image->transform($this->filterCallback(), ['format' => 'png']);

        $this->assertSame(100, $resized->dimensions()->width());
        $this->assertSame(120, $resized->dimensions()->height());
        $this->assertSame('png', $resized->getExtension());
        $this->assertSame('/tmp', \dirname($resized));
    }

    /**
     * @test
     */
    public function can_transform_to_specific_file(): void
    {
        $output = TempFile::withExtension('jpg');
        $image = new ImageFileInfo(__DIR__.'/Fixture/files/symfony.jpg');

        $resized = $image->transform($this->filterCallback(), ['output' => $output]);

        $this->assertSame((string) $output, (string) $resized);
        $this->assertSame(100, $resized->dimensions()->width());
        $this->assertSame(120, $resized->dimensions()->height());
        $this->assertSame('image/jpeg', $resized->mimeType());
    }

    /**
     * @test
     */
    public function can_transform_in_place(): void
    {
        $image = new ImageFileInfo(TempFile::for(new \SplFileInfo(__DIR__.'/Fixture/files/symfony.jpg'), extension: 'jpg'));

        $this->assertSame(678, $image->dimensions()->height());
        $this->assertSame(563, $image->dimensions()->width());

        $resized = $image->transformInPlace($this->filterCallback());

        $this->assertSame($image, $resized);
        $this->assertSame((string) $image, (string) $resized);
        $this->assertSame(100, $resized->dimensions()->width());
        $this->assertSame(120, $resized->dimensions()->height());
        $this->assertSame(100, $image->dimensions()->width());
        $this->assertSame(120, $image->dimensions()->height());
    }

    /**
     * @test
     */
    public function can_transform_into_temp_image_with_invokable_object(): void
    {
        $image = new ImageFileInfo(__DIR__.'/Fixture/files/symfony.jpg');

        $resized = $image->transform($this->filterInvokable());

        $this->assertSame(100, $resized->dimensions()->width());
        $this->assertSame(120, $resized->dimensions()->height());
        $this->assertSame('jpg', $resized->getExtension());
        $this->assertSame('/tmp', \dirname($resized));

        $resized = $image->transform($this->filterCallback(), ['format' => 'png']);

        $this->assertSame(100, $resized->dimensions()->width());
        $this->assertSame(120, $resized->dimensions()->height());
        $this->assertSame('png', $resized->getExtension());
        $this->assertSame('/tmp', \dirname($resized));
    }

    /**
     * @test
     */
    public function can_transform_to_specific_file_with_invokable_object(): void
    {
        $output = TempFile::withExtension('jpg');
        $image = new ImageFileInfo(__DIR__.'/Fixture/files/symfony.jpg');

        $resized = $image->transform($this->filterInvokable(), ['output' => $output]);

        $this->assertSame((string) $output, (string) $resized);
        $this->assertSame(100, $resized->dimensions()->width());
        $this->assertSame(120, $resized->dimensions()->height());
        $this->assertSame('image/jpeg', $resized->mimeType());
    }

    /**
     * @test
     */
    public function can_transform_in_place_with_invokable_object(): void
    {
        $image = new ImageFileInfo(TempFile::for(new \SplFileInfo(__DIR__.'/Fixture/files/symfony.jpg'), extension: 'jpg'));

        $this->assertSame(678, $image->dimensions()->height());
        $this->assertSame(563, $image->dimensions()->width());

        $resized = $image->transformInPlace($this->filterInvokable());

        $this->assertSame($image, $resized);
        $this->assertSame((string) $image, (string) $resized);
        $this->assertSame(100, $resized->dimensions()->width());
        $this->assertSame(120, $resized->dimensions()->height());
        $this->assertSame(100, $image->dimensions()->width());
        $this->assertSame(120, $image->dimensions()->height());
    }

    /**
     * @test
     */
    public function can_convert_image_to_transformer_object(): void
    {
        $image = new ImageFileInfo(__DIR__.'/Fixture/files/symfony.jpg');

        $object = $image->as($this->objectClass());

        $this->assertInstanceOf($this->objectClass(), $object);

        [$height, $width] = $this->objectDimensionsCallback()($object);

        $this->assertSame(678, $height);
        $this->assertSame(563, $width);
    }

    /**
     * @test
     */
    public function filter_callback_must_return_proper_object(): void
    {
        $image = new ImageFileInfo(__DIR__.'/Fixture/files/symfony.jpg');

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Filter callback must return');

        $image->transform($this->invalidFilterCallback());
    }

    /**
     * @return class-string
     */
    abstract protected function objectClass(): string;

    /**
     * @return callable(object):array{int, int}
     */
    abstract protected function objectDimensionsCallback(): callable;

    abstract protected function invalidFilterCallback(): callable;

    /**
     * @return object&callable
     */
    abstract protected function filterInvokable(): object;

    abstract protected function filterCallback(): callable;
}
