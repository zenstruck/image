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

use Imagine\Filter\FilterInterface as ImagineFilter;
use Imagine\Gd\Image as GdImagineImage;
use Imagine\Gmagick\Image as GmagickImagineImage;
use Imagine\Image\ImageInterface as ImagineImage;
use Imagine\Imagick\Image as ImagickImagineImage;
use Intervention\Image\Filters\FilterInterface as InterventionFilter;
use Intervention\Image\Image as InterventionImage;
use Intervention\Image\Interfaces\ImageInterface as InterventionImageInterface;
use Intervention\Image\Interfaces\ModifierInterface as InterventionModifier;
use Psr\Container\ContainerInterface;
use Spatie\Image\Image as SpatieImage;
use Zenstruck\Image\Transformer;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @internal
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

    public function transform(\SplFileInfo $image, callable|object $filter, array $options = []): \SplFileInfo
    {
        if ($filter instanceof ImagineFilter) {
            return $this->get(ImagineImage::class)->transform($image, $filter, $options);
        }

        if ($filter instanceof InterventionFilter || $filter instanceof InterventionModifier) { // @phpstan-ignore-line
            return $this->get(InterventionImage::class)->transform($image, $filter, $options);
        }

        if (!\is_callable($filter)) {
            throw new \LogicException('Filter is not callable.');
        }

        $ref = new \ReflectionFunction($filter instanceof \Closure ? $filter : \Closure::fromCallable($filter));
        $type = ($ref->getParameters()[0] ?? null)?->getType();

        if (!$type instanceof \ReflectionNamedType) {
            throw new \LogicException('Filter callback must have a single typed argument (union/intersection arguments are not allowed).');
        }

        $type = $type->getName();

        if (!\class_exists($type) && !\interface_exists($type)) {
            throw new \LogicException(\sprintf('First parameter type "%s" for filter callback is not a valid class/interface.', $type ?: '(none)'));
        }

        return $this->get($type)->transform($image, $filter, $options);
    }

    /**
     * @template T of object
     *
     * @param class-string<T>|null $class
     *
     * @return T
     */
    public function object(\SplFileInfo $image, ?string $class = null): object
    {
        if (!$class) {
            throw new \InvalidArgumentException(\sprintf('A class name must be provided when using %s().', __METHOD__));
        }

        return $this->get($class)->object($image);
    }

    /**
     * @template T of object
     *
     * @param class-string<T> $class
     *
     * @return Transformer<T>
     */
    private function get(string $class): Transformer
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
            InterventionImage::class, InterventionImageInterface::class => new InterventionTransformer(),
            SpatieImage::class => new SpatieImageTransformer(),
            default => throw new \InvalidArgumentException(\sprintf('No transformer available for "%s".', $class)),
        };
    }
}
