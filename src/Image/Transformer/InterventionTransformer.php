<?php

namespace Zenstruck\Image\Transformer;

use Intervention\Image\Image as InterventionImage;
use Intervention\Image\ImageManager;
use Intervention\Image\ImageManagerStatic;
use Zenstruck\Image;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 * @author Jakub Caban <kuba.iluvatar@gmail.com>
 *
 * @extends BaseTransformer<InterventionImage>
 */
final class InterventionTransformer extends BaseTransformer
{
    public function __construct(private ?ImageManager $manager = null)
    {
    }

    protected function doTransform(Image $image, callable $manipulator, array $options): void
    {
        $interventionImage = $this->manager ? $this->manager->make($image) : ImageManagerStatic::make($image);

        $interventionImage = $manipulator($interventionImage);

        if (!$interventionImage instanceof InterventionImage) {
            throw new \LogicException('Manipulator callback must return an Intervention\Image object.');
        }

        $interventionImage->save($options['output'], $options['quality'] ?? null, $options['format']);
    }
}
