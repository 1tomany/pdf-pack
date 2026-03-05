<?php

namespace OneToMany\PdfPack\Contract\Enum;

enum OutputType
{
    case Jpeg;
    case Png;
    case Text;

    /**
     * @return non-empty-string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return non-empty-lowercase-string
     */
    public function getExtension(): string
    {
        $extension = match ($this) {
            self::Jpeg => 'jpeg',
            self::Png => 'png',
            self::Text => 'txt',
        };

        return $extension;
    }

    /**
     * @return non-empty-lowercase-string
     */
    public function getFormat(): string
    {
        $mimeType = match ($this) {
            self::Jpeg => 'image/jpeg',
            self::Png => 'image/png',
            self::Text => 'text/plain',
        };

        return $mimeType;
    }

    /**
     * @phpstan-assert-if-true self::Jpeg $this
     */
    public function isJpeg(): bool
    {
        return self::Jpeg === $this;
    }

    /**
     * @phpstan-assert-if-true self::Png $this
     */
    public function isPng(): bool
    {
        return self::Png === $this;
    }

    /**
     * @phpstan-assert-if-true self::Text $this
     */
    public function isText(): bool
    {
        return self::Text === $this;
    }
}
