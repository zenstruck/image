<?php

/*
 * This file is part of the zenstruck/image package.
 *
 * (c) Kevin Bond <kevinbond@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zenstruck\Image\Tests;

use PHPUnit\Framework\TestCase;
use Zenstruck\Image\Dimensions;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class DimensionsTest extends TestCase
{
    /**
     * @test
     */
    public function can_create_from_array(): void
    {
        $this->assertSame(['width' => 55, 'height' => 22], (new Dimensions([55, '22']))->jsonSerialize());
        $this->assertSame(['width' => 55, 'height' => 22], (new Dimensions(['width' => 55, 'height' => '22']))->jsonSerialize());
        $this->assertSame(['width' => 55, 'height' => 22], (new Dimensions(['height' => '22', 'width' => 55]))->jsonSerialize());
    }

    /**
     * @test
     */
    public function invalid_width(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new Dimensions([]))->width();
    }

    /**
     * @test
     */
    public function invalid_height(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new Dimensions([]))->height();
    }
}
