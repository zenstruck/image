<?php

namespace Zenstruck\Image;

use Zenstruck\Image;
use Zenstruck\TempFile;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class LocalImage extends \SplFileInfo implements Image
{
    use CalculatedProperties;

    private const MIME_EXTENSION_MAP = [
        'image/jpeg' => 'jpg',
        'image/gif' => 'gif',
        'image/svg+xml' => 'svg',
        'image/png' => 'png',
        'image/bmp' => 'bmp',
        'image/webp' => 'webp',
        'image/vnd.wap.wbmp' => 'wbmp',
    ];

    private static TransformerRegistry $transformerRegistry;

    /** @var mixed[] */
    private array $imageMetadata;

    /** @var array<string,string> */
    private array $iptc;

    /** @var array<string,string> */
    private array $exif;

    public static function wrap(self|string $file): self
    {
        return $file instanceof self ? $file : new self($file);
    }

    /**
     * Create a temporary image file that's deleted at the end of the script.
     *
     * @param string|resource|\SplFileInfo $what
     */
    public static function from(mixed $what): self
    {
        return new self(TempFile::for($what));
    }

    public function transform(object|callable $filter, array $options = []): self
    {
        /** @var self $transformed */
        $transformed = self::transformerRegistry()->transform($this, $filter, $options);

        return $transformed->refresh();
    }

    /**
     * @template T of object
     *
     * @param object|callable(T):T $filter
     */
    public function transformInPlace(object|callable $filter, array $options = []): self
    {
        return $this->transform($filter, \array_merge($options, ['output' => $this]));
    }

    /**
     * @template T of object
     *
     * @param class-string<T> $class
     *
     * @return T
     */
    public function transformer(string $class): object
    {
        return self::transformerRegistry()->get($class)->object($this);
    }

    public function height(): int
    {
        return $this->imageMetadata()[1];
    }

    public function width(): int
    {
        return $this->imageMetadata()[0];
    }

    public function mimeType(): string
    {
        return $this->imageMetadata()['mime'] ?? throw new \RuntimeException(\sprintf('Unable to determine mime-type for "%s".', $this));
    }

    public function guessExtension(): string
    {
        return $this->getExtension() ?: self::MIME_EXTENSION_MAP[$this->mimeType()] ?? throw new \RuntimeException(\sprintf('Unable to guess extension for "%s".', $this));
    }

    /**
     * @copyright Bulat Shakirzyanov <mallluhuct@gmail.com>
     * @source https://github.com/php-imagine/Imagine/blob/9b9aacbffadce8f19abeb992b8d8d3a90cc2a52a/src/Image/Metadata/ExifMetadataReader.php
     */
    public function exif(): array
    {
        if (!\function_exists('exif_read_data')) {
            throw new \LogicException('exif extension is not available.');
        }

        if (isset($this->exif)) {
            return $this->exif;
        }

        if (false === $data = @\exif_read_data($this, as_arrays: true)) {
            throw new \RuntimeException(\sprintf('Unable to parse EXIF data for "%s".', $this));
        }

        $ret = [];

        foreach ($data as $section => $values) {
            if (!\is_array($values)) {
                continue;
            }

            if (array_is_list($values)) {
                $ret[\mb_strtolower($section)] = \implode("\n", $values);

                continue;
            }

            foreach ($values as $key => $value) {
                $ret[\sprintf('%s.%s', \mb_strtolower($section), $key)] = $value;
            }
        }

        return $this->exif = $ret;
    }

    /**
     * @copyright Oliver Vogel
     * @source https://github.com/Intervention/image/blob/54934ae8ea3661fd189437df90fb09ec3b679c74/src/Intervention/Image/Commands/IptcCommand.php
     */
    public function iptc(): array
    {
        if (isset($this->iptc)) {
            return $this->iptc;
        }

        if (!\array_key_exists('APP13', $info = $this->imageMetadata())) {
            return $this->iptc = [];
        }

        if (false === $iptc = \iptcparse($info['APP13'])) {
            throw new \RuntimeException(\sprintf('Unable to parse IPTC data for "%s".', $this));
        }

        return $this->iptc = \array_filter([
            'DocumentTitle' => $iptc['2#005'][0] ?? null,
            'Urgency' => $iptc['2#010'][0] ?? null,
            'Category' => $iptc['2#015'][0] ?? null,
            'Subcategories' => $iptc['2#020'][0] ?? null,
            'Keywords' => $iptc['2#025'][0] ?? null,
            'ReleaseDate' => $iptc['2#030'][0] ?? null,
            'ReleaseTime' => $iptc['2#035'][0] ?? null,
            'SpecialInstructions' => $iptc['2#040'][0] ?? null,
            'CreationDate' => $iptc['2#055'][0] ?? null,
            'CreationTime' => $iptc['2#060'][0] ?? null,
            'AuthorByline' => $iptc['2#080'][0] ?? null,
            'AuthorTitle' => $iptc['2#085'][0] ?? null,
            'City' => $iptc['2#090'][0] ?? null,
            'SubLocation' => $iptc['2#092'][0] ?? null,
            'State' => $iptc['2#095'][0] ?? null,
            'Country' => $iptc['2#101'][0] ?? null,
            'OTR' => $iptc['2#103'][0] ?? null,
            'Headline' => $iptc['2#105'][0] ?? null,
            'Source' => $iptc['2#110'][0] ?? null,
            'PhotoSource' => $iptc['2#115'][0] ?? null,
            'Copyright' => $iptc['2#116'][0] ?? null,
            'Caption' => $iptc['2#120'][0] ?? null,
            'CaptionWriter' => $iptc['2#122'][0] ?? null,
        ]);
    }

    public function refresh(): static
    {
        \clearstatcache(filename: $this);

        unset($this->imageMetadata, $this->exif, $this->iptc);

        return $this;
    }

    public function delete(): void
    {
        if (\file_exists($this)) {
            \unlink($this);
        }
    }

    private static function transformerRegistry(): TransformerRegistry
    {
        return self::$transformerRegistry ??= new TransformerRegistry();
    }

    /**
     * @return mixed[]
     */
    private function imageMetadata(): array
    {
        if (isset($this->imageMetadata)) {
            return $this->imageMetadata;
        }

        if ('svg' === $this->getExtension()) {
            return $this->imageMetadata = self::parseSvg($this) ?? throw new \RuntimeException(\sprintf('Unable to load "%s" as svg.', $this));
        }

        $info = [];

        if (false === $imageMetadata = @\getimagesize($this, $info)) {
            // try as svg
            return $this->imageMetadata = self::parseSvg($this) ?? throw new \RuntimeException(\sprintf('Unable to parse image metadata for "%s".', $this));
        }

        return $this->imageMetadata = \array_merge($imageMetadata, $info);
    }

    /**
     * @return null|mixed[]
     */
    private static function parseSvg(\SplFileInfo $file): ?array
    {
        if (false === $xml = \file_get_contents($file)) {
            return null;
        }

        if (false === $xml = @\simplexml_load_string($xml)) {
            return null;
        }

        if (!$xml = $xml->attributes()) {
            return null;
        }

        return [
            (int) \round((float) $xml->width),
            (int) \round((float) $xml->height),
            'mime' => 'image/svg+xml',
        ];
    }
}
