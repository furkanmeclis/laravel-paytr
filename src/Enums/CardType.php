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

enum CardType: string
{
    case ADVANTAGE = 'advantage';
    case AXESS = 'axess';
    case COMBO = 'combo';
    case BONUS = 'bonus';
    case CARDFINANS = 'cardfinans';
    case MAXIMUM = 'maximum';
    case PARAF = 'paraf';
    case WORLD = 'world';
    case SAGLAMKART = 'saglamkart';
}
