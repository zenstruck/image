<?php

namespace Zenstruck\Image;

use Intervention\Image\Constraint;
use Intervention\Image\Image as InterventionImage;
use Intervention\Image\ImageManagerStatic;
use Intervention\Image\Size;
use kornrunner\Blurhash\Blurhash as BlurHashEncoder;
use Zenstruck\Image;

/**
 * @author Titouan Galopin <galopintitouan@gmail.com>
 * @author Kevin Bond <kevinbond@gmail.com>
 * @source https://github.com/symfony/ux/blob/240028f49fcbe5a9ba16334a19a3bb120b5c22fc/src/LazyImage/BlurHash/BlurHash.php
 */
final class BlurHash implements \Stringable
{
    public function __construct(private Image $image)
    {
        if (!\class_exists(BlurHashEncoder::class)) {
            throw new \LogicException('kornrunner/blurhash is required to create blurhashes. Install with "composer require kornrunner/blurhash".');
        }
    }

    public function __toString(): string
    {
        return $this->encode();
    }

    /**
     * @param array{
     *     width?: int,
     *     height?:int,
     * } $options
     */
    public function encode(array $options = []): string
    {
        $image = $this->image->transformer(InterventionImage::class);

        // Resize image to increase encoding performance
        $image->resize($options['width'] ?? 75, $options['height'] ?? 75, static function(Constraint $constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        // Encode using BlurHash
        $width = $image->getWidth();
        $height = $image->getHeight();

        $pixels = [];

        for ($y = 0; $y < $height; ++$y) {
            $row = [];

            for ($x = 0; $x < $width; ++$x) {
                $color = $image->pickColor($x, $y);
                $row[] = [$color[0], $color[1], $color[2]];
            }

            $pixels[] = $row;
        }

        return BlurhashEncoder::encode($pixels, 4, 3);
    }

    /**
     * @param array{
     *     width?: int,
     *     height?: int,
     * } $options
     */
    public function dataUri(array $options = []): string
    {
        // Resize and encode
        $encoded = $this->encode($options);
        $size = (new Size($this->image->width(), $this->image->height()))
            ->resize($options['width'] ?? 75, $options['width'] ?? 75, static function(Constraint $constraint) {
                $constraint->aspectRatio();
            })
        ;

        // Create a new blurred thumbnail from encoded BlurHash
        $pixels = BlurhashEncoder::decode($encoded, $size->width, $size->height);
        $thumbnail = ImageManagerStatic::canvas($size->width, $size->height);

        for ($y = 0; $y < $size->height; ++$y) {
            for ($x = 0; $x < $size->width; ++$x) {
                $thumbnail->pixel($pixels[$y][$x], $x, $y);
            }
        }

        return 'data:image/jpeg;base64,'.\base64_encode($thumbnail->encode('jpg', 80));
    }
}
