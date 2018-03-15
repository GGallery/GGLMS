<?php

/**
 * WebTVContenuto Model
 *
 * @package    Joomla.Components
 * @subpackage WebTV
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');
require_once JPATH_COMPONENT . '/models/contenuto.php';
require_once JPATH_COMPONENT . '/models/unita.php';
require_once JPATH_COMPONENT . '/models/coupon.php';
require_once JPATH_COMPONENT . '/models/users.php';


/**
 * WebTVContenuto Model
 *
 * @package    Joomla.Components
 * @subpackage WebTV
 */
require_once JPATH_COMPONENT . '/models/syncviewcarigelearningbatch.php';
class gglmsModelReportUtente extends JModelLegacy {

	private $_dbg;
	private $_app;
	private $_userid;
	protected $params;
	protected  $_db;


	public function __construct($config = array()) {
		parent::__construct($config);

		//$user = JFactory::getUser();
		//$this->_userid = $user->get('id');

		$this->_db = JFactory::getDbo();

		$this->_app = JFactory::getApplication('site');
		//$this->params = $this->_app->getParams();


		//$this->populateState();
	}

	public function __destruct() {

	}

    public function get_data($user){

        try {
            $carigemodel=new gglmsModelSyncViewCarigeLearningBatch();
            $userid=$user['id_user'];
            $query = $this->_db->getQuery(true);
            $query->select('v.id_corso,v.id_anagrafica as id_anagrafica, u.titolo as corso, v.stato as stato, DATE_FORMAT(u.data_fine,\'%d/%m/%Y\') as data_fine');
            $query->from('#__gg_view_stato_user_corso as v');
            $query->join('inner', '#__gg_unit as u on v.id_corso=u.id');
            $query->join('inner', '#__gg_report_users as anagrafica on v.id_anagrafica=anagrafica.id');
            $query->where('anagrafica.id_user=' . $userid);
            //var_dump((string)$query);die;
            $this->_db->setQuery($query);
            $rows = $this->_db->loadAssocList();

            foreach ($rows as &$row){
                $row['percentuale_completamento']=number_format($carigemodel->percentualeCompletamento($row['id_corso'],$row['id_anagrafica']),2);
            }

            $result['query'] =(string)$query;
            $result['rows'] = $rows;

            return $result;
        }catch (exceptions $e){

            DEBUGG::log('ERRORE DA GETDATA','ERRORE DA GET DATA',1,1);
        }
    }

    public function get_nome($userid){

        try {

            $query = $this->_db->getQuery(true);
            $query->select('name');
            $query->from('#__users as u');
            $query->where('u.id=' . $userid);
            $this->_db->setQuery($query);
            $result = $this->_db->loadResult();

            return $result;
        }catch (exceptions $e){

            DEBUGG::log('ERRORE DA GETDATA','ERRORE DA GET DATA',1,1);
        }

    }
    public function get_user($userid){

        try {

            $query = $this->_db->getQuery(true);
            $query->select('nome, cognome, id_user');
            $query->from('#__gg_report_users as u');
            $query->where('u.id_user=' . $userid);
            $this->_db->setQuery($query);
            $result = $this->_db->loadAssocList();
            return $result[0];
        }catch (exceptions $e){
            DEBUGG::log('ERRORE DA GETUSER','ERRORE DA GET USER',1,1);
        }
    }

    public function _generate_pdf($user, $unita_id,$datetest) {

        try {
            require_once JPATH_COMPONENT . '/libraries/pdf/certificatePDF.class.php';
            $pdf = new certificatePDF();
            $info['data_superamento']=$datetest;
            $info['path_id'] = $unita_id;
            $info['path'] = $_SERVER['DOCUMENT_ROOT'].'/mediagg/image/unit/';
            $info['content_path'] = $info['path'] . $info['path_id'];
            $template = "file:" . $_SERVER['DOCUMENT_ROOT'].'/mediagg/image/unit/'. $unita_id . "/" . $unita_id . ".tpl";
            $pdf->add_data($user);
            $pdf->add_data($info);
            $nomefile = "attestato_" . $user['nome'] . "_" . $user['cognome'] . ".pdf";
            $pdf->fetch_pdf_template($template, null, true, false, 0);
            $pdf->Output($nomefile, 'D');
            return 1;
        } catch (Exception $e) {
            // FB::log($e);
            DEBUGG::error($e, 'error generate_pdf');
        }
        return 0;
    }

}

