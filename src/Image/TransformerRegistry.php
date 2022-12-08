<?php

namespace Zenstruck\Image;

use Imagine\Filter\FilterInterface as ImagineFilter;
use Imagine\Gd\Image as GdImagineImage;
use Imagine\Gmagick\Image as GmagickImagineImage;
use Imagine\Image\ImageInterface as ImagineImage;
use Imagine\Imagick\Image as ImagickImagineImage;
use Intervention\Image\Filters\FilterInterface as InterventionFilter;
use Intervention\Image\Image as InterventionImage;
use Psr\Container\ContainerInterface;
use Zenstruck\Image;
use Zenstruck\Image\Transformer\GdImageTransformer;
use Zenstruck\Image\Transformer\ImagickTransformer;
use Zenstruck\Image\Transformer\ImagineTransformer;
use Zenstruck\Image\Transformer\InterventionTransformer;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class TransformerRegistry
{
    /** @var array<class-string,Transformer<object>> */
    private static array $defaultTransformers = [];

    /**
     * @param array<class-string,Transformer<object>>|ContainerInterface $transformers
     */
    public function __construct(private array|ContainerInterface $transformers = [])
    {
    }

    public function transform(\SplFileInfo $image, object|callable $filter, array $options = []): Image
    {
        if ($filter instanceof ImagineFilter) {
            return $this->get(ImagineImage::class)->transform(
                $image,
                static fn(ImagineImage $i) => $filter->apply($i),
                $options
            );
        }

        if ($filter instanceof InterventionFilter) {
            return $this->get(InterventionImage::class)->transform(
                $image,
                static fn(InterventionImage $i) => $i->filter($filter),
                $options
            );
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
