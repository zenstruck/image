# zenstruck/image

[![CI Status](https://github.com/zenstruck/image/workflows/CI/badge.svg)](https://github.com/zenstruck/image/actions?query=workflow%3ACI)
[![codecov](https://codecov.io/gh/zenstruck/image/branch/1.x/graph/badge.svg?token=MBKSCPO6U5)](https://codecov.io/gh/zenstruck/image)

Image file wrapper to provide image-specific metadata and transformations.

## Installation

```bash
composer require zenstruck/image
```

## Usage

> [!NOTE]
> `Zenstruck\ImageFileInfo` extends `\SplFileInfo`.

```php
use Zenstruck\ImageFileInfo;

$image = ImageFileInfo::wrap('some/local.jpg'); // create from local file
$image = ImageFileInfo::from($resource); // create from resource/stream (in a temp file)

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

> [!NOTE]
> Images created with `ImageFileInfo::from()` are created in unique temporary files
> and deleted at the end of the script.

### Transformations

The following transformers are available:

- [GD](https://www.php.net/manual/en/book.image.php)
- [Imagick](https://www.php.net/manual/en/book.imagick.php)
- [intervention\image](https://github.com/Intervention/image)
- [imagine\imagine](https://github.com/php-imagine/Imagine)
- [spatie\image](https://github.com/spatie/image)

To use the desired transformer, type-hint the first parameter of the callable
passed to `Zenstruck\ImageFileInfo::transform()` with the desired transformer's
_image object_:

- **GD**: `\GdImage`
- **Imagick**: `\Imagick`
- **intervention\image**: `Intervention\Image\Image`
- **imagine\imagine**: `Imagine\Image\ImageInterface`
- **spatie\image**: `Spatie\Image\Image`

> [!NOTE]
> The return value of the callable must be the same as the passed parameter.

The following example uses `\GdImage` but any of the above type-hints can be used.

```php
/** @var Zenstruck\ImageFileInfo $image */

$resized = $image->transform(function(\GdImage $image): \GdImage {
    // perform desired manipulations...

    return $image;
}); // a new temporary Zenstruck\ImageFileInfo instance (deleted at the end of the script)

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
/** @var Zenstruck\ImageFileInfo $image */

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
/** @var Intervention\Image\Filters\FilterInterface|Intervention\Image\Interfaces\ModifierInterface $interventionFilter */
/** @var Zenstruck\ImageFileInfo $image */

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
/** @var Zenstruck\ImageFileInfo $image */

$thumbnail = $image->transform(new GreyscaleThumbnail(200, 200));

$image->transformInPlace(new GreyscaleThumbnail(200, 200));
```
