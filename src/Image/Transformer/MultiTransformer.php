<?php

namespace Zenstruck\Image\Transformer;

use Imagine\Gd\Image as GdImagineImage;
use Imagine\Gmagick\Image as GmagickImagineImage;
use Imagine\Image\ImageInterface as ImagineImage;
use Imagine\Imagick\Image as ImagickImagineImage;
use Intervention\Image\Image as InterventionImage;
use Psr\Container\ContainerInterface;
use Zenstruck\Image;
use Zenstruck\Image\Transformer;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @implements Transformer<object>
 */
final class MultiTransformer implements Transformer
{
    /** @var array<class-string,Transformer<object>> */
    private static array $defaultTransformers = [];

    /**
     * @param array<class-string,Transformer<object>>|ContainerInterface $transformers
     */
    public function __construct(private array|ContainerInterface $transformers = [])
    {
    }

    public function transform(\SplFileInfo $image, callable $manipulator, array $options = []): Image
    {
        $ref = new \ReflectionFunction($manipulator instanceof \Closure ? $manipulator : \Closure::fromCallable($manipulator));
        $type = ($ref->getParameters()[0] ?? null)?->getType();

        if (!$type instanceof \ReflectionNamedType) {
            throw new \LogicException('Manipulator callback must have a single typed argument (union/intersection arguments are not allowed).');
        }

        $type = $type->getName();

        if (!\class_exists($type) && !\interface_exists($type)) {
            throw new \LogicException(\sprintf('First parameter type "%s" for manipulator callback is not a valid class/interface.', $type ?: '(none)'));
        }

        return $this->get($type)->transform($image, $manipulator, $options);
    }

    public function object(\SplFileInfo $image): object
    {
        throw new \BadMethodCallException();
    }

    /**
     * @template T of object
     *
     * @param class-string<T> $class
     *
     * @return Transformer<T>
     */
    public function get(string $class): Transformer
    {
        if (\is_array($this->transformers) && isset($this->transformers[$class])) {
            return $this->transformers[$class]; // @phpstan-ignore-line
        }

        if ($this->transformers instanceof ContainerInterface && $this->transformers->has($class)) {
            return $this->transformers->get($class);
        }

        return self::defaultTransformer($class);
    }

    /**
     * @template T of object
     *
     * @param class-string<T> $class
     *
     * @return Transformer<T>
     */
    private static function defaultTransformer(string $class): Transformer
    {
        return self::$defaultTransformers[$class] ??= match ($class) { // @phpstan-ignore-line
            \GdImage::class => new GdImageTransformer(),
            \Imagick::class => new ImagickTransformer(),
            ImagineImage::class, GdImagineImage::class, ImagickImagineImage::class, GmagickImagineImage::class => ImagineTransformer::createFor($class),
            InterventionImage::class => new InterventionTransformer(),
            default => throw new \InvalidArgumentException(\sprintf('No transformer available for "%s".', $class)),
        };
    }
}
