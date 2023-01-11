<?php

/*
 * This file is part of the zenstruck/image package.
 *
 * (c) Kevin Bond <kevinbond@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zenstruck\Image;

use Zenstruck\Image;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @template T of object
 */
interface Transformer
{
    /**
     * @param callable(T):T $filter
     */
    public function transform(Image $image, callable $filter, array $options = []): Image;

    /**
     * @return T
     */
    public function object(Image $image): object;
}
