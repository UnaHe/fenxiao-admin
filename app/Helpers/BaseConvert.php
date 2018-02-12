<?php
/**
 * Created by PhpStorm.
 * User: yangtao
 * Date: 2018/1/26
 * Time: 14:59
 */

namespace App\Helpers;

class BaseConvert
{
    static $map = [
        0=>'0',1=>'1',2=>'2',3=>'3',4=>'4',5=>'5',6=>'6',7=>'7',8=>'8',9=>'9',
        10=>'A',11=>'B',12=>'C',13=>'D',14=>'E',15=>'F',16=>'G',17=>'H',18=>'I',19=>'J',
        20=>'K',21=>'L',22=>'M',23=>'N',24=>'O',25=>'P',26=>'Q',27=>'R',28=>'S',29=>'T',
        30=>'U',31=>'V',32=>'W',33=>'X',34=>'Y',35=>'Z',36=>'a',37=>'b',38=>'c',39=>'d',
        40=>'e',41=>'f',42=>'g',43=>'h',44=>'i',45=>'j',46=>'k',47=>'l',48=>'m',49=>'n',
        50=>'o',51=>'p',52=>'q',53=>'r',54=>'s',55=>'t',56=>'u',57=>'v',58=>'w',59=>'x',
        60=>'y',61=>'z',62=>'_',63=>'=',
    ];

    /**
     * 将10进制数转换为指定进制字符串
     * @param string $dec 10进制字符串
     * @param int $jz 指定需要转换的进制
     * @return bool|string
     */
    static function dec2str($dec, $jz = 64) {
        if ($dec < 0) {
            return false;
        }
        if(strlen($dec)>14){
            throw new \Exception("不支持14位以上数字的转换");
        }
        if($jz > 64 || $jz < 2){
            throw new \Exception("不支持的进制数");
        }
        $b64 = '';
        do {
            $mod = bcmod($dec, $jz);
            $b64 = self::$map[$mod] . $b64;
            $dec = bcdiv($dec, $jz);
        } while ($dec >= 1);

        return $b64;
    }

    /**
     * 将指定进制的字符串转换为10进制数
     * @param string $str 需要转换的字符串
     * @param int $jz   指定进制
     * @return bool|int|string
     */
    static function str2dec($str, $jz=64) {
        if($jz > 64 || $jz < 2){
            throw new \Exception("不支持的进制数");
        }

        $map = array_flip(self::$map);
        $dec = 0;
        $len = strlen($str);

        for ($i = $len-1; $i >= 0; $i--) {
            $code = $map[$str{$i}];
            if ($code === null) {
                return FALSE;
            }
            $dec = bcadd($dec, $jz**($len-$i-1) * $code);
        }

        return $dec;
    }

}