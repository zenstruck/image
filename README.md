# zenstruck/image

[![CI Status](https://github.com/zenstruck/image/workflows/CI/badge.svg)](https://github.com/zenstruck/image/actions?query=workflow%3ACI)
[![codecov](https://codecov.io/gh/zenstruck/image/branch/1.x/graph/badge.svg?token=MBKSCPO6U5)](https://codecov.io/gh/zenstruck/image)

Image file wrapper to provide image-specific metadata and transformations.

## Installation

```bash
composer require zenstruck/image
```

## Usage

> **Note**: `Zenstruck\Image` extends `\SplFileInfo`.

```php
use Zenstruck\Image;

$image = Image::wrap('some/local.jpg'); // create from local file
$image = Image::from($resource); // create from resource/stream (in a temp file)

// dimensional information
$image->dimensions()->height(); // int
$image->dimensions()->width(); // int
$image->dimensions()->aspectRatio(); // float
$image->dimensions()->pixels(); // int
$image->dimensions()->isSquare(); // bool
$image->dimensions()->isLandscape(); // bool
$image->dimensions()->isPortrait(); // bool

// other metadata
$image->mimeType(); // string (ie "image/jpeg")
$image->guessExtension(); // string - the extension if available or guess from mime-type
$image->iptc(); // array - IPTC data (if the image supports)
$image->exif(); // array - EXIF data (if the image supports)

// utility
$image->refresh(); // self - clear any cached metadata
$image->delete(); // void - delete the image file

// access any \SplFileInfo methods
$image->getMTime();
```

> **Note**: images created with `Image::from()` are created in unique temporary files
> and deleted at the end of the script.

### Transformations

The following transformers are available:

- [GD](https://www.php.net/manual/en/book.image.php)
- [Imagick](https://www.php.net/manual/en/book.imagick.php)
- [intervention\image](https://github.com/Intervention/image)
- [imagine\imagine](https://github.com/php-imagine/Imagine)

To use the desired transformer, type-hint the first parameter of the callable
passed to `Zenstruck\Image::transform()` with the desired transformer's
_image object_:

- **GD**: `\GdImage`
- **Imagick**: `\Imagick`
- **intervention\image**: `Intervention\Image\Image`
- **imagine\imagine**: `Imagine\Image\ImageInterface`

> **Note**: The return value of the callable must be the same as the passed parameter.

The following example uses `\GdImage` but any of the above type-hints can be used.

```php
/** @var Zenstruck\Image $image */

$resized = $image->transform(function(\GdImage $image): \GdImage {
    // perform desired manipulations...

    return $image;
}); // a new temporary Zenstruck\Image instance (deleted at the end of the script)

// configure the format
$resized = $image->transform(
    function(\GdImage $image): \GdImage {
        // perform desired manipulations...

        return $image;
    },
    ['format' => 'png']
);

// configure the path for the created file
$resized = $image->transform(
    function(\GdImage $image): \GdImage {
        // perform desired manipulations...

        return $image;
    },
    ['output' => 'path/to/file.jpg']
);
```

#### Transform "In Place"

```php
/** @var Zenstruck\Image $image */

$resized = $image->transformInPlace(function(\GdImage $image): \GdImage {
    // perform desired manipulations...

    return $image;
}); // overwrites the original image file
```

#### Filter Objects

Both _Imagine_ and _Intervention_ have the concept of _filters_. These are objects
that can be passed directly to `transform()` and `transformInPlace()`:

```php
/** @var Imagine\Filter\FilterInterface $imagineFilter */
/** @var Intervention\Image\Filters\FilterInterface $interventionFilter */
/** @var Zenstruck\Image $image */

$transformed = $image->transform($imagineFilter);
$transformed = $image->transform($interventionFilter);

$image->transformInPlace($imagineFilter);
$image->transformInPlace($interventionFilter);
```

##### Custom Filter Objects

Because `transform()` and `transformInPlace()` accept any callable, you can wrap complex
transformations into invokable _filter objects_:

```php
class GreyscaleThumbnail
{
    public function __construct(private int $width, private int $height)
    {
    }

    public function __invoke(\GdImage $image): \GdImage
    {
        // greyscale and resize to $this->width/$this->height

        return $image;
    }
}
```

To use, pass a new instance to `transform()` or `transformInPlace()`:

```php
/** @var Zenstruck\Image $image */

$thumbnail = $image->transform(new GreyscaleThumbnail(200, 200));

$image->transformInPlace(new GreyscaleThumbnail(200, 200));
```

#### Transformation Object

`Zenstruck\Image::transformer()` returns a new instance of the desired
transformation library's _image object_:

```php
use Imagine\Image\ImageInterface;

/** @var Zenstruck\Image $image */

$image->transformer(ImageInterface::class); // ImageInterface object for this image
$image->transformer(\Imagick::class); // \Imagick object for this image
```
