<?php


namespace App\Services;


class ConstService
{
    const CANNOT_REGISTER_FOR_PAST             = -7;
    const CANNOT_REGISTER_FOR_NUMBER           = -6;
    const CANNOT_REGISTER_FOR_WORKLIMI         = -5;
    const CANNOT_REGISTER_FOR_WORKLIMIT_IN_DAY = -4;
    const NOT_CORRECT_INPUT                    = -3;
    const NOT_EXIST_PERSON                     = -2;
    const ONLY_FOR_ADMINISTRATOR               = -1;
    const UPDATE_EMPLOYEE                      = 1;
    const SHIFTS_FOR_ADMINISTRATOR             = 2;
    const SHIFTS_FOR_EMPLOYEE                  = 3;
    const REGISTER_BY_EMPLOYEE                 = 4;
    const REGISTER_BY_EXPERT_EMPLOYEE          = 5;
}
