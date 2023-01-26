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
use Zenstruck\Image;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class ImageTest extends TestCase
{
    /**
     * @test
     * @dataProvider imageMetadataProvider
     */
    public function can_get_metadata(string $file, int $height, int $width, string $mime, string $extension): void
    {
        $this->metadataAssertions(Image::wrap($file), $height, $width, $mime, $extension);

        $this->metadataAssertions($image = Image::from(\file_get_contents($file)), $height, $width, $mime, $extension);
        $this->assertSame('/tmp', \dirname($image));

        $this->metadataAssertions($image = Image::from(\fopen($file, 'r')), $height, $width, $mime, $extension);
        $this->assertSame('/tmp', \dirname($image));

        $this->metadataAssertions($image = Image::from(new \SplFileInfo($file)), $height, $width, $mime, $extension);
        $this->assertSame('/tmp', \dirname($image));
    }

    public static function imageMetadataProvider(): iterable
    {
        $fixtureDir = __DIR__.'/Fixture/files';

        yield [$fixtureDir.'/symfony.jpg', 678, 563, 'image/jpeg', 'jpg'];
        yield [$fixtureDir.'/symfony-jpg', 678, 563, 'image/jpeg', 'jpg'];
        yield [$fixtureDir.'/symfony.gif', 678, 563, 'image/gif', 'gif'];
        yield [$fixtureDir.'/symfony-gif', 678, 563, 'image/gif', 'gif'];
        yield [$fixtureDir.'/symfony.png', 678, 563, 'image/png', 'png'];
        yield [$fixtureDir.'/symfony-png', 678, 563, 'image/png', 'png'];
        yield [$fixtureDir.'/symfony.svg', 224, 202, 'image/svg+xml', 'svg'];
        yield [$fixtureDir.'/symfony-svg', 224, 202, 'image/svg+xml', 'svg'];
    }

    /**
     * @test
     */
    public function can_get_exif_and_iptc_data(): void
    {
        $image = new Image(__DIR__.'/Fixture/files/metadata.jpg');

        $this->assertSame(16, $image->exif()['computed.Height']);
        $this->assertSame('Lorem Ipsum', $image->iptc()['DocumentTitle']);
    }

    /**
     * @test
     */
    public function cannot_get_metadata_for_non_image(): void
    {
        $image = new Image(__FILE__);

        $this->expectException(\RuntimeException::class);

        $image->dimensions()->height();
    }

    /**
     * @test
     */
    public function can_delete_image(): void
    {
        $image = Image::from('image content');

        $this->assertFileExists($image);

        $image->delete();

        $this->assertFileDoesNotExist($image);
    }

    /**
     * @test
     */
    public function can_refresh(): void
    {
        $image = Image::from(new \SplFileInfo(__DIR__.'/Fixture/files/symfony.jpg'));

        $this->assertSame('image/jpeg', $image->mimeType());
        $this->assertSame(678, $image->dimensions()->height());
        $this->assertSame(563, $image->dimensions()->width());
        $this->assertSame([], $image->iptc());
        $this->assertSame(678, $image->exif()['computed.Height']);

        \file_put_contents($image, \file_get_contents(__DIR__.'/Fixture/files/metadata.jpg'));

        $this->assertSame('image/jpeg', $image->mimeType());
        $this->assertSame(678, $image->dimensions()->height());
        $this->assertSame(563, $image->dimensions()->width());
        $this->assertSame([], $image->iptc());
        $this->assertSame(678, $image->exif()['computed.Height']);

        $this->assertSame($image, $image->refresh());

        $this->assertSame('image/jpeg', $image->mimeType());
        $this->assertSame(16, $image->dimensions()->height());
        $this->assertSame(16, $image->dimensions()->width());
        $this->assertSame('Lorem Ipsum', $image->iptc()['DocumentTitle']);
        $this->assertSame(16, $image->exif()['computed.Height']);
    }

    private function metadataAssertions(Image $image, int $height, int $width, string $mime, string $extension): void
    {
        $this->assertSame($height, $image->dimensions()->height());
        $this->assertSame($width, $image->dimensions()->width());
        $this->assertSame($width * $height, $image->dimensions()->pixels());
        $this->assertSame($width / $height, $image->dimensions()->aspectRatio());
        $this->assertSame($height === $width, $image->dimensions()->isSquare());
        $this->assertSame($height < $width, $image->dimensions()->isLandscape());
        $this->assertSame($height > $width, $image->dimensions()->isPortrait());
        $this->assertSame($mime, $image->mimeType());
        $this->assertSame($extension, $image->guessExtension());
    }
}
