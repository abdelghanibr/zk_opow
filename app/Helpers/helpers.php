<?php

if (! function_exists('dayName')) {
    function dayName($dayNumber)
    {
        return match ((int) $dayNumber) {
            0 => 'الأحد',
            1 => 'الإثنين',
            2 => 'الثلاثاء',
            3 => 'الأربعاء',
            4 => 'الخميس',
            5 => 'الجمعة',
            6 => 'السبت',
            default => 'غير معروف',
        };
    }
}
