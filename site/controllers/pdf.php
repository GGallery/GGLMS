<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

require_once JPATH_COMPONENT . '/models/contenuto.php';
require_once JPATH_COMPONENT . '/models/libretto.php';
require_once JPATH_COMPONENT . '/models/unita.php';
require_once JPATH_COMPONENT . '/models/pdf.php';
require_once JPATH_COMPONENT . '/controllers/attestatibulk.php';


/**
 * Controller for single contact view
 *
 * @since  1.5.19
 */
class gglmsControllerPdf extends JControllerLegacy
{

    private $_user;
    private $_japp;
    public $_params;
    public $_folder_location = JPATH_COMPONENT . '/models/tmp/';

    public function __construct($config = array())
    {
        parent::__construct($config);
        $this->_japp = JFactory::getApplication();
        $this->_params = $this->_japp->getParams();
        $this->_user = JFactory::getUser();


        define('SMARTY_DIR', JPATH_COMPONENT . '/libraries/smarty/smarty/');
        define('SMARTY_COMPILE_DIR', JPATH_COMPONENT . '/models/cache/compile/');
        define('SMARTY_CACHE_DIR', JPATH_COMPONENT . '/models/cache/');
        define('SMARTY_TEMPLATE_DIR', JPATH_COMPONENT . '/models/templates/');
        define('SMARTY_CONFIG_DIR', JPATH_COMPONENT . '/models/');
        define('SMARTY_PLUGINS_DIRS', JPATH_COMPONENT . '/libraries/smarty/extras/');

    }

    // se $generate_pdf = false il metodo ritorna id dati per generare il pdf
    // il default == true --> stampa il pdf
    // aggiunto il riferimento ad id_corso dalla vista di scaricamento dell'attestato
    public function generateAttestato($user_id = null,
                                      $id_content = null,
                                      $generate_pdf = true,
                                      $id_corso = null,
                                      $unita_padre = false)
    {

        try {


            $generate_pdf = isset($generate_pdf) ? $generate_pdf : true;
            $this->_japp = JFactory::getApplication();

            if($unita_padre == true){
                $attestati_bulk = new gglmsControllerAttestatiBulk();
                $array_user = array();
                array_push($array_user,$user_id);
                $attestati_bulk->do_genereate_attestati_multiple($array_user, $id_corso, null, true);

            }else {

                $db = JFactory::getDbo();
                $postData = $this->_japp->input->get;
                $id_elemento = $id_content != null ? $id_content : $postData->get('content', 0, 'int');
                $user_id = $user_id != null ? $user_id : $this->_user->get('id');
                // da vista o come argomento
                $id_corso = $id_corso != null ? $id_corso : $postData->get('id_corso', null);

                if (!$user_id)
                    JFactory::getApplication()->enqueueMessage('Impossibile determinare l\'id dell\'utente ', 'error');

                if (!$id_elemento)
                    JFactory::getApplication()->enqueueMessage('Impossibile determinare l\'id dell\'attestato ', 'error');


                //ATTESTATO CORRENTE
                $query = $db->getQuery(true)
                    ->select('c.* ,t.tipologia as tipologia_contenuto')
                    ->from('#__gg_contenuti as c')
                    ->leftJoin('#__gg_contenuti_tipology as t on t.id=c.tipologia')
                    ->where('c.id = ' . (int)$id_elemento);
                $db->setQuery($query);

                $attestato = $db->loadObject('gglmsModelContenuto');


                if (!$attestato->attestato_path)
                    JFactory::getApplication()->enqueueMessage('Non hai impostato l\'id del contenuto di riferimento per l\'attestato ', 'error');


                //CONTENUTO VERIFICA
                $query = $db->getQuery(true)
                    ->select('c.*
					,t.tipologia as tipologia_contenuto'
                    )
                    ->from('#__gg_contenuti as c')
                    ->leftJoin('#__gg_contenuti_tipology as t on t.id=c.tipologia')
                    ->where('c.id = ' . $attestato->attestato_path);
                $db->setQuery($query);


                $contenuto_verifica = $db->loadObject('gglmsModelContenuto');

                //UNITA PADRE
                $query = $db->getQuery(true)
                    ->select('*')
                    ->from('#__gg_unit as u')
                    ->where('u.id = ' . $attestato->getUnitPadre());


                $db->setQuery($query);
                $unita = $db->loadObject('gglmsModelUnita');


                //DIRETTORE GENERALE
                $query = $db->getQuery(true)
                    ->select('d.dg')
                    ->from('#__usergroups_details d')
                    ->where('d.dominio = "' . DOMINIO . '"');
                $db->setQuery($query);
                $dg = $db->loadResult();


                //TRACKLOG

                $corso_obj = null;
                if (!$unita->is_corso) {
                    $corso_obj = $unita->find_corso($unita->id);
                    $corso_obj = $db->loadObject('gglmsModelUnita');

                } else {

                    $corso_obj = $unita;
                }


                /// COUPON recupero il coupon a partire da utente e gruppo corso
                $gac = $unita->get_gruppo_accesso_corso($corso_obj->id);

                $query = $db->getQuery(true)
                    ->select("c.coupon")
                    ->from('#__gg_coupon c')
                    ->where('id_utente = "' . $user_id . '"')
                    ->where('id_gruppi = "' . $gac . '"');
                $db->setQuery($query);
                $coupon = $db->loadResult();


                // DATI CORSO
                // aggiunto order per eventuali ripetizioni in modo da prendere l'ultima inserita
                // vanno inclusi anche corsi non completati
                $dati_corso = null;
                if (!is_null($id_corso)) {

                    $query = $db->getQuery(true)
                        ->select('COALESCE(r.data_inizio, "") as data_inizio, 
                            COALESCE(r.data_inizio, "") as data_inizio_corso, 
                            COALESCE(r.data_fine, "") as data_fine,
                            COALESCE(r.data_fine, "") as data_fine_corso,
                            COALESCE(c.titolo, "") as titolo, 
                            COALESCE(c.prefisso_coupon, "") as codice_corso')
                        ->from('#__gg_view_stato_user_corso as r')
                        ->join('inner', '#__gg_report_users as ru on r.id_anagrafica = ru.id')
                        ->join('left', '#__gg_unit as c on r.id_corso = c.id')
                        ->where('ru.id_user = ' . $user_id)
                        ->where('r.id_corso = ' . $id_corso)
                        ->order('r.timestamp desc');

                    $db->setQuery($query);
                    $dati_corso = $db->loadObjectList();
                }

                $tracklog = null;
                if ($corso_obj->accesso == 'gruppo') {

                    $stampa_tracciato = $corso_obj->isStampaTracciato($user_id);

                    if ($stampa_tracciato == 1) {
//
                        $all_contents = $corso_obj->getAllContentsByCorso();

                        $tracklog = array();
                        foreach ($all_contents as $c) {

                            // carico il contenuto come oggetto
                            $query = $db->getQuery(true)
                                ->select('*')
                                ->from('#__gg_contenuti c')
                                ->where('id =' . $c['id']);
                            $db->setQuery($query);
                            $gg_content = $db->loadObject('gglmsModelContenuto');
                            $gg_content->setUserContent($user_id, $c['id']);
                            $permanenza = $gg_content->getPermanenza_tot($c['id'], $user_id);


                            //TRACKLOG
                            $item = new stdClass();
                            $scorm_vars = $gg_content->getStato($user_id);
                            $item->titolo = $gg_content->titolo;
                            $item->permanenza = $permanenza; //$scorm_vars->permanenza;
                            $item->data = $scorm_vars->data;

                            array_push($tracklog, $item);
                        }
                    }


                }


                $model_user = new gglmsModelUsers();
                $user = $model_user->get_user($user_id, $unita->id_event_booking);

                if ($this->_params->get('verifica_cf')) {


                    switch ($this->_params->get('integrazione')) {
                        case 'eb':
                            $integrazione = 'eb';
                            $campo_integrazione = $this->_params->get('campo_event_booking_controllo_cf');
                            $cf = $user->fields[$campo_integrazione];
                            break;

                        case 'cb':
                            $integrazione = 'cb';
                            $campo_integrazione = $this->_params->get('campo_community_builder_controllo_cf');
                            $cf = $user->$campo_integrazione;
                            break;

                        default:
                            echo "Componente di integrazione non specificato in GGLMS oppure non gestito";
                            die();
                    }

                    $conformita = utilityHelper::conformita_cf($cf);
                    if (!$conformita['valido']) {
                        $data_change['integration'] = $integrazione;
                        $data_change['registrant_id'] = $user->id;
                        $data_change['field_id'] = $campo_integrazione;
                        $data_change['codicefiscale'] = $cf;
                        $data_change['return'] = $attestato->alias;
                        $data_change = base64_encode(json_encode($data_change));
                        $app = JFactory::getApplication();


                        // se gli arriva user_id  come  parametro --> è il tutor che scarica l'attesato per l'utente
                        if ($user_id != $this->_user->get('id')) {
                            $data_change = base64_encode(json_encode($user));
                            // tutor, redirect a pagina di errore
                            $this->_japp->redirect(JRoute::_('index.php?option=com_gglms&view=gglms&layout=nocf&data=' . $data_change));
                        } else {

                            // utente per se stesso, redirect a pagina di aggiorna cf
                            $this->_japp->redirect(JRoute::_('index.php?option=com_gglms&view=gglms&layout=mcf&data=' . $data_change));
                        }

                    }

                }


                $orientamento = ($attestato->orientamento != null ? $attestato->orientamento : null);

                //ATECO calcolo codice ateco a partire da utente
                $user_soc = $model_user->get_user_societa($user->id, true);
                $tutor_id = $model_user->get_tutor_aziendale($user_soc[0]->id);
                $ateco = '';
                $piattaforma ='';

                if ($tutor_id) {
                    $query = $db->getQuery(true)
                        ->select('c.cb_ateco')
                        ->from('#__comprofiler as c')
                        ->where('c.user_id =' . $tutor_id);
                    $db->setQuery($query);
                    $ateco = $db->loadResult();

                    $queryPiattaforma = $db->getQuery(true)
                        ->select('mud.alias')
                        ->from('#__usergroups_details mud')
                        ->join('inner','#__usergroups mu ON mud.group_id = mu.parent_id ')
                        ->where('mu.id = '.$user_soc[0]->id);
                    $db->setQuery($queryPiattaforma);
                    $piattaforma = $db->loadResult();
                }

                if ($generate_pdf == true) {
                    $model = $this->getModel('pdf');

                    $model->_generate_pdf($user,
                        $orientamento,
                        $attestato,
                        $contenuto_verifica,
                        $dg,
                        $tracklog,
                        $ateco,
                        $coupon,
                        $piattaforma,
                        false,
                        $dati_corso);

                } else {


                    $result_user = new stdClass();
                    $result_user->user = $user;
                    $result_user->orientamento = $orientamento;
                    $result_user->attestato = $attestato;
                    $result_user->contenuto_verifica = $contenuto_verifica;
                    $result_user->dg = $dg;
                    $result_user->tracklog = $tracklog;
                    $result_user->ateco = $ateco;
                    $result_user->dati_corso = $dati_corso;
                    // per modifica tipologia coupon
                    $result_user->coupon = $coupon;
                    return $result_user;

                }

            }


        } catch (Exception $e) {

            DEBUGG::log($e, 'Exception in generateAttestato ', 1);
        }
        $this->_japp->close();
    }

    public function generate_libretto()
    {
        try {
            $user_id = JRequest::getVar('user_id');
            if ($user_id) {

                $model = $this->getModel('pdf');
                $modelLibretto = new gglmsModelLibretto();
                $data = $modelLibretto->get_data($user_id);
                $user = $modelLibretto->get_user($user_id);
                $model->_generate_libretto_pdf($data['rows'], $user);
            }
        } catch (Exception $e) {

            DEBUGG::log($e, 'Exception in generateAttestato ', 1);
        }
        $this->_japp->close();
    }

    public function getDataForAttestato_multi($user_id_list, $id_content = null, $id_corso = null)
    {


        try {
            // todo validazione
//        $id_elemento = $id_content; //$id_content != null ? $id_content : $postData->get('content', 0, 'int');
//         $user_id = $user_id != null ? $user_id : $this->_user->get('id');

            $result = array();
            foreach ($user_id_list as $user_id) {

                $res = $this->generateAttestato($user_id, $id_content, false, $id_corso);
                array_push($result, $res);

            }

            return $result;
        } catch (Exception $e) {

            DEBUGG::log($e, 'Exception in generateAttestato ', 1);
        }

    }


}
