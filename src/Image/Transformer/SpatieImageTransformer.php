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
        return Image::load($image);
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
