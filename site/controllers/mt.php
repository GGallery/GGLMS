<?php
/**
 * Created by IntelliJ IDEA.
 * User: Francesca
 * Date: 26/01/2021
 * Time: 09:10
 */
defined('_JEXEC') or die;

class gglmsControllerMt extends JControllerLegacy {

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
