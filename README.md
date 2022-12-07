# zenstruck/image

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

// general metadata
$image->height(); // int
$image->width(); // int
$image->aspectRatio(); // float
$image->pixels(); // int
$image->isSquare(); // bool
$image->isLandscape(); // bool
$image->isPortrait(); // bool
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
}); // overrides the original image file
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

### BlurHash

> **Warning**: Generating BlurHash strings is resource intensive. It is recommended to
> use some kind of cache if possible.

You can create [BlurHash](https://blurha.sh/) strings for your image:

```php
/** @var Zenstruck\Image $image */

(string) $image->blurHash(); // string (ie "LKN]Rv%2Tw=w]~RBVZRi};RPxuwH")

// customize the encoding
$image->blurHash()->encode(['width' => 100, 'height' => 100]); // string
```

#### BlurHash DataUri

If your frontend does not have the JS support for decoding blurhash strings, you
can generate a data-uri to use in your `<img src="...">` attributes.

```php
/** @var Zenstruck\Image $image */

$image->blurHash()->dataUri() // string - "data:image/jpeg;base64,...";

// create an img tag
$tag = \sprintf(
    '<img src="%s" height="%s" width="%s" />',
    $image->blurHash()->dataUri(),
    $image->height(),
    $image->width()
);
```
