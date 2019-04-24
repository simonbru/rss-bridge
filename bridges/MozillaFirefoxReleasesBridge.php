<?php
/**
* blablafd
* @name MozillaFirefoxReleases
* @description asd asd
*/
class MozillaFirefoxReleasesBridge extends BridgeAbstract {

    public function collectData() {
        $html = '';
        $link = $this->getURI();
        $errMsg = 'Could not request Mozilla FTP.';
        $html = getSimpleHTMLDOM($link) or $this->returnError($errMsg, 403);
        $rows = $html->find('table tr');
        foreach ($rows as $row)  {
            $innertext = $row->find('td a', 0)->innertext;

            if (!is_numeric($innertext[0]))
                    continue;

            $version = rtrim($innertext, '/');

            $title = $version;
            $uri = $link.$innertext;
            $content = '<a href="'.$uri.'">'.$uri.'</a>';
            // $item->timestamp = strtotime($row->find("td")[2]->innertext);

            $this->items[] = array(
                'title' => $title,
                'version' => $version,
                'uri' => $uri,
                'content' => $content
            );
        }

        usort($this->items, function($itema, $itemb) {
            return $this->my_version_compare(
                $this->version_parts($itema['title']),
                $this->version_parts($itemb['title'])
            );
        });
        $this->items = array_reverse($this->items);
    }

    public function getName(){
        return 'MozillaFirefoxReleases';
    }

    public function getURI(){
        return 'https://ftp.mozilla.org/pub/firefox/releases/';
    }

    public function getCacheDuration(){
        return 7200; // 2 hours
        // return 1;
    }

    /* Split version string into parts more easily comparable */
    protected static function version_parts($version_str) {
        $tmp = preg_split('/[._-]/', $version_str);
        $nums = [];
        foreach($tmp as $part) {
            if (preg_match('/(\d+)([a-zA-Z]+)(.*)/', $part, $matches)) {
                $nums[] = $matches[1];
                // We prefix the verpart with '-' so beta, rc, etc..
                // is always smaller than a number alone
                $nums[] = '-' . $matches[2];
                $nums[] = $matches[3];
            } else {
                $nums[] = $part;
            }
        }
        return $nums;
    }

    /* Compare two splitted version arrays */
    protected static function my_version_compare($aa, $bb) {
        if (empty($aa) and empty($bb))
            return 0;
        // Pop the next version part and give '0' if there is none
        $a = array_shift($aa) ?: '0';
        $b = array_shift($bb) ?: '0';

        if ($a < $b) return -1;
        elseif ($a > $b) return 1;
        else return self::my_version_compare($aa, $bb);
    }
}
