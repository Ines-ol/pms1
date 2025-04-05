<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case MANAGER = 'manager';
    case EMPLOYEE = 'employee';
    case CLIENT = 'client';

    // Ajoutez cette méthode
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}