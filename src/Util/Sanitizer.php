<?php


namespace App\Util;


class Sanitizer
{
    public function removeUndesiredCharacters($string){
        return preg_replace('/[^\p{Latin}\d ]/u', '', $string);
    }
}