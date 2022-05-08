<?php

namespace App\Utilities;

use Hekmatinasser\Verta\Verta;
use Illuminate\Support\Carbon;

class JalaliDateConverter
{

    public static function convertToGregorianDateTimeFormat(string $jalaliDateTime):string
    {

        $explodedJalaliDate = explode('/', $jalaliDateTime);

        $explodedGregorianDate = Verta::getGregorian($explodedJalaliDate[0],$explodedJalaliDate[1],substr($explodedJalaliDate[2],0,2));
        $hour = substr($explodedJalaliDate[2],3,2);
        $minute = substr($explodedJalaliDate[2],6,2);
        $second = substr($explodedJalaliDate[2],9,2);
        $convertedDateTime = Carbon::create($explodedGregorianDate[0], $explodedGregorianDate[1], $explodedGregorianDate[2], (int)$hour, (int)$minute, $second)->toDateTimeString();
        return  $convertedDateTime;
    }

    private function jalali_to_gregorian($j_y, $j_m, $j_d)
    {
        $g_days_in_month = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
        $j_days_in_month = array(31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29);
        $jy = $j_y-979;
        $jm = $j_m-1;
        $jd = $j_d-1;
        $j_day_no = 365*$jy + $this->div($jy, 33)*8 + $this->div($jy%33+3, 4);

        for ($i=0; $i < $jm; ++$i)
            $j_day_no += $j_days_in_month[$i];

        $j_day_no += $jd;
        $g_day_no = $j_day_no+79;
        $gy = 1600 + 400*$this->div($g_day_no, 146097); /* 146097 = 365*400 + 400/4 - 400/100 + 400/400 */
        $g_day_no = $g_day_no % 146097;
        $leap = true;

        if ($g_day_no >= 36525) /* 36525 = 365*100 + 100/4 */
        {
            $g_day_no--;
            $gy += 100*$this->div($g_day_no, 36524); /* 36524 = 365*100 + 100/4 - 100/100 */
            $g_day_no = $g_day_no % 36524;
            if ($g_day_no >= 365)
                $g_day_no++;
            else
                $leap = false;
        }

        $gy += 4*$this->div($g_day_no, 1461); /* 1461 = 365*4 + 4/4 */
        $g_day_no %= 1461;

        if ($g_day_no >= 366)
        {
            $leap = false;
            $g_day_no--;
            $gy += $this->div($g_day_no, 365);
            $g_day_no = $g_day_no % 365;
        }

        for ($i = 0; $g_day_no >= $g_days_in_month[$i] + ($i == 1 && $leap); $i++)
            $g_day_no -= $g_days_in_month[$i] + ($i == 1 && $leap);

        $gm = $i+1;
        $gd = $g_day_no+1;

        return array($gy, $gm, $gd);
    }

    function div($a,$b)
    {
        return (int) ($a / $b);
    }

}
