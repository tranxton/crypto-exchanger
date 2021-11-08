<?php

declare(strict_types=1);

namespace App\Helpers;

trait BCMathHelper
{
    /**
     * Сравнивает два числа
     *
     * @param string $num1
     * @param string $num2
     * @param int    $scale
     *
     * @return int
     */
    private static function compare(string $num1, string $num2, int $scale = 10): int
    {
        return bccomp($num1, $num2, $scale);
    }

    /**
     * Складывает два числа
     *
     * @param string $num1
     * @param string $num2
     * @param int    $scale
     *
     * @return string
     */
    private static function addition(string $num1, string $num2, int $scale = 10): string
    {
        return bcadd($num1, $num2, $scale);
    }

    /**
     * Вычитает второе число из первого
     *
     * @param string $num1
     * @param string $num2
     * @param int    $scale
     *
     * @return string
     */
    private static function subtraction(string $num1, string $num2, int $scale = 10): string
    {
        return bcsub($num1, $num2, $scale);
    }

    /**
     * Перемножает переданные числа
     *
     * @param string $num1
     * @param string $num2
     * @param int    $scale
     *
     * @return string
     */
    private static function multiplication(string $num1, string $num2, int $scale = 10): string
    {
        return bcmul($num1, $num2, $scale);
    }
}
