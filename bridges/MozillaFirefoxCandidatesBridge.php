<?php
/**
* blablafdasd
* @name MozillaFirefoxCandidates
* @description asd asda
*/

require_once "MozillaFirefoxReleasesBridge.php";

class MozillaFirefoxCandidatesBridge extends MozillaFirefoxReleasesBridge {

    public function getName(){
        return 'MozillaFirefoxCandidates';
    }

    public function getURI(){
        return 'https://ftp.mozilla.org/pub/mozilla.org/firefox/candidates/';
    }

}
