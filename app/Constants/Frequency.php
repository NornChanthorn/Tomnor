<?php

namespace App\Constants;

class Frequency
{
    const MONTHLY = 30;
    const HALF_MONTH = 15;
    const WEEKLY = 7;
    const DAILY = 1;
    
    public static $frequencies = [
        self::MONTHLY => 'Monthly',
        self::HALF_MONTH => 'Half Month',
        self::WEEKLY => 'Weekly',
        self::DAILY => 'Daily',
    ];
}
