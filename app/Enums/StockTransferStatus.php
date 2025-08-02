<?php

namespace App\Enums;

enum StockTransferStatus: string
{
    case NEW = 'new';
    case PREPARING = 'preparing';
    case READY = 'ready';
    case SHIPPING = 'shipping';
    case RECEIVED = 'received';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case RETURNING = 'returning';

    public function label(): string
    {
        return match ($this) {
            self::NEW => 'New',
            self::PREPARING => 'Preparing',
            self::READY => 'Ready',
            self::SHIPPING => 'Shipping',
            self::RECEIVED => 'Received',
            self::COMPLETED => 'Completed',
            self::CANCELLED => 'Cancelled',
            self::RETURNING => 'Returning',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::NEW => 'blue',
            self::PREPARING => 'yellow',
            self::READY => 'green',
            self::SHIPPING => 'purple',
            self::RECEIVED => 'indigo',
            self::COMPLETED => 'emerald',
            self::CANCELLED => 'red',
            self::RETURNING => 'orange',
        };
    }

    public static function getWorkflowTransitions(): array
    {
        return [
            self::NEW->value => [self::PREPARING->value, self::CANCELLED->value],
            self::PREPARING->value => [self::READY->value, self::CANCELLED->value],
            self::READY->value => [self::SHIPPING->value, self::CANCELLED->value],
            self::SHIPPING->value => [self::RECEIVED->value],
            self::RECEIVED->value => [self::COMPLETED->value, self::RETURNING->value],
            self::COMPLETED->value => [self::RETURNING->value],
            self::CANCELLED->value => [],
            self::RETURNING->value => [],
        ];
    }
}
