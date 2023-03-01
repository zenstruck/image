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

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @internal
 *
 * @template T of object
 */
interface Transformer
{
    /**
     * @param object|callable(T):T $filter
     */
    public function transform(\SplFileInfo $image, callable|object $filter, array $options = []): \SplFileInfo;
}
