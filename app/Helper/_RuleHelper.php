<?php


namespace App\Helper;


class _RuleHelper
{
    const _Rule_Require = 'required';
    const _Rule_Email = 'email';
    const _Rule_Number = 'numeric';
    const _Rule_Date = 'date_format:Y-m-d';
    const _Rule_Date_Time = 'date_format:Y-m-d H:i:s';
    const _Rule_Time = 'date_format:H:i:s';
    const _Rule_After_Time = 'date_format:H:i:s|after:';
    const _Rule_Min = 'min:';
    const _Rule_Max = 'max:';
}
