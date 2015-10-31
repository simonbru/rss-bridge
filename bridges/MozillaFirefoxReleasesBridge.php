<?php
/**
* blablafd
* @name MozillaFirefoxReleases
* @description asd asd
*/
class MozillaFirefoxReleasesBridge extends BridgeAbstract{

    public function collectData(array $param){
        $html = '';
        $link = $this->getURI();

        $html = file_get_html($link) or $this->returnError('Could not request Mozilla FTP.', 403);
        
        $rows = $html->find('table tbody tr');
        foreach ($rows as $row)  {
                $innertext = $row->find('td a', 0)->innertext;
                
                if (!is_numeric($innertext[0]))
                        continue;

                $item = new \Item();
                $version = rtrim($innertext, '/');
                
                $item->title = $version;
                $item->uri = $link.$innertext;
                $item->content = '<a href="'.$item->uri.'">'.$item->uri.'</a>';
                // $item->timestamp = strtotime($row->find("td")[2]->innertext);
                
                $this->items[] = $item;
        }

        usort($this->items, function($itema, $itemb) {
            return $this->my_version_compare(
                $this->version_parts($itema->title),
                $this->version_parts($itemb->title)
            );
        });
        $this->items = array_reverse($this->items);
    }

    public function getName(){
        return 'MozillaFirefoxReleases';
    }

    public function getURI(){
        return 'http://ftp.mozilla.org/pub/firefox/releases/';
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
