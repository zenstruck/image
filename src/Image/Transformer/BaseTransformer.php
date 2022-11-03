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

        $this->doTransform($image, $manipulator, $options);

        return Image::wrap($output)->refresh();
    }

    /**
     * @param array{format:string,output:string}|array<string,mixed> $options
     */
    abstract protected function doTransform(Image $image, callable $manipulator, array $options): void;
}
