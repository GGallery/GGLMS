<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;


require_once JPATH_COMPONENT . '/controllers/pdf.php';

/**
 * Controller for single contact view
 *
 * @since  1.5.19
 */
class gglmsControllerAttestatiBulk extends JControllerLegacy
{

    private $_user;
    private $_japp;
    public $_params;
    public $_db;
    public $generaCoupon;

    public function __construct($config = array())
    {
        parent::__construct($config);
        $this->_japp = JFactory::getApplication();
        $this->_db = JFactory::getDbo();

    }

    public function scaricaattestati()
    {
        try {

            $data = JRequest::get($_POST);
            $id_corso = $data['id_corso'];
//            echo (json_encode($id_corso));
//            $this->_japp->close();

//            var_dump($id_corso);
//            die();


//            // dal id corso entro in base_gg_view_stato_user_corso in join con user e capisco per chi devo scaricare l'attestato
            $users = $this->getUserCompleted($id_corso);
            $att_id_string = $this->getAttestati($id_corso);
//
            $base_link = 'index.php?option=com_gglms&task=reportutente.generateAttestato';
            $link_list = array();

//            echo(json_encode($users));
//            $this->_japp->close();


            foreach ($users as $u) {
//                $link = $base_link . '&content_id=4&user_id=' . $u;
//                array_push($link_list, $u["id_user"]);


                foreach ($att_id_string as $a) {


                    $link = $base_link . '&content_id=' . $a . '&user_id=' . $u["id_user"];
                    array_push($link_list, $link);


//                    $pdf_ctrl->generateAttestato($u->id_user, $a);
                }


            }

            echo(json_encode($link_list));
            $this->_japp->close();

//            echo(json_encode($link_list));
//            $this->_japp->close();

//            var_dump($link_list);
//            die();


//            index.php?option=com_gglms&amp;task=reportutente.generateAttestato&amp;content_id=4&amp;user_id=400

//            var_dump($att_id_string);
//            var_dump($users);
//            die();


//            $pdf_ctrl = new gglmsControllerPdf();
//            foreach ($users as $u) {
//
//                foreach ($att_id_string as $a) {
//
//                    $pdf_ctrl->generateAttestato($u->id_user, $a);
//                }
//
//
//            }


        } catch (Exception $e) {

            echo(json_encode($e));
            $this->_japp->close();
//            DEBUGG::error($e, 'scaricaattestati');
        }
//        $this->_japp->close();
    }


    public function getUserCompleted($id_corso)
    {

        try {

            $query = $this->_db->getQuery(true)
                ->select(' ru.id_user')
//                ->select(' ru.id_user, ru.nome, ru.cognome')
                ->from('#__gg_view_stato_user_corso as r')
                ->join('inner', '#__gg_report_users as ru on r.id_anagrafica = ru.id')
                ->where("r.stato=1")
                ->where("r.id_corso=" . $id_corso);


            $this->_db->setQuery($query);
            $users = $this->_db->loadAssocList();
            return $users;

        } catch (Exception $e) {
            DEBUGG::error($e, 'getGruppiCorsi');

        }


    }


    public function getAttestati($id_corso)
    {

        $corso_obj = $this->_db->loadObject('gglmsModelUnita');
        $corso_obj->setAsCorso($id_corso);

        $all_attestati = $corso_obj->getAllAttestatiByCorso();
        $att_id_array = array();

        foreach ($all_attestati as $att) {
            array_push($att_id_array, $att->id);
        }


        return $att_id_array;
    }


}
