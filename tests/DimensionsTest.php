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
        $this->assertSame(['width' => 55, 'height' => 22], Dimensions::fromArray([55, '22'])->jsonSerialize());
        $this->assertSame(['width' => 55, 'height' => 22], Dimensions::fromArray(['width' => 55, 'height' => '22'])->jsonSerialize());
        $this->assertSame(['width' => 55, 'height' => 22], Dimensions::fromArray(['height' => '22', 'width' => 55])->jsonSerialize());
    }

    /**
     * @test
     */
    public function from_array_invalid_width(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Dimensions::fromArray([]);
    }

    /**
     * @test
     */
    public function from_array_invalid_height(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Dimensions::fromArray([22]);
    }
}
