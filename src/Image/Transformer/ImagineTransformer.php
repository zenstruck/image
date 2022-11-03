<?php

namespace Zenstruck\Image\Transformer;

use Imagine\Gd\Image as GdImage;
use Imagine\Gd\Imagine as GdImagine;
use Imagine\Gmagick\Image as GmagickImage;
use Imagine\Gmagick\Imagine as GmagickImagine;
use Imagine\Image\ImageInterface;
use Imagine\Image\ImagineInterface;
use Imagine\Imagick\Image as ImagickImage;
use Imagine\Imagick\Imagine as ImagickImagine;
use Zenstruck\Image;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @extends BaseTransformer<ImageInterface>
 */
final class ImagineTransformer extends BaseTransformer
{
    public function __construct(private ImagineInterface $imagine)
    {
    }

    /**
     * @template T of ImageInterface
     *
     * @param class-string<T> $class
     */
    public static function createFor(string $class): self
    {
        return match ($class) {
            ImageInterface::class, GdImage::class => new self(new GdImagine()),
            ImagickImage::class => new self(new ImagickImagine()),
            GmagickImage::class => new self(new GmagickImagine()),
            default => throw new \InvalidArgumentException('invalid class'),
        };
    }

    protected function doTransform(Image $image, callable $manipulator, array $options): void
    {
        $imagineImage = $manipulator($this->imagine->open($image));

        if (!$imagineImage instanceof ImageInterface) {
            throw new \LogicException('Manipulator callback must return an Imagine image object.');
        }

        $imagineImage->save($options['output'], $options);
    }
}
