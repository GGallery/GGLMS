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



        if(is_object($object))
            return  DEBUGG::object($object, $label , $die);

        echo "<div class='debugg'>";
        if($label)
            echo "<label>". $label ."</label>";


        echo "<pre>";

        print_r($object);

        echo "</pre>";
        

        if($die)
            die();
    }

    public static function query($query, $label = "", $die = 0){

        echo "<div class='debugg'>";

        if($label)
            echo "<label>". $label ."</label>";


        echo "<pre>";

        print_r((string)$query);

        echo "</pre>";

        echo "</div>";

        if($die)
            die();
    }


    public static function info($object, $label, $die = 0){

        return self::log($object, $label, $die);

    }

    public static function error($object, $label, $die = 0){

        if($label)
            echo "<label>". $label ."</label>";


        echo "<pre>";
        print_r($object);
        echo "</pre>";

        if($die)
            die();

    }

    public static function object($object, $label = "", $die = 0){

        if($label)
            echo "<label>". $label ."</label>";


        $var= get_object_vars($object);

        $method = get_class_methods($object);



        echo "<pre>";

        print_r($var);
        print_r($method);

        echo "</pre>";

        if($die)
            die();
    }



}