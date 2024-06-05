<?php

namespace App\Helpers;

use Torann\GeoIP\Facades\GeoIP;

class LanguageHelper{

    public static function getLanguage($region = null) {

        $languages = [
            'global' => [
                'en' => 'English',
                'fr' => 'French',
                'es' => 'Spanish',
                'it' => 'Italian',
                'zh-CN' => 'Chinese (Simplified)',
                'zh-TW' => 'Chinese (Traditional)',
                'de' => 'German',
                'ar' => 'Arabic',
                'ja' => 'Japanese',
                'ko' => 'Korean',
                'ru' => 'Russian',
                'ms' => 'Malay',
                'pt' => 'Portuguese',
            ],
            'local' => [
                'en' => 'English',
                'hi' => 'Hindi',
                'mr' => 'Marathi',
                'bn' => 'Bengali',
                'te' => 'Telugu',
                'ta' => 'Tamil',
                'kn' => 'Kannada',
                'ml' => 'Malayalam',
                'gu' => 'Gujarati',
                'pa' => 'Punjabi',
            ],
        ];
        return $languages[$region] ?? [];
    }

    public static function getLanguageByRegion($ip){

        // $region = GeoIP::getLocation('132.154.54.142');  // just for testing purposes
        $region = GeoIP::getLocation($ip);
        $languages = [];

        if (!empty($region)) {

            $country = $region['country'];

            if (!empty($country)) {

                if (strtolower($country) === 'india') {
                    $languages = self::getLanguage('local');
                } else {
                    $languages = self::getLanguage('global');
                }
                return $languages;
            }
        }

        return $languages;
    }
}

?>
