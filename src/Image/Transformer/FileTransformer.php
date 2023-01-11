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

use Zenstruck\Image;
use Zenstruck\Image\Transformer;
use Zenstruck\TempFile;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @template T of object
 * @implements Transformer<T>
 */
abstract class FileTransformer implements Transformer
{
    final public function transform(Image $image, callable $filter, array $options = []): Image
    {
        $options['format'] ??= $image->guessExtension();
        $output = $options['output'] ??= TempFile::withExtension($options['format']);
        $options['output'] = (string) $options['output'];

        $transformed = $filter($this->object($image));

        if (!\is_a($transformed, static::expectedClass())) {
            throw new \LogicException(\sprintf('Filter callback must return a "%s" object.', static::expectedClass()));
        }

        $this->save($transformed, $options);

        return Image::wrap($output);
    }

    /**
     * @return class-string<T>
     */
    abstract protected static function expectedClass(): string;

    /**
     * @param T                                                      $object
     * @param array{format:string,output:string}|array<string,mixed> $options
     */
    abstract protected function save(object $object, array $options): void;
}
