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

use Spatie\Image\Enums\ImageDriver;
use Spatie\Image\Image;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @internal
 *
 * @extends FileTransformer<Image>
 */
final class SpatieImageTransformer extends FileTransformer
{
    protected function object(\SplFileInfo $image): object
    {
        if (!(new \ReflectionMethod(Image::class, 'useImageDriver'))->isStatic()) {
            // using spatie/image v2
            return Image::load($image->getPathname());
        }

        return Image::useImageDriver(\class_exists(\Imagick::class) ? ImageDriver::Imagick : ImageDriver::Gd)
            ->loadFile($image)
        ;
    }

    protected static function expectedClass(): string
    {
        return Image::class;
    }

    protected function save(object $object, array $options): void
    {
        $object
            ->format($options['format'])
            ->save($options['output'])
        ;
    }
}
