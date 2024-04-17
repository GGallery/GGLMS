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
class gglmsModelContenuto extends JModelLegacy
{

    private $_app;
    private $_id; //id dell'oggetto
    protected $_db;
    public $_userid;
    public $_path; //percorso dell'oggetto
    public $_params;


    public function __construct($config = array())
    {
        parent::__construct($config);

        $user = JFactory::getUser();
        $this->_userid = $user->get('id');

        $this->_db = JFactory::getDBO();

        $this->_app = JFactory::getApplication('site');
        $this->_params = $this->_app->getParams();

    }

    public function __destruct()
    {
    }

    public function getPropedeuticita()
    {
        if ($this->prerequisiti) {
            foreach (explode(",", $this->prerequisiti) as $idprerequisito) {
                $model_prerequisito = new gglmsModelContenuto();
                $prerequisito = $model_prerequisito->getContenuto($idprerequisito);
                if (!$prerequisito->getStato()->completato)
                    return false;
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

    public function getContenuto($id = null)
    {

        $this->_id = (!empty($id)) ? $id : (int)$this->getState('contenuto.id');

        try {
            $query = $this->_db->getQuery(true)
                ->select('c.*,t.tipologia as tipologia_contenuto')
                ->from('#__gg_contenuti as c')
                ->leftJoin('#__gg_contenuti_tipology as t on t.id=c.tipologia')
                ->where('c.id = ' . (int)$this->_id);


            $this->_db->setQuery($query);

            $data = $this->_db->loadObject('gglmsModelContenuto');

            if (empty($data)) {
                DEBUGG::log('contenuto non trovato, id: ' . $id, 'error in getContenuto', 0, 1, 0);
                return null;
            }
        } catch (Exception $e) {
            DEBUGG::query($query, 'query get contenuto');
            DEBUGG::log($e->getMessage(), 'error in getContenuto', 0, 1, 0);
        }
        return $data;

    }

    public function getJumperXML()
    {
        try {

            $xml_path = PATH_CONTENUTI . '/' . $this->id . '/' . $this->id . '.xml';
            if (!file_exists($xml_path)) {
                JFactory::getApplication()->enqueueMessage('Impossibile trovare l\'xml per il contenuto "' . $this->titolo . '" al path ' . $this->_path . '', 'error');
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
                $i++;
                /** @todo se il nodo non contiene time e name incremento i e non faccio nessun controllo se il jumper abbia 2 elementi tstart e titolo */
            }
            unset($xml);
            unset($cue_points);

            return $jumpers;
        } catch (Exception $e) {
            DEBUGG::error($e, "error");
        }
        return 0;
    }

    public function getUrlLink()
    {

        switch ($this->tipologia) {

            case 1: //videoslide
            case 2: //videoslide
            case 3: //allegati
            case 4: //Scorm
            case 5:    //attestato
            case 6: //Testuale HTML
            case 9: //pdfsingolo
            case 10: //webinar


                $url = "index.php?option=com_gglms&view=contenuto&alias=" . $this->alias;
                $url = "href='" . JRoute::_($url) . "'";

                return $url;
                break;

            case 7: //quixdeluxe
                $url = "index.php?option=com_joomlaquiz&view=quiz&quiz_id=" . $this->id_quizdeluxe;
                $url = "href='" . JRoute::_($url) . "'";
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

                $serialize = base64_encode(serialize($array));

                $url = $this->path . "?sso=dekra&data=" . $serialize . ">";
                $url = "href='" . $url . "' target='_blank' ";

                return $url;
                break;

        }
    }

    public function createVTT_slide($jumpers)
    {

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
        } catch (Exception $e) {
            var_dump($e);
        }

        $var = fopen($file, "w");
        fwrite($var, $vtt);
        fclose($var);
        //}
        return 0;
    }

    public function convertiDurata($durata)
    {
        $h = floor($durata / 3600);
        $m = floor(($durata % 3600) / 60);
        $s = ($durata % 3600) % 60;
        $result = sprintf('%02d:%02d:%02d', $h, $m, $s);

        return $result;
    }

    public function getUnitPadre()
    {

        try {
            $query = $this->_db->getQuery(true)
                ->select('idunita')
                ->from('#__gg_unit_map as m')
                ->where('idcontenuto = ' . $this->id);

            $this->_db->setQuery($query);
            $data = $this->_db->loadResult();

            return $data;
        } catch (Exception $e) {
            DEBUGG::log($e->getMessage(), 'error in getUnitPAdre', 0, 1, 0);
        }
    }

    public function getStato($user_id = null)
    {

        try {

            //Se passo come parametro lo user_id, invece che avere lo stato per l'utente corrente lo avrò per quell'id. Mi serve per i report
            if ($user_id != null) {
                $this->_userid = $user_id;
            }


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
                case 10: //pdfsingolo
                    $data = $this->getStato_scorm($this->_userid);
                    return $data;
                    break;

                case 5:    //attestato
                    $data = $this->getStato_attestato();
                    return $data;
                    break;

                case 7: //quizdeluxe
                    $data = $this->getStato_quiz_deluxe($this->_userid, $this->id_quizdeluxe);

                    return $data;
                    break;

                case 8:  //ss0
                    return $this->getStato_SSO();
                    break;

            }
        } catch (Exception $e) {
            DEBUGG::log($e->getMessage(), 'error in getStato', 0, 1, 0);
        }
    }

    private function getStato_SSO()
    {
        try {
            $stato = new gglmsModelStatoContenuto();
            return $stato->format_sso();
        } catch (Exception $e) {
            DEBUGG::log($e->getMessage(), 'error in geStato_SSO', 0, 1, 0);
        }
    }

    private function getStato_attestato()
    {

        try {
            if ($this->_userid == null) {
//                JFactory::getApplication()->enqueueMessage('Impossibile visualizzare il contentuo "' . $this->titolo . '" senza essere loggati', 'error');
                JFactory::getApplication()->enqueueMessage(JText::_('COM_GGLMS_STATO_NOT_POSSIBLE') . $this->titolo . JText::_('COM_GGLMS_STATO_NOT_LOGGED'), 'error');
                return 0;
            }

            $query = $this->_db->getQuery(true)
                ->select('varName, varValue, DATE(timestamp) as TimeStamp, timestamp AS TimeStampExtra')
                ->from('#__gg_scormvars as s')
                ->where('scoid = ' . $this->id)
                ->where('s.userid = ' . $this->_userid);


            $this->_db->setQuery($query);
            $data = $this->_db->loadObjectList('varName');

            $stato = new gglmsModelStatoContenuto();

            return $stato->format_attestato($data);

        } catch (Exception $e) {
            DEBUGG::log($e->getMessage(), 'error in getStato_attestato', 0, 1, 0);
        }
    }

    private function getStato_quiz_deluxe($user_id = null, $quiz_id = null)
    {

        try {
            if ($this->id_quizdeluxe == null && $quiz_id == null) {
                JFactory::getApplication()->enqueueMessage(JText::_('COM_GGLMS_STATO_NOT_POSSIBLE_QUIZ') . $this->titolo . '""', 'error');
                return 0;
            }

            if ($this->_userid == null && $user_id == null) {
                JFactory::getApplication()->enqueueMessage(JText::_('COM_GGLMS_STATO_NOT_POSSIBLE_QUIZ') . $this->titolo . JText::_('COM_GGLMS_STATO_NOT_LOGGED'), 'error');
                return 0;
            }

            $user_id = $user_id != null ? $user_id : $this->_userid;
            $quiz_id = $quiz_id != null ? $quiz_id : $this->_userid;


            $query = $this->_db->getQuery(true)
                ->select('*')
                ->from('#__quiz_r_student_quiz as q')
                ->where('q.c_quiz_id = ' . (int)$quiz_id)
                ->where('q.c_student_id = ' . $user_id)
                ->order('q.c_passed desc')
                ->order('c_date_time')
                ->setLimit(1);

            $this->_db->setQuery($query);
            $data = $this->_db->loadObject();

            $stato = new gglmsModelStatoContenuto();

            return $stato->format_quiz_deluxe($data);
        } catch (Exception $e) {
            DEBUGG::log($e->getMessage(), 'error in getStato_quiz_deluxe', 0, 1, 0);
        }
    }

    private function getStato_allegati()
    {

        try {
            if ($this->_userid == null) {
//                JFactory::getApplication()->enqueueMessage('Impossibile determinare l\'id del quiz per il contentuo "' . $this->titolo . '" senza essere loggati', 'error');
                JFactory::getApplication()->enqueueMessage(JText::_('COM_GGLMS_STATO_NOT_POSSIBLE_QUIZ') . $this->titolo . JText::_('COM_GGLMS_STATO_NOT_LOGGED'), 'error');
                return 0;
            }

            $query = $this->_db->getQuery(true)
                ->select('varName, varValue, DATE(timestamp) as TimeStamp, timestamp AS TimeStampExtra')
                ->from('#__gg_scormvars as s')
                ->where('scoid = ' . $this->id)
                ->where('s.userid = ' . $this->_userid);


            $this->_db->setQuery($query);
            $data = $this->_db->loadObjectList('varName');

            $stato = new gglmsModelStatoContenuto();

            return $stato->format_allegati($data);
        } catch (Exception $e) {
            DEBUGG::log($e->getMessage(), 'error in getStato_allegati', 0, 1, 0);
        }
    }

    public function getStato_scorm($user_id = null)
    {

        try {

            if ($user_id) {
                $user_id = $user_id != null ? $user_id : $this->_userid;
            }

            if ($user_id == null) {
//                JFactory::getApplication()->enqueueMessage('Impossibile determinare l\'id del quiz per il contentuo "' . $this->titolo . '" senza essere loggati', 'error');
                JFactory::getApplication()->enqueueMessage(JText::_('COM_GGLMS_STATO_NOT_POSSIBLE_QUIZ') . $this->titolo . JText::_('COM_GGLMS_STATO_NOT_LOGGED'), 'error');
                return 0;
            }

            $query = $this->_db->getQuery(true)
                ->select('varName, varValue, DATE(timestamp) as TimeStamp, timestamp AS TimeStampExtra')
                ->from('#__gg_scormvars as s')
                ->where('scoid = ' . $this->id)
                ->where('s.userid = ' . $user_id);


            $this->_db->setQuery($query);
            $data = $this->_db->loadObjectList('varName');
            $stato = new gglmsModelStatoContenuto();

            return $stato->format_scorm($data);
        } catch (Exception $e) {
            DEBUGG::log($e->getMessage(), 'error in getStato_scorm', 0, 1, 0);
        }
    }

    public function getFiles()
    {

        try {

            $query = $this->_db->getQuery(true)
                ->select('f.*')
                ->from('#__gg_files as f')
                ->innerJoin('#__gg_files_map as m on f.id = m.idfile')
                ->innerJoin('#__gg_contenuti as c on c.id = m.idcontenuto')
                ->where('m.idcontenuto = ' . (int)$this->id)
                ->order('m.ordinamento');


            $this->_db->setQuery($query);

            $data = $this->_db->loadObjectList();

        } catch (Exception $e) {
            $this->setError($e);
            $data = array();
        }

        return $data;

    }

    public function setStato()
    {

        try {

            // Se è uno scorm e mod track non marca subito come passato, non scrivo niente su db.
            if ($this->tipologia == 4 && $this->mod_track != 1)
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
        } catch (Exception $e) {
            DEBUGG::log($e->getMessage(), 'error in setStato', 0, 1, 0);
        }
    }

    public function testscorm()
    {

        echo "opk";


    }

    public function setUserContent($user_id, $content_id)
    {

        $this->_userid = $user_id;
        $this->_id = $content_id;
    }

    /////////////////////////

    public function attestato_scaricabile_by_user()
    {
        // ritorna true se l'utente può scaricare da solo il suo attestato

        // se config.check_coupon_attestato == 1 --> guardo il valore del flag del coupon
        // se config.check_coupon_attestato == 0 --> attestato sempre scaricabile
        //  se il flag 'attestato' su coupon è == a 1 l'utente può scaricare l'attestato

        try {
            $_config = new gglmsModelConfig();
            $check_coupon_attestato = $_config->getConfigValue('check_coupon_attestato');

            if ((int)$check_coupon_attestato == 1) {

                // controllo attivo
                $this->_id = (!empty($id)) ? $id : (int)$this->getState('contenuto.id');


                $query = $this->_db->getQuery(true)
                    ->select('idunita')
                    ->from('#__gg_unit_map')
                    ->where("idcontenuto= " . $this->_id);

                $this->_db->setQuery($query);
                $id_unita_padre = $this->_db->loadResult();

                $model_unita = new gglmsModelUnita();
                $corso = $model_unita->find_corso((int)$id_unita_padre);
                $gruppo_corso = $model_unita->get_gruppo_accesso_corso($corso->id);

                //entro in coupon con gruppo corso e utente e guardo attestato
                $query = $this->_db->getQuery(true)
                    ->select('c.attestato')
                    ->from('#__gg_coupon AS c')
                    ->where('c.id_utente = ' . $this->_userid)
                    ->where('c.id_gruppi = ' . $gruppo_corso);

                $this->_db->setQuery($query);

                if (null === ($results = $this->_db->loadResult())) {
                    throw new RuntimeException($this->_db->getErrorMsg(), E_USER_ERROR);
                }

            } else {
                // controllo spento, attestato sempre scaricabile
                $results = 1;
            }

            return $results;

        } catch (Exception $e) {
//            DEBUGG::query($query, 'query get contenuto');
            DEBUGG::log($e->getMessage(), 'error in getContenuto', 1, 0, 0);
        }
        return false;
    }

    public function set_content_as_passed()
    {

        $stato_model = new gglmsModelStatoContenuto();

//        var_dump($this->_userid);
//        die();
        switch ($this->tipologia) {

            case 7:
                // QUIZ
                $quiz_obj = $this->get_quiz_info($this->id_quizdeluxe);
                $stato_model->setStatoQuiz($quiz_obj, (int)$this->_userid);

                break;

            default:


                $tmp = new stdClass();
                $tmp->scoid = $this->id;
                $tmp->userid = (int)$this->_userid;

                //ultima visualizzazione
                $tmp->varName = 'cmi.core.last_visit_date';
                $tmp->varValue = date('Y-m-d');
                $stato_model->setStato($tmp);

                //contatore
                $tmp->varName = 'cmi.core.lesson_status';
                $tmp->varValue = ('completed');
                $stato_model->setStato($tmp);


                break;


        }


    }

    public function get_dettagli_quiz_per_utente($quiz_id, $user_id) {

        try {

            if (!isset($quiz_id)
                || !$quiz_id)
                throw new Exception("Nessuna quiz specificato", E_USER_ERROR);

            if (!isset($user_id)
                || !$user_id)
                throw new Exception("Nessun utente specificato", E_USER_ERROR);

            $query = "SELECT /*#__quiz_r_student_quiz.c_quiz_id AS id_quiz,
                            #__quiz_r_student_quiz.c_student_id AS id_utente,*/
                            #__quiz_r_student_quiz.c_date_time AS tentativo_quiz,
                            #__quiz_t_qtypes.c_qtype AS tipo_risposta,
                            #__quiz_t_question.c_question AS domanda_quiz,
                            #__quiz_t_choice.c_choice AS risposta,
                            (
                              CASE
                                    WHEN #__quiz_r_student_question.is_correct = 1 THEN 'SI'
                                    ELSE 'NO'
                              END
                            ) AS risposta_giusta
                            /*,
                            (
                              CASE
                                    WHEN #__quiz_t_choice.c_right = 1 THEN 'SI'
                                    ELSE 'NO'
                              END
                              ) AS risposta_corretta */
                        FROM #__quiz_r_student_quiz #__quiz_r_student_quiz
                            JOIN #__quiz_r_student_question ON #__quiz_r_student_quiz.c_id = #__quiz_r_student_question.c_stu_quiz_id
                            JOIN #__quiz_r_student_choice ON #__quiz_r_student_question.c_id = #__quiz_r_student_choice.c_sq_id
                            JOIN #__quiz_t_question ON #__quiz_r_student_question.c_question_id = #__quiz_t_question.c_id
                            JOIN #__quiz_t_qtypes ON #__quiz_t_question.c_type = #__quiz_t_qtypes.c_id
                            JOIN #__quiz_t_choice  ON #__quiz_r_student_choice.c_choice_id = #__quiz_t_choice.c_id
                        WHERE #__quiz_r_student_quiz.c_quiz_id = " . $this->_db->quote($quiz_id) . "
                        AND #__quiz_r_student_quiz.c_student_id = " . $this->_db->quote($user_id);

            $this->_db->setQuery($query);
            $results = $this->_db->loadAssocList();

            return $results;

        }
        catch (Exception $e) {
            return $e->getMessage();
        }

    }

    // lista quiz per corso comprendente anche idpadre
    public function get_quiz_per_unit($id_unita, $tipologia = 7, $pubblicato = 1) {

        try {

            if (!isset($id_unita)
                || !$id_unita)
                throw new Exception("Nessuna unita specificata", E_USER_ERROR);

            $query = $this->_db->getQuery(true)
                ->select('cont.id_quizdeluxe AS id_quiz, cont.titolo AS titolo_quiz')
                ->from('#__gg_unit un')
                ->join('inner', '#__gg_unit_map map ON un.id = map.idunita')
                ->join('inner', '#__gg_contenuti cont ON map.idcontenuto = cont.id')
                ->where('(un.unitapadre = ' . $this->_db->quote($id_unita) . ' OR un.id = ' . $this->_db->quote($id_unita) . ')')
                ->where('un.pubblicato = ' . $this->_db->quote($pubblicato))
                ->where('cont.tipologia = ' . $this->_db->quote($tipologia));

            $this->_db->setQuery($query);
            $results = $this->_db->loadAssocList();

            return $results;
        }
        catch (Exception $e) {
            return $e->getMessage();
        }

    }

    public function get_quiz_info($quiz_id)
    {

        $query = $this->_db->getQuery(true)
            ->select('*')
            ->from('#__quiz_t_quiz')
            ->where('c_id =' . (int)$quiz_id);


        $this->_db->setQuery($query);
        $quiz_info = $this->_db->loadAssoc();


        return $quiz_info;



    }


    // calcola la permanenza sommando i record da gg_log
    public function calculatePermanenza_tot($id_contenuto,$id_user)
    {

        try {

            $query = $this->_db->getQuery(true)
                ->select('COALESCE(sum(permanenza), 0) as permanenza_tot')
                ->from('#__gg_log')
                ->where('id_contenuto = ' . $id_contenuto)
                ->where('id_utente = ' . $id_user);

            $this->_db->setQuery($query);
            $permanenza_tot = $this->_db->loadResult('permanenza_tot');

            return $permanenza_tot;

        } catch (Exception $e) {

            DEBUGG::log((string)$query, 'error in calculatePermanenza_tot', 0, 1, 0);
        }


    }


    // recupera la permanenza da ggreport
    public function getPermanenza_tot($id_contenuto,$id_user){

        try {

            $query = $this->_db->getQuery(true)
                ->select('COALESCE(sum(permanenza_tot), 0) as permanenza_tot')
                ->from('#__gg_report')
                ->where('id_contenuto = ' . $id_contenuto)
                ->where('id_utente = ' . $id_user);

            $this->_db->setQuery($query);
            $permanenza_tot = $this->_db->loadResult('permanenza_tot');

            return $permanenza_tot;

        } catch (Exception $e) {
            DEBUGG::log((string)$query, 'error in getPermanenza_tot', 0, 1, 0);
        }



    }

    public function get_unit_box($id_box)
    {


        $query = $this->_db->getQuery(true)
            ->select('id_unita')
            ->from('#__gg_box_unit_map')
            ->where('box =' . $id_box);


        $this->_db->setQuery($query);
        $id_unita = $this->_db->loadAssocList();


        return $id_unita;



    }

    public function get_usergroup_box($id_box)
    {


        $query = $this->_db->getQuery(true)
            ->select('ugm.idgruppo')
            ->from('#__gg_box_unit_map as map')
            ->join('inner', '#__gg_usergroup_map as ugm on map.id_unita = ugm.idunita')
            ->where('map.box =' . $id_box);


        $this->_db->setQuery($query);
        $id_unita = $this->_db->loadAssocList();


        return $id_unita;



    }

    public function getOreBox($idBox){

        try {

            $totOrePerGruppo = 0;
            $totOreConverted = 0;

            if (!isset($idBox)
                || !$idBox)
                throw new Exception("Nessuna id box specificato", E_USER_ERROR);

            $unitaArr = $this->get_unit_box($idBox);

            foreach ($unitaArr as $key_unita => $single_unita) {


                $oreCorso = $this->getOreCorso($single_unita['id_unita']);


                if ($oreCorso == 0) {

                    $corsiChildren = $this->getCourseReference($single_unita['id_unita']);

                    if (is_array($corsiChildren) && count($corsiChildren) > 0) {

                        foreach ($corsiChildren as $keyChildren => $singleChild) {
                            $oreCorso += $this->getOreCorso($single_unita['id_unita']);
                        }

                    }
                }

                $totOrePerGruppo += $oreCorso;
                $totOreConverted = ($totOrePerGruppo / 3600);
                $totOreConverted = round($totOreConverted, 0, PHP_ROUND_HALF_UP);

            }

            return $totOreConverted ;

        }catch (Exception $e){
                return $e->getMessage();
        }
    }

    public function getOreCorso($idCorso, $titolo_corso, $extDb = null) {

        try {

            if (is_null($idCorso)
                || $idCorso == "")
                throw new Exception("Nessun id corso definito", E_USER_ERROR);

            $oreCorso = 0;
            $array_unit = array();

            $reportObj = new gglmsModelReport();
            $unitas = $reportObj->getSottoUnitaArrayList($idCorso);

            $db = is_null($extDb)
                ? JFactory::getDbo()
                : $extDb;

            if(is_array($unitas) && count($unitas) >0) {

                $array_unit['id'] = $idCorso;
                $array_unit['titolo'] = $titolo_corso;

                array_push($unitas, $array_unit);

                foreach ($unitas as $unita) {

                    $queryContenuti = "SELECT cgc.id AS id_contenuto, (cgc.durata/3600) AS durata, cgc.durata AS durata_plain
                                    FROM #__gg_contenuti cgc
                                    JOIN #__gg_unit_map cgum ON cgc.id = cgum.idcontenuto
                                    WHERE cgum.idunita = " . $unita['id'] . "
                                    AND cgc.pubblicato = 1
                                    AND cgc.durata > 0";

                    $db->setQuery($queryContenuti);

                    $contenutiCorso = $db->loadAssocList();


                    if (!is_array($contenutiCorso) || !count($contenutiCorso)) return 0;


                    foreach ($contenutiCorso as $contentKey => $singleContent) {

                        $oreCorso += $singleContent['durata_plain'];
                    }

                }

            }else {

                $queryContenuti = "SELECT cgc.id AS id_contenuto, (cgc.durata/3600) AS durata, cgc.durata AS durata_plain
                                    FROM #__gg_contenuti cgc
                                    JOIN #__gg_unit_map cgum ON cgc.id = cgum.idcontenuto
                                    WHERE cgum.idunita = " . $idCorso . "
                                    AND cgc.pubblicato = 1
                                    AND cgc.durata > 0";


                $db->setQuery($queryContenuti);

                $contenutiCorso = $db->loadAssocList();

                if (!is_array($contenutiCorso) || !count($contenutiCorso)) return 0;

                $oreCorso = 0;
                foreach ($contenutiCorso as $contentKey => $singleContent) {

                    $oreCorso += $singleContent['durata_plain'];
                }

            }



            return $oreCorso;

        }
        catch(Exception $ex) {
            DEBUGG::log($ex->getMessage(), __FUNCTION__ . ' -> ERRORE', 0, 1, 0);
            return null;
        }

    }

    public function getCourseReference($idPadre) {

        $db = JFactory::getDbo();

        $query = "SELECT id, unitapadre
                    FROM #__gg_unit
                    WHERE unitapadre = " . $idPadre ;

        $db->setQuery($query);

        return $db->loadAssocList();

    }


    public function get_farmacie_dipendenti($codice, $extDb = null) {

        if (is_null($codice)
            || $codice == "")
            throw new Exception("Nessun codice farmacia definito", E_USER_ERROR);

        $db = is_null($extDb)
            ? JFactory::getDbo()
            : $extDb;

        $query = "SELECT ms.id_gruppo, dip.user_id, ms.hh_store_code, dip.id_gruppo AS gruppo_farmacia
                    FROM #__gg_master_farmacie as ms
                    join #__gg_farmacie_dipendenti as dip on ms.hh_store_code = dip.codice_esterno_cdc_3
                    where ms.hh_store_code = " . $codice ."
                    GROUP BY dip.user_id, ms.hh_store_code
                    ORDER BY ms.id_gruppo ASC ";

        $db->setQuery($query);

        return $db->loadAssocList();

    }

    public function getFarmacie($extDb = null) {

        try {

            $db = is_null($extDb)
                    ? JFactory::getDbo()
                    : $extDb;

            $query = "SELECT ms.id_gruppo, ms.ragione_sociale as nome_farmacia, hh_store_code as codice
                        FROM #__gg_master_farmacie as ms
                        ORDER BY ms.id_gruppo ASC ";

            $db->setQuery($query);

            return $db->loadAssocList();
        }
        catch(\Exception $ex) {
            DEBUGG::log($ex->getMessage(), __FUNCTION__ . ' -> ERRORE', 0, 1, 0);
            return null;
        }

    }

    public function getCouponPerUtenteGruppoCorso($idUtente, $idSocieta, $gruppoCorso, $extDb = null) {

        try {

            $db = is_null($extDb)
                ? JFactory::getDbo()
                : $extDb;

            $query = "SELECT id_gruppi, id_societa
                        FROM #__gg_coupon jgc
                        WHERE id_utente = " . $this->_db->quote($idUtente) . "
                        AND id_societa  = " . $this->_db->quote($idSocieta) . "
                        AND id_gruppi = " . $this->_db->quote($gruppoCorso) . "
                        ORDER BY id_gruppi
                        ";

            $db->setQuery($query);

            $results = $db->loadAssocList();

            // if ($normalizzaArray && count($results) > 0) {
            //     return utilityHelper::normalizza_loadassoc($results, null, 'id_gruppi', true);
            // }

            return $results;

        }
        catch(Exception $ex) {
            DEBUGG::log($ex->getMessage(), __FUNCTION__ . ' -> ERRORE', 0, 1, 0);
            return null;
        }

    }

    public function getCorsiPerUtente($idUtente, $data_dal, $data_al, $extDb = null) {

        try {

            if (is_null($idUtente)
                || $idUtente == "")
                throw new Exception("Nessun id utente definito", E_USER_ERROR);


            if (is_null($data_dal)
                || $data_dal == "")
                throw new Exception("Nessun data inizio definito", E_USER_ERROR);

            if (is_null($data_al)
                || $data_al == "")
                throw new Exception("Nessun data fine definito", E_USER_ERROR);

            $db = is_null($extDb)
                ? JFactory::getDbo()
                : $extDb;

            // AND YEAR(ggr.data) = '" . $annoRiferimento . "'
            $query = "SELECT v.id_corso AS id_unita, u.titolo as titolo_unita, u.data_inizio, jgum.idgruppo AS gruppo_corso
                        FROM #__gg_view_stato_user_corso v
                        JOIN #__gg_unit u ON v.id_corso = u.id
                        JOIN #__gg_report ggr ON v.id_corso = ggr.id_corso and v.id_anagrafica = ggr.id_anagrafica
                        JOIN #__gg_usergroup_map jgum ON ggr.id_unita = jgum.idunita
                        WHERE ggr.id_utente = '" . $idUtente . "'
                        AND v.data_inizio BETWEEN " . $this->_db->quote($data_dal) ." AND " . $this->_db->quote($data_al) ."
                        GROUP BY v.id_corso" ;


            $db->setQuery($query);

            return $db->loadAssocList();

        }
        catch(Exception $ex) {
            DEBUGG::log($ex->getMessage(), __FUNCTION__ . ' -> ERRORE', 0, 1, 0);
            return null;
        }

    }

    public function getUserUsergroup($user_id, $id_gruppo){

        try {

            if (is_null($user_id)
                || $user_id == "")
                throw new Exception("Nessun id utente definito", E_USER_ERROR);

            if (!isset($id_gruppo)
                || !is_array($id_gruppo ))
                throw new Exception("Nessun id unita definito", E_USER_ERROR);


            $ids = implode(",", array_map('reset', $id_gruppo));


//            if (is_null($idArea)
//                || $idArea == "")
//                throw new Exception("Nessun id area definito", E_USER_ERROR);

            $log_arr = array(
                'user_id' => $user_id,
                'ids_group' => $ids

            );

            $db = JFactory::getDbo();


            $query = "SELECT group_id
                        FROM #__user_usergroup_map
                        where user_id = " . $user_id . "
                        AND group_id in (" . $ids . ")";


            $db->setQuery($query);
            $result = $db->loadAssocList();




           return $result;

        }
        catch(Exception $ex) {
            DEBUGG::log($ex->getMessage(), __FUNCTION__ . ' -> ERRORE 2023'. print_r($query,true) . print_r($log_arr,true), 0, 1, 0);
            return null;
        }


    }

    public function getCorsoPerUtente($idUtente, $id_unita, $data_dal, $data_al, $extDb = null) {

        try {

            if (is_null($idUtente)
                || $idUtente == "")
                throw new Exception("Nessun id utente definito", E_USER_ERROR);


            if (is_null($id_unita)
                || $id_unita == "")
                throw new Exception("Nessun id unita definito", E_USER_ERROR);

            if (is_null($data_dal)
                || $data_dal == "")
                throw new Exception("Nessun data inizio definita", E_USER_ERROR);

            if (is_null($data_al)
                || $data_al == "")
                throw new Exception("Nessun data fine definita", E_USER_ERROR);

            $db = is_null($extDb)
            ? JFactory::getDbo()
            : $extDb;

            // AND YEAR(ggr.data) = '" . $annoRiferimento . "'
            $query = "SELECT u.id AS id_unita, u.titolo as titolo_unita, ggc.titolo as titolo_contenuto, ggr.stato, ggc.durata AS durata_ore
                        FROM #__gg_unit u
                        JOIN #__gg_report ggr ON u.id = ggr.id_corso
                        JOIN #__gg_contenuti ggc ON ggr.id_contenuto = ggc.id
                        WHERE ggr.id_utente = '" . $idUtente . "'
                        AND ggr.stato = 1
                        AND ggc.pubblicato = 1
                        AND ggr.data BETWEEN ". $this->_db->quote($data_dal) ." AND ". $this->_db->quote($data_al) ."
                        AND ggr.id_corso = ". $this->_db->quote($id_unita) ;




            $db->setQuery($query);

            $contenutiCorso = $db->loadAssocList();

            if (!is_array($contenutiCorso) || !count($contenutiCorso)) return 0;

            $oreCorso = 0;
            foreach ($contenutiCorso as $contentKey => $singleContent) {

                $oreCorso += $singleContent['durata_ore'];
            }

//            $oreCorso = round($oreCorso, 2, PHP_ROUND_HALF_UP);

            return $oreCorso;
        }
        catch(Exception $ex) {
            DEBUGG::log($ex->getMessage(), __FUNCTION__ . ' -> ERRORE', 0, 1, 0);
            return null;
        }

    }

    public function getOreCorsiPerPeriodo($data_dal, $data_al, $extDb = null) {

        try {


            if (is_null($data_dal)
                || $data_dal == "")
                throw new Exception("Nessun data inizio definito", E_USER_ERROR);

            if (is_null($data_al)
                || $data_al == "")
                throw new Exception("Nessun data fine definito", E_USER_ERROR);


            $db = is_null($extDb)
                ? JFactory::getDbo()
                : $extDb;

            $query = "SELECT u.id, u.data_inizio, u.data_fine
                        from #__gg_unit as u
                        WHERE u.data_inizio BETWEEN  ". $this->_db->quote($data_dal) ." AND ".$this->_db->quote($data_al)."
                        AND u.data_fine BETWEEN  ". $this->_db->quote($data_dal) ." AND ".$this->_db->quote($data_al)."
                        OR ISNULL(u.data_inizio)
                        OR ISNULL(u.data_fine) ";


            $db->setQuery($query);

            $ids_unita = $db->loadAssocList();


            if (!is_array($ids_unita) || !count($ids_unita)) return 0;

            $oreCorsi = 0;
            foreach ($ids_unita as $unitKey => $singleUnit) {

                $query_somma = "SELECT SUM(c.durata) as somma_durata
                                FROM jos_gg_unit_map um
                                JOIN jos_gg_contenuti c on um.idcontenuto = c.id
                                WHERE um.idunita = " . $this->_db->quote($singleUnit['id']) ;

                $db->setQuery($query_somma);

                $durata = $db->loadAssoc();
                $durata['somma_durata'] = is_null($durata['somma_durata']) ? 0 : $durata['somma_durata'];
                $oreCorsi += $durata['somma_durata'];
            }

            $oreCorsi = $oreCorsi/3600 ;
            $oreCorsi = round($oreCorsi, 0, PHP_ROUND_HALF_UP);

            return $oreCorsi;

        }
        catch(Exception $ex) {
            DEBUGG::log($ex->getMessage(), __FUNCTION__ . ' -> ERRORE', 0, 1, 0);
            return null;
        }

    }



}

