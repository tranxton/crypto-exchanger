<?php

declare(strict_types=1);

namespace App\Helpers;


trait FloatHelper
{
    /**
     * Конвертирует FLOAT в STRING
     *
     * @param float $float
     *
     * @return string
     */
    private function toString(float $float): string
    {
        $value = (string) $float;
        if (($e = strrchr($value, 'E')) === false) {
            return $value;
        }

        return number_format((float) $value, -(int) substr($e, 1));
    }
}
