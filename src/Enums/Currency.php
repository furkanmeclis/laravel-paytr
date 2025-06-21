<?php
/**
 * Laravel Paytr
 *
 * @author    Furkan Meclis
 * @copyright 2024 Furkan Meclis
 * @license   MIT
 * @link      https://github.com/furkanmeclis/laravel-paytr
 */

namespace FurkanMeclis\Paytr\Enums;

enum Currency: string
{
    case TL = 'TL';
    case EUR = 'EUR';
    case USD = 'USD';
    case GBP = 'GBP';
    case RUB = 'RUB';
}