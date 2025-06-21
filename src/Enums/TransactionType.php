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

enum TransactionType: string
{
    case DIRECT = 'DIRECT';
    case IFRAME = 'IFRAME';
    case IFRAME_TRANSFER = 'IFRAME_TRANSFER';
}
