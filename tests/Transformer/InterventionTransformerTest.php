<?php

/*
 * This file is part of the zenstruck/image package.
 *
 * (c) Kevin Bond <kevinbond@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zenstruck\Image\Tests\Transformer;

use Intervention\Image\Filters\FilterInterface;
use Intervention\Image\Image as InterventionImage;
use Intervention\Image\Interfaces\ImageInterface;
use Intervention\Image\Interfaces\ModifierInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class InterventionTransformerTest extends FilterObjectTransformerTestCase
{
    protected function filterCallback(): callable
    {
        if (\interface_exists(ModifierInterface::class)) {
            return fn(InterventionImage $image) => $image->scale(100);
        }

        return fn(InterventionImage $image) => $image->widen(100);
    }

    protected function filterInvokable(): object
    {
        if (\interface_exists(ModifierInterface::class)) {
            return new class() {
                public function __invoke(ImageInterface $image): ImageInterface
                {
                    return $image->scale(100);
                }
            };
        }

        return new class() {
            public function __invoke(InterventionImage $image): InterventionImage
            {
                return $image->widen(100);
            }
        };
    }

    /**
     * @return FilterInterface|ModifierInterface
     */
    protected function filterObject(): object
    {
        if (\interface_exists(ModifierInterface::class)) {
            return new class() implements ModifierInterface {
                public function apply(ImageInterface $image): ImageInterface
                {
                    return $image->scale(100);
                }
            };
        }

        return new class() implements FilterInterface {
            public function applyFilter(InterventionImage $image): InterventionImage
            {
                return $image->widen(100);
            }
        };
    }

    protected function invalidFilterCallback(): callable
    {
        return fn(InterventionImage $image) => null;
    }
}
