<?php

namespace OneToMany\PdfPack\Contract\Enum;

enum OutputType
{
    case Jpg;
    case Png;
    case Txt;

    public function getExtension(): string
    {
        $extension = match ($this) {
            self::Jpg => 'jpeg',
            self::Png => 'png',
            self::Txt => 'txt',
        };

        return $extension;
    }

    public function getMimeType(): string
    {
        $mimeType = match ($this) {
            self::Jpg => 'image/jpeg',
            self::Png => 'image/png',
            self::Txt => 'text/plain',
        };

        return $mimeType;
    }

    public function isJpg(): bool
    {
        return self::Jpg === $this;
    }

    public function isPng(): bool
    {
        return self::Png === $this;
    }

    public function isTxt(): bool
    {
        return self::Txt === $this;
    }
}
