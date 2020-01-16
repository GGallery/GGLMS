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
        $this->_db = JFactory::getDbo();

    }

    public function scaricaattestati()
    {
        try {

            $data = JRequest::get($_POST);
            $id_corso = $data['id_corso'];
//           var_dump($id_corso);

            // dal id corso entro in base_gg_view_stato_user_corso in join con user e capisco per chi devo scaricare l'attestato
            $users = $this->getUserCompleted($id_corso);
            $att_id_string = $this->getAttestati($id_corso);
//            var_dump($att_id_string);
//            var_dump($users);
//            die();

            $pdf_ctrl = new gglmsControllerPdf();
            foreach ($users as $u) {

                foreach ($att_id_string as $a) {

                    $pdf_ctrl->generateAttestato($u->id_user, $a);
                }


            }


        } catch (Exception $e) {

            DEBUGG::error($e, 'scaricaattestati');
        }
//        $this->_japp->close();
    }


    public function getUserCompleted($id_corso)
    {

        try {

            $query = $this->_db->getQuery(true)
                ->select(' ru.id_user, ru.nome, ru.cognome')
                ->from('#__gg_view_stato_user_corso as r')
                ->join('inner', '#__gg_report_users as ru on r.id_anagrafica = ru.id')
                ->where("r.stato=1")
                ->where("r.id_corso=" . $id_corso);


            $this->_db->setQuery($query);
            $users = $this->_db->loadObjectList();
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
