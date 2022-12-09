<?php

namespace Zenstruck\Image;

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
    public function transform(\SplFileInfo $image, callable $filter, array $options = []): \SplFileInfo;

    /**
     * @return T
     */
    public function object(\SplFileInfo $image): object;
}
