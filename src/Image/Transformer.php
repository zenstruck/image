<?php

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
     * @param callable(T):T $manipulator
     */
    public function transform(\SplFileInfo $image, callable $manipulator, array $options = []): Image;
}
