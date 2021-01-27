<?php
/**
 * Created by IntelliJ IDEA.
 * User: Francesca
 * Date: 26/01/2021
 * Time: 09:10
 */
defined('_JEXEC') or die;

class gglmsControllerMt extends JControllerLegacy {

    public static function test_email() {

        $user_id = 44;

        $_user_quote = new gglmsModelUsers();
        $_user_details = $_user_quote->get_user_details_cb($user_id);
        $send = utilityHelper::send_sinpe_email_pp("luca.gallo@gallerygroup.it",
            '2021-01-21 13:04:57',
            'dettaglio ordine e bla bla',
            '2021',
            $_user_details,
            160,
            90);

        if ($send)
            echo "ok";
        else
            echo "Non inviata";

    }

    public static function test_db() {


        $db = JFactory::getDbo();
        $string = "ciao' come'?";
        echo $db->escape($string);

    }

    public static function test_sub() {

        $app = JFactory::getApplication();

        $prefisso_coupon = "AMM";
        $nome_societa = "L'AZIENDA PERFETTA";

        // controllo lunghezza del nome della società
        $_check_len = strlen($nome_societa);
        if ($_check_len < 3) {
            for ($i = $_check_len; $i < 3; $i++) {
                $nome_societa .= "s";
            }
        }

        // prende il nome società fino al quarto carattere - quindi se la prima parole è UN' nel codice coupon finisce '
        // tolgo dal prefisso tutto ciò che non è lettera o numero
        $_prefisso_az = preg_replace('~[^a-zA-Z]~i', '', substr($nome_societa, 0, 3));
        // controllo la lunghezza della stringa
        $_str_leng = strlen($_prefisso_az);
        // se minore di 3 caratteri accodo "s"
        if ($_str_leng < 3) {
            for ($i = $_str_leng; $i < 3; $i++) {
                $_prefisso_az .= "s";
            }
        }

        //$var_1 = 'X-' . str_replace(' ', '_', $prefisso_coupon) . substr($nome_societa, 0, 3);
        $var_1 = 'X-' . str_replace(' ', '_', $prefisso_coupon) . $_prefisso_az;
        $var_2 = str_replace('.', 'p', str_replace('0', 'k', uniqid('', true))); // no zeros , no dots

        echo str_replace(' ', '_', $var_1 . $var_2);


        $app->close();

    }

    public static function test_ug() {

        try {
            $app = JFactory::getApplication();

            $user_id = 44;

            $user = JFactory::getUser($user_id);
            echo implode(",", $user->groups) . "<br />";

            $_params = utilityHelper::get_params_from_plugin();
            $ug_categoria = utilityHelper::get_ug_from_object($_params, "ug_categoria");
            $ug_default = utilityHelper::get_ug_from_object($_params, "ug_default");
            $ug_extra = utilityHelper::get_ug_from_object($_params, "ug_extra");
            $gruppi_online = utilityHelper::get_ug_from_object($_params, "ug_online");
            $gruppi_moroso = utilityHelper::get_ug_from_object($_params, "ug_moroso");
            $gruppi_decaduto = utilityHelper::get_ug_from_object($_params, "ug_decaduto");

            $_user_quote = new gglmsModelUsers();
            $_user_details = $_user_quote->get_user_details_cb($user_id);
            if (!is_array($_user_details))
                throw new Exception($_user_details, 1);

            // inserisco l'utente nel gruppo online
            $_ins_online = UtilityHelper::set_usergroup_online($user_id, $gruppi_online, $gruppi_moroso, $gruppi_decaduto);
            if (!is_array($_ins_online))
                throw new Exception($_ins_online, 1);

            echo $ug_categoria . "<br />";

            // inserisco l'utente nel gruppo categoria corretto
            $_ins_categoria = utilityHelper::set_usergroup_categorie($user_id, $ug_categoria, $ug_default, $ug_extra, $_user_details);
            if (!is_array($_ins_categoria))
                throw new Exception($_ins_categoria, 1);

            echo "OK!";
        }
        catch (Exception $e) {
            echo $e->getMessage();
        }

        $app->close();
    }

}
