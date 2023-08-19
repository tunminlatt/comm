<?php

namespace App\Helpers;

use Rabbit;

class BurmeseFontConverter
{
    public function __construct()
    {
        $rabbit = new Rabbit();
        $this->rabbit = $rabbit;
    }

    protected function isZawgyi($str) {
        $zawgyiRegex1 = 'ေျ' // e+medial ra
        // beginning e or medial ra
        .'|^ေ|^ျ'
        // independent vowel, dependent vowel, tone , medial ra wa ha (no ya
        // because of 103a+103b is valid in unicode) , digit ,
        // symbol + medial ra
        .'|[ဢ-ူဲ-္ျ-ွ၀-၏]ျ'
        // end with asat
        .'|္$'
        // medial ha + medial wa
        .'|ွြ'
        // medial ra + medial wa
        .'|ျြ'
        // consonant + asat + ya ra wa ha independent vowel e dot below
        // visarga asat medial ra digit symbol
        .'|[က-အ]္[ယရဝဟဢ-ဪေ့-္ျ၀-၏]'
        // II+I II ae
        .'|ီ[ိှဲ]'
        // ae + I II
        .'|ဲ[ိီ]'
        // I II , II I, I I, II II
        //+ "|[ိီ][ိီ]"
        // U UU + U UU
        //+ "|[ုူ][ုူ]" [ FIXED!! It is not so valuable zawgyi pattern ]
        // tall aa short aa
        //+ "|[ါာ][ါာ]" [ FIXED!! It is not so valuable zawgyi pattern ]
        // shan digit + vowel
        .'|[႐-႙][ါ-ူဲ့ြ-ှ]'
        // consonant + medial ya + dependent vowel tone asat
        .'|[က-ဪ]်[ာ-ီဲ-ံ]'
        // independent vowel dependent vowel tone digit + e [ FIXED !!! - not include medial ]
        .'|[ဣ-ူဲ-္၀-၏]ေ'
        // other shapes of medial ra + consonant not in Shan consonant
        .'|[ၾ-ႄ][ခဃစ-ဏဒ-နဖ-ဘဟ]'
        // u + asat
        .'|ဥ္'
        // eain-dray
        .'|[ႁႃ]ႏ'
        // short na + stack characters
        .'|ႏ[ၠ-ႍ]'
        // I II ae dow bolow above + asat typing error
        .'|[ိ-ူဲံ့]္'
        // aa + asat awww
        .'|ာ္'
        // ya + medial wa
        .'|ရြ'
        // non digit + zero + ိ (i vowel) [FIXED!!! rules tested zero + i vowel in numeric usage]
        .'|[^၀-၉]၀ိ'
        // e + zero + vowel
        .'|ေ?၀[ါၚီ-ူဲံ-း]'
        // e + seven + vowel
        .'|ေ?၇[ာ-ူဲံ-း]'
        // cons + asat + cons + virama
        //+ "|[က-အ]်[က-အ]္" [ FIXED!!! REMOVED!!! conflict with Mon's Medial ]
        // U | UU | AI + (zawgyi) dot below
        .'|[ုူဲ]႔'
        // virama + (zawgyi) medial ra
        .'|္[ၾ-ႄ]';
        $ptn = '/'.$zawgyiRegex1.'/u';
        //echo $ptn;
        //preg_match_all($ptn, $str, $output_array);
        // if (count($output_array) > 0) {
        //     return true;
        // } else {
        //     return false;
        // }
        if (preg_match_all($ptn, $str)) {
            return true;
        } else {
            return false;
        }
    }

    public function toUnicode($str) {
        if ($this->isZawgyi($str)) {
            return $this->rabbit->zg2uni($str);
        }

        return $str;
    }

    public function toZawgyi($str) {
        if (!$this->isZawgyi($str)) {
            return $this->rabbit->uni2zg($str);
        }

        return $str;
    }
}