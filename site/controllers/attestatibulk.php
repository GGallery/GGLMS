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
    public $_folder_location = JPATH_COMPONENT . '/models/tmp/';
    public $id_user;
    public $id_corso;
    // integrazione id_azienda nello scaricamento degli attestati
    public $id_azienda;
    // integrazione scelta nome file per salvataggio report
    public $salva_come;
    //public $arr_data_fine;
    public $report_month_limit;
    public $from_bulk;

    public function __construct($config = array())
    {
        parent::__construct($config);
        $this->_japp = JFactory::getApplication();
        $this->_db = JFactory::getDbo();
        $this->report_month_limit = 2;

    }

    //ENTRY POINT
    // salva  gli attestati di un corso (se non riceve utente in input di tutti gli utenti che lo hanno completato) in folder components/gglms/models/tmp
    public function dwnl_attestati_by_corso()
    {

        try {

            // delete all files from tmp folder
            $this->clean_folder();

            $data = JRequest::get($_POST);


            $this->id_corso = $data['id_corso'];

            $this->id_azienda = $data['id_azienda'];
            if ($this->id_azienda == ""
                || empty($this->id_azienda))
                $this->id_azienda = null;

            // tolgo spazi inutile
            $this->salva_come = trim($data['salva_come']);
            // rimuovo virgola se ultimo carattere
            if (substr(rtrim($this->salva_come), -1) == ",")
                $this->salva_come = substr($this->salva_come, 0, -1);

            if ($this->salva_come == ""
                || empty($this->salva_come))
                $this->salva_come = null;

            $this->id_user = $data['user_id'] ? $data['user_id'] : null;

//            var_dump($data['startdate'] );
//            var_dump($data['enddate'] );
//            die();


            $start = $data['startdate'];
            $end = $data['enddate'];

            $this->from_bulk = isset($data['from_bulk']) ? true : false;

            $user_id_list = array();
            // il controllo sulle date lo devo fare soltanto se arrivo dal bulk
            $date_validation = $this->date_validation($start, $end, $this->from_bulk);

            if ($date_validation != "tuttook")
                $this->_japp->redirect(('index.php?option=com_gglms&view=attestatibulk&layout=attestatibulk'), $this->_japp->enqueueMessage($date_validation, 'Warning'));

            if ($this->id_user == null) {

                // UTENTI MULTIPLI (per ora non usato) -> non mi viene passato l'utente
                // scarico tutti
                $user_id_list = $this->getUserCompleted($this->id_corso,
                                                        $start,
                                                        $end,
                                                        $this->id_azienda);

            } else {

                // UTENTE SINGOLO --> chiamato da report kendo
                array_push($user_id_list, $this->id_user);
            }

            if (count($user_id_list) > 0) {

                $this->do_genereate_attestati_multiple($user_id_list, $this->id_corso, $this->salva_come);
            } else {

                $this->_japp->redirect(('index.php?option=com_gglms&view=attestatibulk&layout=attestatibulk'), $this->_japp->enqueueMessage('Non ci sono utenti che hanno completato il corso nelle date selezionate', 'Warning'));

            }


        } catch
        (Exception $e) {
            echo $e;
        }


    }


    // controllo delle data inputate
    private function date_validation($start, $end, $from_bulk) {

        if (!$from_bulk)
            return "tuttook";

        // date non valorizzate
        if (is_null($start)
            || $start == ""
            || empty($start)
            || is_null($end)
            || empty($end)
            || $end == "")
            return "Date non correttamente valorizzate";

        // data fine maggiore di data inizio
        if ($end < $start)
            return "La data di fine non può essere precedente alla data di inizio";

        $diff_in_months = utilityHelper::get_date_diff_format($end, $start, "m");

        if ($diff_in_months > $this->report_month_limit)
            return "L'intervallo temporale fra date non può essere maggiore di " . $this->report_month_limit . " mesi";

        return "tuttook";

    }

    ///////////////////////////////////////////////////
    public function do_genereate_attestati_multiple($user_id_list, $id_corso, $salva_come = null)
    {

        $pdf_ctrl = new gglmsControllerPdf();
        $model = new gglmsModelPdf();
        $file_list = array();
        $attestati_corso = $this->getAttestati($id_corso); //[3,4]

        if (count($attestati_corso) > 1
            || count($user_id_list) > 1) {
            // ho più di un attestato per il corso, oppure più utenti per lo stesso corso --> salvo in tmp, zippo e download

//            var_dump($attestati_corso);
//            die();
            foreach ($attestati_corso as $att_id) {

                $data_att = $pdf_ctrl->getDataForAttestato_multi($user_id_list, $att_id, $id_corso);

                foreach ($data_att as $data) {

                    // per data corso $data->dati_corso[0]
                    $pdf = $model->_generate_pdf(
                                                $data->user,
                                                $data->orientamento,
                                                $data->attestato,
                                                $data->contenuto_verifica,
                                                $data->dg,
                                                $data->tracklog,
                                                '',
                                                '',
                                                true);

                    // il rand non serve più dopo aver adottato il controllo dell'esistenza file multipla
                    //$nome_file = 'attestato_' . $att_id . '_' .$data->user->cognome . rand() . '.pdf';
                    $nome_file = 'attestato_' . $att_id . '_' . $data->user->cognome;

                    // se in modalità salva nome modifico il nome di default del file usando il codice coupon ed eliminando il numero randomico
                    if (isset($data->dati_corso[0]->codice_corso)) {
                        $nome_file = 'attestato_' . $data->dati_corso[0]->codice_corso . '_' . $data->user->cognome;
                    }

                    $nome_file = strtoupper($nome_file) . '.pdf';

                    // se da form lo richiedo salvo il nome secondo la nomenclatura prescelta
                    if (!is_null($salva_come)
                            && $salva_come != "") {
                        $nome_file = utilityHelper::build_nome_file_attestato($data, $salva_come);
                    }

                    // controllo se il file è già esistente..metodo che mi torna utile per il loop di creazione file
                    // così da eliminare il rand()
                    $nome_file = UtilityHelper::rename_file_recursive($this->_folder_location, $nome_file);
                    $path_file = $this->_folder_location . $nome_file;

                    //DEBUGG::log(json_encode($path_file), "nome pdf", 0, 1);

                    // save file in folder
                    $pdf->Output($path_file, 'F');

                    $file_obj = new stdClass();
                    $file_obj->path = $path_file;
                    $file_obj->nome = $nome_file;
                    array_push($file_list, $file_obj);

                }

            }

            $this->zip_and_download($file_list);
        } else {
            $pdf_ctrl->generateAttestato($this->id_user, $attestati_corso[0], true, $id_corso);
        }

    }

    // ritorna array di id_utente che hanno completato il corso
    // aggiunto filtro anche per azienda (o gruppo azienda)
    public function getUserCompleted($id_corso,
                                     $start = null,
                                     $end = null,
                                     $id_azienda = null)
    {

        try {

            $query = $this->_db->getQuery(true);
            $sub_query = null;

            if (!is_null($id_azienda)
                && $id_azienda != "") {
                $sub_query = $this->_db->getQuery(true);
                $sub_query->select('user_id');
                $sub_query->from('#__user_usergroup_map');
                $sub_query->where('group_id = \'' . $id_azienda . '\'');
            }

            //$query->select(' ru.id_user')
            $query->select('ru.id_user, ru.nome, ru.cognome, r.data_inizio, r.data_fine')
                ->from('#__gg_view_stato_user_corso as r')
                ->join('inner', '#__gg_report_users as ru on r.id_anagrafica = ru.id')
                ->where("r.stato = 1")
                ->where("r.id_corso=" . $id_corso);

            if ($start) {
                $query->where("data_fine >= '" . $start . "'");
            }

            if ($end) {
                $query->where("data_fine <= '" . $end . "'");
            }

            // scatta il filtro sull'azienda
            if (!is_null($sub_query)) {
                $query->where('ru.id_user IN (' . $sub_query . ')');
            }

            $this->_db->setQuery($query);
            $users = $this->_db->loadAssocList();

            // creo array di id utenti
            $user_id_list = array();
            foreach ($users as $u) {
                array_push($user_id_list, $u["id_user"]);
                // oltre agli id memorizzo anche la data in cui il corso è stato completato
                // $this->arr_data_fine[$u["id_user"]] = $u["data_fine"];
            }


            return $user_id_list;

        } catch (Exception $e) {
            DEBUGG::error($e, 'getGruppiCorsi');

        }


    }

    // ritorna array di id attestati del corso
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

    public function zip_and_download($file_list)
    {

        try {

            $zip_name = $this->get_prefisso_corso($this->id_corso) . '_' . time() . '.zip';
            $zip_location = $this->_folder_location . $zip_name;
            $error = '';


            $zip = new ZipArchive();
            if ($zip->open($zip_location, ZIPARCHIVE::CREATE) !== TRUE) {
                $error .= "* Sorry ZIP creation failed at this time";
                print_r($error);
                die();
            }


            foreach ($file_list as $f) {
                $zip->addFile($f->path, $f->nome);
            }


            $zip->close();

            //header("Content-length:" . filesize($zipname)); // questo rende il file corrotto who knows why....
            header("Content-type: application/zip");
            header("Content-Disposition: attachment; filename=" . $zip_name);
            header("Pragma: no-cache");
            header("Expires: 0");
            readfile($zip_location);


            // delete zip folder
            $this->delete_attestati($file_list);


            $this->_japp->close();

        } catch (Exception $e) {

            echo $e;
        }


    }

    public function delete_attestati($file_list)
    {    // delete all files
        foreach ($file_list as $f) {
            if (is_file($f->path))
                unlink($f->path);
        }


    }

    public function clean_folder()
    {


        $files = glob($this->_folder_location . '*'); // get all file names
        foreach ($files as $file) { // iterate files
            if (is_file($file))
                unlink($file); // delete file
        }
    }

    public function get_prefisso_corso($id_corso)
    {

        try {

            $query = $this->_db->getQuery(true)
                ->select('u.prefisso_coupon')
                ->from('#__gg_unit AS u ')
                ->where('u.id=' . $id_corso)
                ->setLimit('1');

            $this->_db->setQuery($query);


            if (false === ($result = $this->_db->loadAssoc())) {

                throw new RuntimeException($this->_db->getErrorMsg(), E_USER_ERROR);
            }


            return $result['prefisso_coupon'];

        } catch (Exception $e) {
            DEBUGG::error($e, 'get_prefisso_corso');
        }


    }

}
