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
                
                $href = $row->find('td a', 0)->href;
                
                if (!is_numeric($href[0]))
                        continue;

                $item = new \Item();
                $version = rtrim($href, '/');
                
                $item->title = $version;
                $item->uri = $link.$href;
                $item->content = '<a href="'.$item->uri.'">'.$item->uri.'</a>';
                $item->timestamp = strtotime($row->find("td")[2]->innertext);
                
                $this->items[] = $item;
        }
        $this->items = array_reverse($this->items);
    }

    public function getName(){
        return 'MozillaFirefoxReleases';
    }

    public function getURI(){
        return 'http://ftp.mozilla.org/pub/mozilla.org/firefox/releases/';
    }

    public function getCacheDuration(){
        return 7200; // 2 hours
        // return 1;
    }
}
