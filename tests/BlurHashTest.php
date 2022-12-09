<?php

namespace Zenstruck\Image\Tests;

use PHPUnit\Framework\TestCase;
use Zenstruck\Image\LocalImage;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class BlurHashTest extends TestCase
{
    /**
     * @test
     */
    public function can_encode(): void
    {
        $blurHash = LocalImage::wrap(__DIR__.'/Fixture/files/symfony.jpg')->blurHash();

        $this->assertSame('LcMQ*Jj[~qWB4nWB-;j[-;WBWBxu', $blurHash->encode());
        $this->assertSame($blurHash->encode(), (string) $blurHash);
    }

    /**
     * @test
     */
    public function can_create_data_uri(): void
    {
        $dataUri = LocalImage::wrap(__DIR__.'/Fixture/files/symfony.jpg')->blurHash()->dataUri();

        $this->assertSame(
            'data:image/jpeg;base64,/9j/4AAQSkZJRgABAQEAYABgAAD//gA7Q1JFQVRPUjogZ2QtanBlZyB2MS4wICh1c2luZyBJSkcgSlBFRyB2ODApLCBxdWFsaXR5ID0gODAK/9sAQwAGBAUGBQQGBgUGBwcGCAoQCgoJCQoUDg8MEBcUGBgXFBYWGh0lHxobIxwWFiAsICMmJykqKRkfLTAtKDAlKCko/9sAQwEHBwcKCAoTCgoTKBoWGigoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgo/8AAEQgASwA/AwEiAAIRAQMRAf/EAB8AAAEFAQEBAQEBAAAAAAAAAAABAgMEBQYHCAkKC//EALUQAAIBAwMCBAMFBQQEAAABfQECAwAEEQUSITFBBhNRYQcicRQygZGhCCNCscEVUtHwJDNicoIJChYXGBkaJSYnKCkqNDU2Nzg5OkNERUZHSElKU1RVVldYWVpjZGVmZ2hpanN0dXZ3eHl6g4SFhoeIiYqSk5SVlpeYmZqio6Slpqeoqaqys7S1tre4ubrCw8TFxsfIycrS09TV1tfY2drh4uPk5ebn6Onq8fLz9PX29/j5+v/EAB8BAAMBAQEBAQEBAQEAAAAAAAABAgMEBQYHCAkKC//EALURAAIBAgQEAwQHBQQEAAECdwABAgMRBAUhMQYSQVEHYXETIjKBCBRCkaGxwQkjM1LwFWJy0QoWJDThJfEXGBkaJicoKSo1Njc4OTpDREVGR0hJSlNUVVZXWFlaY2RlZmdoaWpzdHV2d3h5eoKDhIWGh4iJipKTlJWWl5iZmqKjpKWmp6ipqrKztLW2t7i5usLDxMXGx8jJytLT1NXW19jZ2uLj5OXm5+jp6vLz9PX29/j5+v/aAAwDAQACEQMRAD8A+hLg/Ka5jWD8rV0Nw/y1zerHKtQB5/4g/irhrz/Wmu51/wDirg9QOJDQBq6O3zCu/wBEbha820mXDiu/0OXhaAO+048CtqI/LXP6Y+VFbsJ+WgCrNJ8tYWpnKmtWVuKyL/kGgDiNcXIauB1VcOa9H1iPIauE1iE7jQBnabLtkFd7oE2dvNefWqFZRXbaAT8tAHpWkvlVro4G+WuV0c/KtdNbn5RQBUccVnXaZBraaLiqVzFwaAOO1SHIPFcdqtpknivRr+DOeK5q/s8k8UAcLHaESdK6jRISCvFIth8/StvS7TaRxQB0mkJgLXS24+WsbTIcAV0ECfLQBM0PHSqdxDwa3Xi4qlcRdaAOYu4M54rFubTJPFdZcxdaz5LfJ6UAc0tj83StOys8EcVopa89Kv21tjHFAC2UGAOK1oo/lplvDjHFXUTigC+6cVRuU61pv0qhcd6AMi4TrVNo+a0J+9VT1oASKIZq7DEKhiq7D2oAmiTFThaalSigD//Z',
            $dataUri
        );

        $image = LocalImage::from(\base64_decode(\mb_substr($dataUri, 23)));

        $this->assertSame(63, $image->width());
        $this->assertSame(75, $image->height());
    }
}
