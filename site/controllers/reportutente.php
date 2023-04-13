<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

require_once JPATH_COMPONENT . '/models/users.php';
require_once JPATH_COMPONENT . '/controllers/pdf.php';

/**
 * Controller for single contact view
 *
 * @since  1.5.19
 */
class gglmsControllerReportUtente extends JControllerLegacy
{
    protected $_db;
    private $_app;
    private $params;
    private $_filterparam;
    private $user;
    private $user_id;

    public function __construct($config = array())
    {
        parent::__construct($config);

        $this->_app = JFactory::getApplication();
        $this->_db = JFactory::getDbo();
        $this->_filterparam = new stdClass();
        $this->_filterparam->user_id = JRequest::getVar('user_id');
        $this->_filterparam->unita_id = JRequest::getVar('unita_id');
        $this->_filterparam->datetest = JRequest::getVar('datetest');
        $this->_filterparam->data_superamento = JRequest::getVar('data_superamento');

        define('SMARTY_DIR', JPATH_COMPONENT . '/libraries/smarty/smarty/');
        define('SMARTY_COMPILE_DIR', JPATH_COMPONENT . '/models/cache/compile/');
        define('SMARTY_CACHE_DIR', JPATH_COMPONENT . '/models/cache/');
        define('SMARTY_TEMPLATE_DIR', JPATH_COMPONENT . '/models/templates/');
        define('SMARTY_CONFIG_DIR', JPATH_COMPONENT . '/models/');
        define('SMARTY_PLUGINS_DIRS', JPATH_COMPONENT . '/libraries/smarty/extras/');
//        $this->params = $this->_app->getParams();

        $this->_japp = JFactory::getApplication();
        $this->_params = $this->_japp->getParams();


        JHtml::_('stylesheet', 'components/com_gglms/libraries/css/debugg.css');


    }

    public function get_report_utente()
    {

        try {

            $model = $this->getModel('reportutente');

            return $model->get_data($this->get_user());
        } catch (exceptions $ex) {
            DEBUGG::log('ERRORE DA get_libretto', 'gglmsControllerLibretto', 1, 1);
        }

    }

    private function get_data($user_id)
    {

        $model = $this->getModel('reportutente');
        return $model->get_data($user_id);

    }

    public function get_user()
    {

        try {
            if (!$this->_filterparam->user_id) {
                $user = JFactory::getUser();
                $user_id = $user->id;
            } else {

                $user_id = $this->_filterparam->user_id;
            }

            $model = $this->getModel('reportutente');
            $user = $model->get_user($user_id);
            return $user;

        } catch (exceptions $ex) {
            DEBUGG::log('ERRORE DA get_libretto', 'gglmsControllerLibretto', 1, 1);

        }
    }

    public function generateAttestato()
    {

        try {


           $data_superamento=$this->_filterparam->data_superamento;
            $postData = $this->_japp->input->get;
            $content_id = $postData->get('content_id', null, 'int');
            $user_id = $postData->get('user_id', null, 'int');
            $id_corso = $postData->get('id_corso', null, 'int');
            $unita_padre = false;

            if(is_null($content_id)){

                $query = $this->_db->getQuery(true)
                    ->select('un.is_corso')
                    ->from('#__gg_unit un')
                    ->where('un.unitapadre = ' . $this->_db->quote($id_corso));
                $this->_db->setQuery($query);
                $is_corso = $this->_db->loadAssoc();

                if(count($is_corso) === 0) {

                    $query = $this->_db->getQuery(true)
                        ->select('cont.id')
                        ->from('#__gg_unit un')
                        ->join('inner', '#__gg_unit_map map ON un.id = map.idunita')
                        ->join('inner', '#__gg_contenuti cont ON map.idcontenuto = cont.id')
                        ->where('un.id = ' . $this->_db->quote($id_corso))
                        ->where('cont.tipologia = 5');

                    $this->_db->setQuery($query);
                    $result = $this->_db->loadAssoc();

                }elseif (count($is_corso) > 0){

                    $query = $this->_db->getQuery(true)
                        ->select('cont.id')
                        ->from('#__gg_unit un')
                        ->join('inner', '#__gg_unit_map map ON un.id = map.idunita')
                        ->join('inner', '#__gg_contenuti cont ON map.idcontenuto = cont.id')
                        ->where('un.unitapadre = ' . $this->_db->quote($id_corso))
                        ->where('cont.tipologia = 5');

                    $this->_db->setQuery($query);
                    $result = $this->_db->loadAssoc();

                    $unita_padre = true;

                }else{
                    throw new Exception("Corso on esiste!", 1);
                }

                $content_id = (int)$result['id'];
            }

            $pdf_ctrl = new gglmsControllerPdf();
            $pdf_ctrl->generateAttestato($user_id, $content_id, true, $id_corso, $unita_padre);


        } catch (Exception $e) {

            DEBUGG::log($e, 'Exception in generateAttestato ', 1);
        }
        $this->_app->close();
    }

}
