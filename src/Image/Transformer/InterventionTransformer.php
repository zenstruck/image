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

use Intervention\Image\Filters\FilterInterface;
use Intervention\Image\Image as InterventionImage;
use Intervention\Image\ImageManager;
use Intervention\Image\ImageManagerStatic;
use Intervention\Image\Interfaces\ImageInterface;
use Intervention\Image\Interfaces\ModifierInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 * @author Jakub Caban <kuba.iluvatar@gmail.com>
 *
 * @internal
 *
 * @extends FileTransformer<InterventionImage|ImageInterface>
 */
final class InterventionTransformer extends FileTransformer
{
    public function __construct(private ?ImageManager $manager = null)
    {
        if (!\class_exists(ImageManager::class)) {
            throw new \LogicException('intervention/image required. Install with "composer require intervention/image".');
        }
    }

    public static function normalizeFilter(callable|object $filter): callable
    {
        if ($filter instanceof FilterInterface) { // @phpstan-ignore-line
            $filter = static fn(InterventionImage $i) => $i->filter($filter); // @phpstan-ignore-line
        }

        if ($filter instanceof ModifierInterface) {
            $filter = static fn(InterventionImage $i) => $i->modify($filter);
        }

        return parent::normalizeFilter($filter);
    }

    protected function object(\SplFileInfo $image): object
    {
        if (\interface_exists(ImageInterface::class)) {
            return $this->manager ? $this->manager->read($image) : ImageManager::gd()->read($image);
        }

        return $this->manager ? $this->manager->make($image) : ImageManagerStatic::make($image); // @phpstan-ignore-line
    }

    protected static function expectedClass(): string
    {
        if (\interface_exists(ImageInterface::class)) {
            return ImageInterface::class;
        }

        return InterventionImage::class;
    }

    protected function save(object $object, array $options): void
    {
        $object->save($options['output'], $options['quality'] ?? null, $options['format']);
    }
}
