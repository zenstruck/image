<?php

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
abstract class BaseTransformer implements Transformer
{
    final public function transform(\SplFileInfo $image, callable $manipulator, array $options = []): Image
    {
        $image = Image::wrap($image);
        $options['format'] ??= $image->guessExtension();
        $output = $options['output'] ??= TempFile::withExtension($options['format']);
        $options['output'] = (string) $options['output'];

        $transformed = $manipulator($this->object($image));

        if (!\is_a($transformed, static::expectedClass())) {
            throw new \LogicException(\sprintf('Manipulator callback must return a "%s" object.', static::expectedClass()));
        }

        $this->save($transformed, $options);

        return Image::wrap($output)->refresh();
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
