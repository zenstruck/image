<?php

namespace Zenstruck\Image\Transformer;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 * @author Jakub Caban <kuba.iluvatar@gmail.com>
 *
 * @extends FileTransformer<\Imagick>
 */
final class ImagickTransformer extends FileTransformer
{
    public function __construct()
    {
        if (!\class_exists(\Imagick::class)) {
            throw new \LogicException('Imagick extension not available.');
        }
    }

    protected function createObject(\SplFileInfo $image): object
    {
        $imagick = new \Imagick();
        $imagick->readImage((string) $image);

        return $imagick;
    }

    protected static function expectedClass(): string
    {
        return \Imagick::class;
    }

    protected function save(object $object, array $options): void
    {
        $object->setImageFormat($options['format']);
        $object->writeImage($options['output']);
    }
}
