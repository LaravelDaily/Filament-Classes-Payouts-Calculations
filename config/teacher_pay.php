<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Teacher Pay Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration file contains the default teacher pay settings
    | that will be used for calculating teacher payments globally.
    |
    */

    'base_pay' => env('TEACHER_BASE_PAY', 50.00),

    'bonus_per_student' => env('TEACHER_BONUS_PER_STUDENT', 2.50),

];
