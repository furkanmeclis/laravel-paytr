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

enum PaymentType: string
{
    case CARD = 'card';
    case EFT = 'eft';
}
