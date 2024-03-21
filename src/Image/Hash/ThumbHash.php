<?php

/*
 * This file is part of the zenstruck/image package.
 *
 * (c) Kevin Bond <kevinbond@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zenstruck\Image\Hash;

use Zenstruck\ImageFileInfo;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class ThumbHash
{
    /** @var list<int> */
    private array $hash;
    private string $dataUri;

    private function __construct(private \SplFileInfo|string $source)
    {
        if (!\class_exists(\Thumbhash\Thumbhash::class)) {
            throw new \LogicException(\sprintf('"%s" requires the "srwiez/thumbhash" package to be installed. Run "composer require srwiez/thumbhash".', self::class));
        }

        if (!\class_exists(\Imagick::class)) {
            throw new \LogicException(\sprintf('"%s" requires the "imagick" extension to be installed.', self::class));
        }
    }

    /**
     * Create from either an \SplFileInfo or a "key" string.
     */
    public static function from(\SplFileInfo|string $source): self
    {
        return new self($source);
    }

    public function dataUri(): string
    {
        return $this->dataUri ??= \Thumbhash\Thumbhash::toDataURL($this->hash());
    }

    public function key(): string
    {
        if (\is_string($this->source)) {
            return $this->source;
        }

        return $this->source = \Thumbhash\Thumbhash::convertHashToString($this->hash());
    }

    /**
     * @return list<int>
     */
    public function hash(): array
    {
        if (isset($this->hash)) {
            return $this->hash;
        }

        if (\is_string($this->source)) {
            return $this->hash = \Thumbhash\Thumbhash::convertStringToHash($this->source);
        }

        [$width, $height, $pixels] = self::extractSizeAndPixels($this->source);

        return $this->hash = \Thumbhash\Thumbhash::RGBAToHash($width, $height, $pixels);
    }

    public function approximateAspectRatio(): float
    {
        return \Thumbhash\Thumbhash::toApproximateAspectRatio($this->hash());
    }

    /**
     * @see \Thumbhash\extract_size_and_pixels_with_imagick()
     *
     * @return array{int, int, array}
     */
    private static function extractSizeAndPixels(\SplFileInfo $file): array
    {
        $image = ImageFileInfo::wrap($file)->as(\Imagick::class);

        if ($image->getImageWidth() > 100 || $image->getImageHeight() > 100) {
            $image->scaleImage(100, 100, bestfit: true);
        }

        $width = $image->getImageWidth();
        $height = $image->getImageHeight();
        $pixels = [];

        for ($y = 0; $y < $height; ++$y) {
            for ($x = 0; $x < $width; ++$x) {
                $pixel = $image->getImagePixelColor($x, $y);
                $colors = $pixel->getColor(2);
                $pixels[] = $colors['r'];
                $pixels[] = $colors['g'];
                $pixels[] = $colors['b'];
                $pixels[] = $colors['a'];
            }
        }

        return [$width, $height, $pixels];
    }
}
