<?php

namespace Zenstruck\Image\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Zenstruck\Image;
use Zenstruck\Image\Transformer;
use Zenstruck\Image\TransformerRegistry;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class TransformerRegistryTest extends TestCase
{
    /**
     * @test
     */
    public function callable_must_have_parameter(): void
    {
        $this->expectException(\LogicException::class);

        $this->image()->transform(fn() => null);
    }

    /**
     * @test
     */
    public function callable_parameter_must_have_type(): void
    {
        $this->expectException(\LogicException::class);

        $this->image()->transform(fn($foo) => null);
    }

    /**
     * @test
     */
    public function callable_parameter_type_must_be_object(): void
    {
        $this->expectException(\LogicException::class);

        $this->image()->transform(fn(int $foo) => null);
    }

    /**
     * @test
     */
    public function invalid_transformer_type(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->image()->transform(fn(\stdClass $c) => null);
    }

    /**
     * @test
     */
    public function can_provide_transformers_as_array(): void
    {
        $transformer = new TransformerRegistry([
            \stdClass::class => new MockTransformer(),
        ]);

        $resized = $transformer->transform($this->image(), fn(\stdClass $c) => null);

        $this->assertSame(__FILE__, (string) $resized);
    }

    /**
     * @test
     */
    public function can_provide_transformers_as_container(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())->method('has')->with(\stdClass::class)->willReturn(true);
        $container->expects($this->once())->method('get')->with(\stdClass::class)->willReturn(new MockTransformer());

        $transformer = new TransformerRegistry($container);

        $resized = $transformer->transform($this->image(), fn(\stdClass $c) => null);

        $this->assertSame(__FILE__, (string) $resized);
    }

    private function image(): Image
    {
        return Image::from(new \SplFileInfo(__DIR__.'/Fixture/files/symfony.jpg'));
    }
}

class MockTransformer implements Transformer
{
    public function transform(Image $image, object|callable $filter, array $options = []): Image
    {
        return new Image(__FILE__);
    }

    public function object(Image $image): object
    {
        return new \stdClass();
    }
}
