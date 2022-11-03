# zenstruck/image

Image file wrapper to provide image-specific metadata and transformations.

## Installation

```bash
composer require zenstruck/image
```

## Usage

> *Note*: `Zenstruck\Image` extends `\SplFileInfo`.

```php
use Zenstruck\Image;

$image = new Image('some/local.jpg'); // create from local file
$image = Image::from($resource); // create from resource/stream

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

// access any \SplFileInfo methods
$image->getMTime();
```
