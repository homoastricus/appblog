<?php

namespace App\Core\Service;

class ValidationManager
{

    private static array $errors = [
        'min_length' => "длина строки меньше минимально возможной ",
        'max_length' => "длина строки больше максимально возможной "
    ];

    public static function validate(array $rules, string $field): array
    {
        $result = true;
        $errors = [];
        foreach ($rules as $rule => $value) {
            if ($rule == "trim" && $value) {
                $field = trim($field);
            }
            if ($rule == "min_length") {
                if (!self::min_length($value, $field)) {
                    $result = false;
                    $errors[] = self::$errors['min_length'] . $value;
                }
            }
            if ($rule == "max_length") {
                if (!self::max_length($value, $field)) {
                    $result = false;
                    $errors[] = self::$errors['max_length'] . $value;
                }
            }
        }
        return ['result' => $result, 'errors' => $errors];
    }

    private static function min_length($value, $field): bool
    {
        return mb_strlen($field) >= $value;
    }

    private static function max_length($value, $field): bool
    {
        return mb_strlen($field) <= $value;
    }
}