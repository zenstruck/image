<?php

namespace Zenstruck\Image\Transformer;

use Intervention\Image\Image as InterventionImage;
use Intervention\Image\ImageManager;
use Intervention\Image\ImageManagerStatic;

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
        if (!\class_exists(ImageManager::class)) {
            throw new \LogicException('intervention/image required. Install with "composer require intervention/image".');
        }
    }

    public function object(\SplFileInfo $image): object
    {
        return $this->manager ? $this->manager->make($image) : ImageManagerStatic::make($image);
    }

    protected static function expectedClass(): string
    {
        return InterventionImage::class;
    }

    protected function save(object $object, array $options): void
    {
        $object->save($options['output'], $options['quality'] ?? null, $options['format']);
    }
}
