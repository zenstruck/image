<?php

namespace Zenstruck\Image\Transformer;

use Zenstruck\Image;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 * @author Jakub Caban <kuba.iluvatar@gmail.com>
 *
 * @extends BaseTransformer<\Imagick>
 */
final class ImagickTransformer extends BaseTransformer
{
    public function __construct()
    {
        if (!\class_exists(\Imagick::class)) {
            throw new \LogicException('Imagick extension not available.');
        }
    }

    protected function doTransform(Image $image, callable $manipulator, array $options): void
    {
        $imagick = new \Imagick();
        $imagick->readImage((string) $image);

        $imagick = $manipulator($imagick);

        if (!$imagick instanceof \Imagick) {
            throw new \LogicException('Manipulator callback must return an Imagick object.');
        }

        $imagick->setImageFormat($options['format']);
        $imagick->writeImage($options['output']);
    }
}
