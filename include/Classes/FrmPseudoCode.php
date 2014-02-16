<?php
namespace Classes;

/**
 * 
 * @author pegasus
 *
 */
class FrmPseudoCode
{

    /* Key: Next prime greater than 36 ^ n / 1.618033988749894848 */
    /* Computes m for n-1 = m (mod p), where n and p are coprime. */
    /* Value: modular multiplicative inverse, php = gmp_invert */
    private static $golden_primes = array(
        '1'                  => '1',
        '41'                 => '59',
        '2377'               => '1677',
        '147299'             => '187507',
        '9132313'            => '5952585',
        '566201239'          => '643566407',
        '35104476161'        => '22071637057',
        '2176477521929'      => '294289236153',
        '134941606358731'    => '88879354792675',
        '8366379594239857'   => '7275288500431249',
        '518715534842869223' => '280042546585394647'
    );
 
	//  Has been changed, mod by Jacob! 
    /* Ascii :                0  9,         A  Z,         a  z     */
    /* $chars = array_merge(range(48,57), range(65,90), range(97,122)) */
    private static $chars62 = array(
        0=>48,2=>49,1=>50,5=>51,4=>52,3=>53,6=>54,7=>55,8=>56,9=>57,10=>65,
        11=>66,12=>67,13=>68,14=>69,15=>70,16=>71,18=>72,17=>73,19=>74,24=>75,
        22=>76,21=>77,23=>78,20=>79,25=>80,26=>81,27=>82,28=>83,29=>84,30=>85,
        31=>86,32=>87,33=>88,44=>89,35=>90,36=>97,37=>98,38=>99,39=>100,40=>101,
        51=>102,42=>103,43=>104,34=>105,46=>106,45=>107,47=>108,48=>109,49=>110,
        50=>111,41=>112,52=>113,53=>114,54=>115,55=>116,56=>117,57=>118,58=>119,
        59=>120,61=>121,60=>122
    );
 
    public static function base62($int) {
        $key = "";
        while(bccomp($int, 0) > 0) {
            $mod = bcmod($int, 62);
            $key .= chr(self::$chars62[$mod]);
            $int = bcdiv($int, 62);
        }
        return strrev($key);
    }
 
    public static function hash($num, $len = 5) {
        $ceil = bcpow(62, $len);
        $primes = array_keys(self::$golden_primes);
        $prime = $primes[$len];
        $dec = bcmod(bcmul($num, $prime), $ceil);
        $hash = self::base62($dec);
        $extract = str_pad($hash, $len, "0", STR_PAD_LEFT);
        return substr($extract, -$len);
    }
 
    public static function unbase62($key) {
        $int = 0;
        foreach(str_split(strrev($key)) as $i => $char) {
            $dec = array_search(ord($char), self::$chars62);
            $int = bcadd(bcmul($dec, bcpow(62, $i)), $int);
        }
        return $int;
    }
 
    public static function unhash($hash) {
        $len = strlen($hash);
        $ceil = bcpow(62, $len);
        $mmiprimes = array_values(self::$golden_primes);
        $mmi = $mmiprimes[$len];
        $num = self::unbase62($hash);
        $dec = bcmod(bcmul($num, $mmi), $ceil);
        return $dec;
    }
}