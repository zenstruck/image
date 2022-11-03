<?php

namespace Zenstruck\Image\Transformer;

use Zenstruck\Image;

/**
 * @author Jakub Caban <kuba.iluvatar@gmail.com>
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @extends BaseTransformer<\GdImage>
 */
final class GdImageTransformer extends BaseTransformer
{
    public function __construct()
    {
        if (!\class_exists(\GdImage::class)) {
            throw new \LogicException('GD extension not available.');
        }
    }

    protected function doTransform(Image $image, callable $manipulator, array $options): void
    {
        $gdImage = $manipulator(@\imagecreatefromstring(\file_get_contents($image) ?: throw new \RuntimeException(\sprintf('Unable to read "%s".', $image))) ?: throw new \RuntimeException(\sprintf('Unable to create GdImage for "%s".', $image)));
        if (!$gdImage instanceof \GdImage) {
            throw new \LogicException('Manipulator callback must return a GdImage object.');
        }

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

        $function($gdImage, $options['output']);
    }
}
