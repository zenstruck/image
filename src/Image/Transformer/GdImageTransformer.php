<?php

namespace Zenstruck\Image\Transformer;

/**
 * @author Jakub Caban <kuba.iluvatar@gmail.com>
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @extends FileTransformer<\GdImage>
 */
final class GdImageTransformer extends FileTransformer
{
    public function __construct()
    {
        if (!\class_exists(\GdImage::class)) {
            throw new \LogicException('GD extension not available.');
        }
    }

    protected function createObject(\SplFileInfo $image): object
    {
        return @\imagecreatefromstring(\file_get_contents($image) ?: throw new \RuntimeException(\sprintf('Unable to read "%s".', $image))) ?: throw new \RuntimeException(\sprintf('Unable to create GdImage for "%s".', $image));
    }

    protected static function expectedClass(): string
    {
        return \GdImage::class;
    }

    protected function save(object $object, array $options): void
    {
        /** @var string&callable $function */
        $function = match ($options['format']) {
            'png' => 'imagepng',
            'jpg', 'jpeg' => 'imagejpeg',
            'gif' => 'imagegif',
            'webp' => 'imagewebp',
            'avif' => 'imageavif',
            default => throw new \LogicException(\sprintf('Image format "%s" is invalid.', $options['format'])),
        };

        if (!\function_exists($function)) {
            throw new \LogicException(\sprintf('The "%s" gd extension function is not available.', $function));
        }

        $function($object, $options['output']);
    }
}
