<?php
/**
* blabla
* @name Mensa UNIFR Bridge
* @description asd
*/
class MensaUNIFRBridge extends BridgeAbstract {

    public function collectData() {
        $html = '';
        $link = 'http://www.unifr.ch/mensa/fr/menu/week';

        $html = getSimpleHTMLDOM($link) or $this->returnError('Could not request MensaUNIFR.', 404);

        $rows = $html->find('div#content table tr');
        $titles = $rows[0]->find('td');
        $menus = $rows[4]->find('td');
        for ($i=0; $i<5; $i++) {
            $title = $titles[$i]->plaintext;
            $content = $menus[$i]->innertext;

            $id = md5($title . $content);
            $uri = "http://www.unifr.ch/mensa/fr/menu/week?uid=".$id;

            $this->items[] = array(
                "title" => $title,
                "content" => $content,
                "uri" => $uri
            );
        }
        $this->items = array_reverse($this->items);
    }

    public function getName(){
        return 'MensaUNIFR';
    }

    public function getURI(){
        return 'http://www.unifr.ch/mensa/fr/menu/week';
    }

    public function getCacheDuration(){
        return 43200; // 12 hours
        //return 1;
    }
}
