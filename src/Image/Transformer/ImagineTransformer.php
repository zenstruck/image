<?php

/*
 * This file is part of the zenstruck/image package.
 *
 * (c) Kevin Bond <kevinbond@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zenstruck\Image\Transformer;

use Imagine\Gd\Image as GdImage;
use Imagine\Gd\Imagine as GdImagine;
use Imagine\Gmagick\Image as GmagickImage;
use Imagine\Gmagick\Imagine as GmagickImagine;
use Imagine\Image\ImageInterface;
use Imagine\Image\ImagineInterface;
use Imagine\Imagick\Image as ImagickImage;
use Imagine\Imagick\Imagine as ImagickImagine;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @extends FileTransformer<ImageInterface>
 */
final class ImagineTransformer extends FileTransformer
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
        if (!\interface_exists(ImageInterface::class)) {
            throw new \LogicException('imagine/imagine required. Install with "composer require imagine/imagine".');
        }

        return match ($class) {
            ImageInterface::class, GdImage::class => new self(new GdImagine()),
            ImagickImage::class => new self(new ImagickImagine()),
            GmagickImage::class => new self(new GmagickImagine()),
            default => throw new \InvalidArgumentException('invalid class'),
        };
    }

    protected function createObject(\SplFileInfo $image): object
    {
        return $this->imagine->open($image);
    }

    protected static function expectedClass(): string
    {
        return ImageInterface::class;
    }

    protected function save(object $object, array $options): void
    {
        $object->save($options['output'], $options);
    }
}
