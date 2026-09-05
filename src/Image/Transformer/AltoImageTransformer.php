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

use Alto\Image\Format;
use Alto\Image\Image;
use Alto\Image\Operation\OperationInterface;
use Alto\Image\Transform;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @internal
 *
 * @extends FileTransformer<Image>
 */
final class AltoImageTransformer extends FileTransformer
{
    public function __construct()
    {
        if (!\class_exists(Image::class)) {
            throw new \LogicException('alto/image required. Install with "composer require alto/image".');
        }
    }

    public static function normalizeFilter(callable|object $filter): callable
    {
        if ($filter instanceof OperationInterface) {
            $filter = static fn(Image $i) => $i->apply($filter);
        }

        if ($filter instanceof Transform) {
            $filter = static fn(Image $i) => $i->transformedBy($filter);
        }

        return parent::normalizeFilter($filter);
    }

    public function object(\SplFileInfo $image): object
    {
        return Image::open($image->getPathname());
    }

    protected static function expectedClass(): string
    {
        return Image::class;
    }

    protected function save(object $object, array $options): void
    {
        $object
            ->encode(Format::of($options['format']), $options['quality'] ?? null)
            ->save($options['output'])
        ;
    }
}
