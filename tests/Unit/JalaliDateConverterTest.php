<?php

namespace Tests\Unit;

use App\Utilities\JalaliDateConverter;

class JalaliDateConverterTest extends \Tests\TestCase
{

    public function test_ensure_can_convert_jalali_date_time_to_gregorian_date_time_format()
    {
        $jalaliDateTime = '1401/01/31 03:10';
        $gregorianDateTime = JalaliDateConverter::convertToGregorianDateTimeFormat($jalaliDateTime);
        $this->assertEquals('2022-04-20 03:10:00', $gregorianDateTime);
    }
}
