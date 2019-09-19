<?php
// no direct access

defined('_JEXEC') or die('Restricted access');

foreach($this->catalogo as $item){
//    var_dump($item);
  echo $item->titolo.'<a href="'.$item->descrizione.'">vai</a>'.'<br>';
}
