<?php
/**
* blabla
* @name Mensa UNIFR Bridge
* @description asd
*/
class MensaUNIFRBridge extends BridgeAbstract{

    public function collectData(array $param){
        $html = '';
        $link = 'http://www.unifr.ch/mensa/fr/menu/week';

        $html = file_get_html($link) or $this->returnError('Could not request MensaUNIFR.', 404);
        
        $rows = $html->find('div#content table tr');
        $titles = $rows[0]->find('td');
        $menus = $rows[4]->find('td');
        for ($i=0; $i<5; $i++) {
                $item = new \Item();
                
                
                $item->title = $titles[$i]->plaintext;
                //var_dump($titles[$i]->plaintext);
                $item->content = $menus[$i]->innertext;
                //$item->title = "asd";
                //$item->content = "asdasd";
                $id = md5($item->title . $item->content);
                $item->uri = "http://www.unifr.ch/mensa/fr/menu/week?uid=".$id;
                $this->items[] = $item;
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
