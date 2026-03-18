<?php

namespace App\Enums;

enum LanguageEnum: string
{
    case English    = 'en';
    case German     = 'de';
    case French     = 'fr';
    case Spanish    = 'es';
    case Italian    = 'it';
    case Portuguese = 'pt';
    case Croatian   = 'hr';
    case Serbian    = 'sr';
    case Bosnian    = 'bs';
    case Slovenian  = 'sl';

    public function label(): string
    {
        return match ($this) {
            self::English    => 'English',
            self::German     => 'German',
            self::French     => 'French',
            self::Spanish    => 'Spanish',
            self::Italian    => 'Italian',
            self::Portuguese => 'Portuguese',
            self::Croatian   => 'Croatian',
            self::Serbian    => 'Serbian',
            self::Bosnian    => 'Bosnian',
            self::Slovenian  => 'Slovenian',
        };
    }

    public static function options(): array
    {
        return array_column(self::cases(), 'value', 'name');
    }
}

