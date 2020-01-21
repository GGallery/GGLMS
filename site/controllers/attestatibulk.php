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
require_once JPATH_COMPONENT . '/models/pdf.php';

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


    public function getUserCompleted($id_corso)
    {

        try {

            $query = $this->_db->getQuery(true)
//                ->select(' ru.id_user')
                ->select('ru.id_user, ru.nome, ru.cognome, r.data_inizio, r.data_fine')
                ->from('#__gg_view_stato_user_corso as r')
                ->join('inner', '#__gg_report_users as ru on r.id_anagrafica = ru.id')
                ->where("r.stato=1")
                ->where("r.id_corso=" . $id_corso);


            $this->_db->setQuery($query);
            $users = $this->_db->loadAssocList();

            // creo array di id utenti
            $user_id_list = array();
            foreach ($users as $u) {
                array_push($user_id_list, $u["id_user"]);
            }


            return $user_id_list;

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


    public function downloadAttestati_multiple()
    {


        try {

            $data = JRequest::get($_POST);
            $id_corso = $data['id_corso'];

            $user_id_list = $this->getUserCompleted($id_corso); //[400,394]
            $attestati_corso = $this->getAttestati($id_corso); //[3,4]

            $pdf_ctrl = new gglmsControllerPdf();
            $file_location = JPATH_COMPONENT . '/models/tmp/';


            foreach ($attestati_corso as $att_id) {

                $data_att = $pdf_ctrl->getDataForAttestato_multi($user_id_list, $att_id);
                foreach ($data_att as $data) {
                    $model = new gglmsModelPdf();
                    $pdf = $model->_generate_pdf($data->user, $data->orientamento, $data->attestato, $data->contenuto_verifica, $data->dg, $data->tracklog, true);

                    ob_end_clean();
                    $nomefile = $file_location . 'attestato_' .$att_id . $data->user->id;
                    $pdf->Output($nomefile . '.pdf', 'F');

                }

            }


            // zippala
            //scaricala
            //svuotala

            $this->_japp->close();

        } catch
        (Exception $e) {
            echo $e;
        }


    }


}
