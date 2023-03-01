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

use Zenstruck\Image\Transformer;
use Zenstruck\ImageFileInfo;
use Zenstruck\TempFile;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @internal
 *
 * @template T of object
 * @implements Transformer<T>
 */
abstract class FileTransformer implements Transformer
{
    final public function transform(\SplFileInfo $image, callable|object $filter, array $options = []): \SplFileInfo
    {
        $filter = static::normalizeFilter($filter);
        $image = ImageFileInfo::wrap($image);
        $options['format'] ??= $image->guessExtension();
        $output = $options['output'] ??= TempFile::withExtension($options['format']);
        $options['output'] = (string) $options['output'];

        $transformed = $filter($this->object($image));

        if (!\is_a($transformed, static::expectedClass())) {
            throw new \LogicException(\sprintf('Filter callback must return a "%s" object.', static::expectedClass()));
        }

        $this->save($transformed, $options);

        return ImageFileInfo::wrap($output);
    }

    /**
     * @param object|callable(T):T $filter
     *
     * @return callable(T):T
     */
    public static function normalizeFilter(callable|object $filter): callable
    {
        return \is_callable($filter) ? $filter : throw new \InvalidArgumentException(\sprintf('"%s" does not support "%s".', self::class, $filter::class));
    }

    /**
     * @return T
     */
    abstract protected function object(\SplFileInfo $image): object;

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
