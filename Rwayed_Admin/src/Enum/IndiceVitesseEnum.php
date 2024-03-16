<?php

namespace App\Enum;

enum IndiceVitesseEnum: string
{
    case H = 'H';
    case Q = 'Q';
    case R = 'R';
    case T = 'T';
    case V = 'V';
    case W = 'W';
    case Y = 'Y';
    case Z = 'Z';

    public static function getChoices(): array
    {
        $choices = [];
        foreach (self::cases() as $case) {
            $choices[$case->name] = $case->value;
        }
        return $choices;
    }
}