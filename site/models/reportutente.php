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
            $query->select('v.id_corso,v.id_anagrafica as id_anagrafica, u.titolo as corso, v.stato as stato, DATE_FORMAT(u.data_fine,\'%d/%m/%Y\') as data_fine, DATE_FORMAT(v.data_fine,\'%Y/%m/%d\') as data_superamento');
            $query->from('#__gg_view_stato_user_corso as v');
            $query->join('inner', '#__gg_unit as u on v.id_corso=u.id');
            $query->join('inner', '#__gg_report_users as anagrafica on v.id_anagrafica=anagrafica.id');
            $query->where('anagrafica.id_user=' . $userid);


            $this->_db->setQuery($query);
            $rows = $this->_db->loadAssocList();

            if($rows){
                foreach ($rows as &$row){
                    $row['percentuale_completamento']=number_format($carigemodel->percentualeCompletamento($row['id_corso'],$row['id_anagrafica']),2);
                    $query = $this->_db->getQuery(true);
                    $query->select("id from #__gg_contenuti where attestato_path=(select id_contenuto_completamento from #__gg_unit where id=".$row['id_corso'].")");
                    $this->_db->setQuery($query);
                    $row['attestato_id']=$this->_db->loadResult();
                }

                $result['query'] =(string)$query;
                $contenuti_id=array_column($rows,'attestato_id');

                foreach ($contenuti_id as $key=>&$contenuto_id){

                    if($contenuto_id==null)
                        //       var_dump($contenuto_id);
                        unset($contenuti_id[$key]);
                }
                $result['attestati_intermedi']=[];
                if(count($contenuti_id)>0) {

                    $query = $this->_db->getQuery(true);
                    $query->select('id,titolo');
                    $query->from('#__gg_contenuti');
                    $query->where('tipologia=5 and id not in (' . implode(',', $contenuti_id) . ')');

                    // echo $query;
                    //die;

                    $this->_db->setQuery($query);
                    $attestati = $this->_db->loadAssocList();


                    foreach ($attestati as $attestato) {

                        if ($this->getPropedeuticita($attestato['id'])) {
                            array_push($result['attestati_intermedi'], $attestato);
                        }
                    }
                }
                $result['rows'] = $rows;
                return $result;
            }else{
                return null;

            }


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
            $query->select('nome, cognome, id_user, fields');
            $query->from('#__gg_report_users as u');
            $query->where('u.id_user=' . $userid);
            $this->_db->setQuery($query);
            $result = $this->_db->loadAssocList();
            return $result[0];
        }catch (exceptions $e){
            DEBUGG::log('ERRORE DA GETUSER','ERRORE DA GET USER',1,1);
        }
    }

    public function _generate_pdf($user_,$orientamento, $unita_id,$data_superamento) {



        try {
            require_once JPATH_COMPONENT . '/libraries/pdf/certificatePDF.class.php';
            $orientation=$orientamento;
            $pdf = new certificatePDF($orientation);
            $info['data_superamento']=$data_superamento;
            $info['path_id'] = $unita_id;
            $info['path'] = $_SERVER['DOCUMENT_ROOT'].'/mediagg/images/unit/';
            $info['content_path'] = $info['path'] . $info['path_id'];
            $template = "file:" . $_SERVER['DOCUMENT_ROOT'].'/mediagg/images/unit/'. $unita_id . "/" . $unita_id . ".tpl";
            //$user['Luogodinascita']='Trocopinzo';
            //$user['Datadinascita']='01/02/2016';
            $user['nome']=$user_['nome'];
            $user['cognome']=$user_['cognome'] ;

            $user['Luogodinascita']=json_decode($user_['fields'])->Luogodinascita;
            $user['Datadinascita']=json_decode($user_['fields'])->Datadinascita;
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

    private function getPropedeuticita($id_attestato){

        $query = $this->_db->getQuery(true);
        $query->select('prerequisiti');
        $query->from('#__gg_contenuti');
        $query->where('id='.$id_attestato);
        $this->_db->setQuery($query);
        $prerequisiti = $this->_db->loadResult();

        if($prerequisiti) {
            foreach (explode(",", $prerequisiti) as $idprerequisito) {
                $model_prerequisito = new gglmsModelContenuto();
                $prerequisito = $model_prerequisito->getContenuto($idprerequisito);
                if (!$prerequisito->getStato()->completato)
                    return  false;
            }
        }
        return true;
    }

}

