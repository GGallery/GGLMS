<?php

/**
 * WebTVContenuto Model
 *
 * @package    Joomla.Components
 * @subpackage WebTV
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

require_once JPATH_COMPONENT . '/models/stato.php';


/**
 * WebTVContenuto Model
 *
 * @package    Joomla.Components
 * @subpackage WebTV
 */
class gglmsModelContenuto extends JModelLegacy {

    private $_app;
    private $_id; //id dell'oggetto
    protected $_db;
    public $_userid;
    public $_path; //percorso dell'oggetto
    public $_params;


    public function __construct($config = array()) {
        parent::__construct($config);

        $user = JFactory::getUser();
        $this->_userid = $user->get('id');

        $this->_db = JFactory::getDBO();

        $this->_app = JFactory::getApplication('site');
        $this->_params = $this->_app->getParams();

    }

    public function __destruct() {}

    public function getPropedeuticita(){
        if($this->prerequisiti) {
            foreach (explode(",", $this->prerequisiti) as $idprerequisito) {
                $model_prerequisito = new gglmsModelContenuto();
                $prerequisito = $model_prerequisito->getContenuto($idprerequisito);
                if (!$prerequisito->getStato()->completato)
                    return  false;
            }
        }
        return true;
    }

    protected function populateState()
    {
        $app = JFactory::getApplication('site');

        // Load state from the request.
        $pk = $app->input->getInt('id');
        $this->setState('contenuto.id', $pk);

        $offset = $app->input->getUInt('limitstart');
        $this->setState('list.offset', $offset);

        // Load the parameters.
        $params = $app->getParams();
        $this->setState('params', $params);


        $this->setState('filter.language', JLanguageMultilang::isEnabled());
    }

    public function getContenuto($id = null){

        $this->_id = (!empty($id)) ? $id : (int) $this->getState('contenuto.id');

        try
        {
            $query = $this->_db->getQuery(true)
                ->select('c.*,t.tipologia as tipologia_contenuto')
                ->from('#__gg_contenuti as c')
                ->leftJoin('#__gg_contenuti_tipology as t on t.id=c.tipologia')
                ->where('c.id = ' . (int) $this->_id);


            $this->_db->setQuery($query);

            $data = $this->_db->loadObject('gglmsModelContenuto');

            if (empty($data))
            {
                DEBUGG::log('contenuto non trovato, id: '.$id, 'error in getContenuto' , 0,1,0);
                return null;
            }
        }
        catch (Exception $e)
        {
            DEBUGG::query($query, 'query get contenuto');
            DEBUGG::log($e->getMessage(), 'error in getContenuto' , 0,1,0);
        }
        return $data;

    }

    public function getJumperXML() {
        try {

            $xml_path = PATH_CONTENUTI.'/'.$this->id. '/'.$this->id.'.xml';
            if (!file_exists($xml_path)) {
                JFactory::getApplication()->enqueueMessage('Impossibile trovare l\'xml per il contenuto "'. $this->titolo.'" al path '. $this->_path.'', 'error');
                return array();
            }

            // if (empty($itemid) || !filter_var($itemid, FILTER_VALIDATE_INT))
            //     throw new BadMethodCallException('Parametro non valido, atteso un intero valido - Jumper', E_USER_ERROR);
            // if (empty($jumpers) || !is_array($jumpers))
            //     throw new BadMethodCallException('Parametro non valido, atteso un array valido - Jumper', E_USER_ERROR);


            $jumpers = array();
            $xml = new DOMDocument();
            $xml->load($xml_path);
            $cue_points = $xml->getElementsByTagName('CuePoint');
            $i = 0;


            foreach ($cue_points as $point) {
                foreach ($point->childNodes as $node) {
                    if ('Time' == $node->nodeName)
                        $jumpers[$i]['tstart'] = $node->nodeValue;
                    elseif ('Name' == $node->nodeName)
                        $jumpers[$i]['titolo'] = $node->nodeValue;
                }
                $i++;/** @todo se il nodo non contiene time e name incremento i e non faccio nessun controllo se il jumper abbia 2 elementi tstart e titolo */
            }
            unset($xml);
            unset($cue_points);

            return $jumpers;
        } catch (Exception $e) {
            DEBUGG::error($e, "error");
        }
        return 0;
    }

    public function getUrlLink(){

        switch($this->tipologia){

            case 1: //videoslide
            case 2: //videoslide
            case 3: //allegati
            case 4: //Scorm
            case 5:	//attestato
            case 6: //Testuale HTML
            case 9: //pdfsingolo


                $url="index.php?option=com_gglms&view=contenuto&alias=".$this->alias;
                $url = "href='". JRoute::_($url)."'";

                return $url;
                break;

            case 7: //quixdeluxe
                $url="index.php?option=com_joomlaquiz&view=quiz&quiz_id=".$this->id_quizdeluxe;
                $url = "href='". JRoute::_($url)."'";
                return $url;
                break;

            case 8: //sso
                $user = JFactory::getUser();
                $userid = $user->get('id');
                $username = $user->get('name');
                $email = $user->get('email');

                $array['id'] = $userid;
                $array['nome'] = $username;
                $array['cognome'] = $username;
                $array['email'] = $email;
                $array['username'] = $username;
                $array['password'] = $username;

                $serialize=base64_encode(serialize($array));

                $url = $this->path."?sso=dekra&data=".$serialize.">";
                $url = "href='". $url ."' target='_blank' ";

                return $url;
                break;

        }
    }

    public function createVTT_slide($jumpers) {

        // if (empty($itemid) || !filter_var($itemid, FILTER_VALIDATE_INT))
        //     throw new BadMethodCallException('Parametro non valido, atteso un intero valido - VTT', E_USER_ERROR);
        // if (empty($jumpers) || !is_array($jumpers))
        //     throw new BadMethodCallException('Parametro non valido, atteso un array valido - VTT', E_USER_ERROR);



        try {
            $path = PATH_CONTENUTI . '/' . $this->id;
            $filepath = JPATH_BASE . "/" . $path . "/";


            //if (!file_exists($filepath . "vtt_slide.vtt")) {
            $values = array();
            $i = 0;
            $vtt = "";
            $pathimmagini = "../../../../" . $path;



            foreach ($jumpers AS $jumper) {
                //$values[] = '(' . $itemid . ', ' . $jumper['tstart'] . ', \'' . $jumper['titolo'] . '\')';
                $values[$i]['a'] = "$i\n";
                $values[$i]['b'] = $this->convertiDurata($jumper['tstart']);
                $values[$i]['c'] = NULL;
                $shift = $i + 1;
                $values[$i]['d'] = $pathimmagini . "/slide/Slide" . $shift . ".jpg\n\n";
//				echo $pathimmagini . "/slide/Slide" . $shift . ".jpg\n\n";
                $i++;
            }




            for ($i = 0; $i < count($values); $i++) {
                if ($i == 0)
                    $values[$i]['b'] = "00:00:00";

                if ($i != count($values))
                    $values[$i]['c'] = $values[$i + 1]['b'] . "\n";

                if ($i == count($values) - 1)
                    $values[$i]['c'] = "99:00:00\n";

                $vtt .= $values[$i]['a'] . $values[$i]['b'] . " --> " . $values[$i]['c'] . $values[$i]['d'];
            }



            $file = $filepath . "vtt_slide.vtt";
        }

        catch (Exception $e){
            var_dump($e);
        }

        $var = fopen($file, "w");
        fwrite($var, $vtt);
        fclose($var);
        //}
        return 0;
    }

    public function convertiDurata($durata) {
        $h = floor($durata / 3600);
        $m = floor(($durata % 3600) / 60);
        $s = ($durata % 3600) % 60;
        $result = sprintf('%02d:%02d:%02d', $h, $m, $s);

        return $result;
    }

    public function getUnitPadre(){

        try {
            $query = $this->_db->getQuery(true)
                ->select('idunita')
                ->from('#__gg_unit_map as m')
                ->where('idcontenuto = ' . $this->id);

            $this->_db->setQuery($query);
            $data = $this->_db->loadResult();

            return $data;
        }catch (Exception $e){
            DEBUGG::log($e->getMessage(), 'error in getUnitPAdre' , 0,1,0);
        }
    }

    public function getStato($user_id = null){

        try {

            //Se passo come parametro lo user_id, invece che avere lo stato per l'utente corrente lo avrò per quell'id. Mi serve per i report
            if ($user_id)
                $this->_userid = $user_id;


//		RESTITUISCO UN OGGETTO STATO
            switch ($this->tipologia) {
                case 3: //allegati
                    $data = $this->getStato_allegati();
                    return $data;
                    break;

                case 1: //videoslide
                case 2: //solovideo
                case 4: //scorm
                case 6: //testuale
                case 9: //pdfsingolo
                    $data = $this->getStato_scorm();
                    return $data;
                    break;

                case 5:    //attestato
                    $data = $this->getStato_attestato();
                    return $data;
                    break;

                case 7: //quizdeluxe
                    $data = $this->getStato_quiz_deluxe();

                    return $data;
                    break;

                case 8:  //ss0
                    return $this->getStato_SSO();
                    break;

            }
        }catch (Exception $e){
            DEBUGG::log($e->getMessage(), 'error in getStato' , 0,1,0);
        }
    }

    private function getStato_SSO(){
        try {
            $stato = new gglmsModelStatoContenuto();
            return $stato->format_sso();
        }catch (Exception $e){
            DEBUGG::log($e->getMessage(), 'error in geStato_SSO' , 0,1,0);
        }
    }

    private function getStato_attestato(){

        try {
            if ($this->_userid == null) {
                JFactory::getApplication()->enqueueMessage('Impossibile visualizzare il contentuo "' . $this->titolo . '" senza essere loggati', 'error');
                return 0;
            }

            $query = $this->_db->getQuery(true)
                ->select('varName, varValue')
                ->from('#__gg_scormvars as s')
                ->where('scoid = ' . $this->id)
                ->where('s.userid = ' . $this->_userid);


            $this->_db->setQuery($query);
            $data = $this->_db->loadObjectList('varName');

            $stato = new gglmsModelStatoContenuto();

            return $stato->format_attestato($data);

        }catch (Exception $e){
            DEBUGG::log($e->getMessage(), 'error in getStato_attestato' , 0,1,0);
        }
    }

    private function getStato_quiz_deluxe(){

        try {
            if ($this->id_quizdeluxe == null) {
                JFactory::getApplication()->enqueueMessage('Impossibile determinare l\'id del quiz per il contentuo "' . $this->titolo . '""', 'error');
                return 0;
            }

            if ($this->_userid == null) {
                JFactory::getApplication()->enqueueMessage('Impossibile determinare l\'id del quiz per il contentuo "' . $this->titolo . '" senza essere loggati', 'error');
                return 0;
            }

            $query = $this->_db->getQuery(true)
                ->select('*')
                ->from('#__quiz_r_student_quiz as q')
                ->where('q.c_quiz_id = ' . (int)$this->id_quizdeluxe)
                ->where('q.c_student_id = ' . $this->_userid)
                ->order('q.c_passed desc')
                ->order('c_date_time')
                ->setLimit(1);

            $this->_db->setQuery($query);
            $data = $this->_db->loadObject();

            $stato = new gglmsModelStatoContenuto();

            return $stato->format_quiz_deluxe($data);
        }catch (Exception $e){
            DEBUGG::log($e->getMessage(), 'error in getStato_quiz_deluxe' , 0,1,0);
        }
    }

    private function getStato_allegati(){

        try {
            if ($this->_userid == null) {
//			COMMENTATO IN FASE DI SVIL☺PO
                JFactory::getApplication()->enqueueMessage('Impossibile determinare l\'id del quiz per il contentuo "' . $this->titolo . '" senza essere loggati', 'error');
                return 0;
            }

            $query = $this->_db->getQuery(true)
                ->select('varName, varValue')
                ->from('#__gg_scormvars as s')
                ->where('scoid = ' . $this->id)
                ->where('s.userid = ' . $this->_userid);


            $this->_db->setQuery($query);
            $data = $this->_db->loadObjectList('varName');

//		DEBUGG::log($data, 'data getStato_allegati');

            $stato = new gglmsModelStatoContenuto();

            return $stato->format_allegati($data);
        }catch (Exception $e){
            DEBUGG::log($e->getMessage(), 'error in getStato_allegati' , 0,1,0);
        }
    }

    private function getStato_scorm(){

        try {
            if ($this->_userid == null) {
                JFactory::getApplication()->enqueueMessage('Impossibile determinare l\'id del quiz per il contentuo "' . $this->titolo . '" senza essere loggati', 'error');
                return 0;
            }

            $query = $this->_db->getQuery(true)
                ->select('varName, varValue,DATE(timestamp) as TimeStamp')
                ->from('#__gg_scormvars as s')
                ->where('scoid = ' . $this->id)
                ->where('s.userid = ' . $this->_userid);

            //echo $query;

            $this->_db->setQuery($query);
            $data = $this->_db->loadObjectList('varName');
            $stato = new gglmsModelStatoContenuto();

            return $stato->format_scorm($data);
        }catch (Exception $e){
            DEBUGG::log($e->getMessage(), 'error in getStato_scorm' , 0,1,0);
        }
    }

    public function getFiles(){

        try {

            $query = $this->_db->getQuery(true)
                ->select('f.*')
                ->from('#__gg_files as f')
                ->innerJoin('#__gg_files_map as m on f.id = m.idfile')
                ->innerJoin('#__gg_contenuti as c on c.id = m.idcontenuto')
                ->where('m.idcontenuto = ' . (int) $this->id)
                ->order('m.ordinamento');


            $this->_db->setQuery($query);

            $data = $this->_db->loadObjectList();

        }
        catch (Exception $e)
        {
            $this->setError($e);
            $data = array();
        }

        return $data;

    }

    public function setStato(){

        try {
            if ($this->tipologia == 4)
                return;

            $stato_attuale = $this->getStato();

            $stato = new gglmsModelStatoContenuto();
            $tmp = new stdClass();

            $tmp->scoid = $this->id;
            $tmp->userid = $this->_userid;

            //ultima visualizzazione
            $tmp->varName = 'cmi.core.last_visit_date';
            $tmp->varValue = date('Y-m-d');
            $stato->setStato($tmp);

            //contatore
            $tmp->varName = 'cmi.core.count_views';
            $tmp->varValue = (int)$stato_attuale->visualizzazioni + 1;
            $stato->setStato($tmp);

            //bookmark
            if (!$stato_attuale->bookmark) {
                $tmp->varName = 'bookmark';
                $tmp->varValue = 0;
                $stato->setStato($tmp);
            }

            //stato
            if (!$stato_attuale->completato) {
                $tmp->varName = 'cmi.core.lesson_status';

                if ($this->mod_track == 1)
                    $tmp->varValue = 'completed';
                else
                    $tmp->varValue = 'init';

                $stato->setStato($tmp);
            }
        }catch (Exception $e){
            DEBUGG::log($e->getMessage(), 'error in setStato' , 0,1,0);
        }
    }

    public function testscorm(){

        echo "opk";


    }


}

