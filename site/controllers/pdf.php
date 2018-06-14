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

/**
 * Controller for single contact view
 *
 * @since  1.5.19
 */
class gglmsControllerPdf extends JControllerLegacy
{

    private $_user;
    private $_japp;
    public  $_params;

    public function __construct($config = array())
    {
        parent::__construct($config);
        $this->_japp = JFactory::getApplication();
        $this->_params = $this->_japp->getParams();
        $this->_user =   JFactory::getUser();


        define('SMARTY_DIR', JPATH_COMPONENT.'/libraries/smarty/smarty/');
        define('SMARTY_COMPILE_DIR', JPATH_COMPONENT.'/models/cache/compile/');
        define('SMARTY_CACHE_DIR', JPATH_COMPONENT.'/models/cache/');
        define('SMARTY_TEMPLATE_DIR', JPATH_COMPONENT.'/models/templates/');
        define('SMARTY_CONFIG_DIR', JPATH_COMPONENT.'/models/');
        define('SMARTY_PLUGINS_DIRS', JPATH_COMPONENT.'/libraries/smarty/extras/');

    }

    public function generateAttestato() {

        try {
            $db = JFactory::getDbo();
            $postData = $this->_japp->input->get;
            $id_elemento = $postData->get('content', 0, 'int');

            if (!$id_elemento)
                JFactory::getApplication()->enqueueMessage('Impossibile determinare l\'id dell\'attestato ', 'error');

            //ATTESTATO CORRENTE
            $query = $db->getQuery(true)
                ->select('c.*
					,t.tipologia as tipologia_contenuto'
                )
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

            $model_user = new gglmsModelUsers();
            $user = $model_user->get_user($this->_user->get('id'), $unita->id_event_booking);

            if($this->_params->get('verifica_cf')) {



                switch ($this->_params->get('integrazione')){
                    case 'eb':
                        $integrazione= 'eb';
                        $campo_integrazione = $this->_params->get('campo_event_booking_controllo_cf');
                        $cf = $user->fields[$campo_integrazione];
                        break;

                    case 'cb':
                        $integrazione= 'cb';
                        $campo_integrazione = $this->_params->get('campo_community_builder_controllo_cf');
                        $cf = $user->$campo_integrazione;
                        break;

                    default:
                        echo "Componente di integrazione non specificato in GGLMS oppure non gestito";
                        die();
                }

//                DEBUGG::error($cf, 'cf', 1);

                $conformita = utilityHelper::conformita_cf($cf);
                if(!$conformita['valido']) {
                    $data_change['integration']=$integrazione;
                    $data_change['registrant_id'] = $user->id;
                    $data_change['field_id']= $campo_integrazione;
                    $data_change['codicefiscale'] = $cf;
                    $data_change['return'] = $attestato->alias;
                    $data_change=base64_encode(json_encode($data_change));
                    $app = JFactory::getApplication();
                    $app->redirect(JRoute::_('index.php?option=com_gglms&view=gglms&layout=mcf&data='.$data_change));
                }
            }

            $model = $this->getModel('pdf');

            $orientamento =  ($attestato->orientamento!=null? $attestato->orientamento:null);

            $model->_generate_pdf($user, $orientamento,$attestato, $contenuto_verifica);

        }catch (Exception $e){

            DEBUGG::log($e, 'Exception in generateAttestato ', 1);
        }
        $this->_japp->close();
    }

    public function generate_libretto(){
        try{
            $user_id = JRequest::getVar('user_id');
            if($user_id) {

                $model = $this->getModel('pdf');
                $modelLibretto=new gglmsModelLibretto();
                $data=$modelLibretto->get_data($user_id);
                $user=$modelLibretto->get_user($user_id);
                $model->_generate_libretto_pdf($data['rows'],$user);
            }
        }catch (Exception $e){

            DEBUGG::log($e, 'Exception in generateAttestato ', 1);
        }
        $this->_japp->close();
    }
}
