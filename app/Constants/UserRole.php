<?php

namespace App\Constants;

class UserRole
{
    const ADMIN = 1;
    const STAFF = 2;
    const REPORTER = 3;

    /**
     * Get all user roles.
     * 
     * @return array
     */
    public static function allRoles()
    {
        return [
            self::ADMIN,
            self::STAFF,
            self::REPORTER,
        ];
    }
}