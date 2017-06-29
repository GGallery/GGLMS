<?php

/**
 * Created by Antonio Giangravè - GGallery.
 * User: Tony
 * Date: 29/06/2017
 * Time: 09:15
 */
class DEBUGG
{

    public static function log($object, $label = "", $die = 0){

        if($label)
            echo "<label>". $label ."</label>";


            echo "<pre>";

            print_r($object);

            echo "</pre>";

        if($die)
            die();
    }


    public static function info($object, $label, $die = 0){

        return self::log($object, $label, $die);

    }

    public static function error($object, $label, $die = 0){

        return self::log($object, $label, $die);

    }

}