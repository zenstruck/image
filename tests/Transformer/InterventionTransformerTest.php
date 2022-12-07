<?php

namespace Zenstruck\Image\Tests\Transformer;

use Intervention\Image\Image as InterventionImage;
use Zenstruck\Image\Tests\TransformerTest;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class InterventionTransformerTest extends TransformerTest
{
    protected function filterCallback(): callable
    {
        return fn(InterventionImage $image) => $image->widen(100);
    }

    protected function invalidFilterCallback(): callable
    {
        return fn(InterventionImage $image) => null;
    }

    protected function objectClass(): string
    {
        return InterventionImage::class;
    }
}
