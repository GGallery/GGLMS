<?php
/**
 * Created by PhpStorm.
 * User: Tony
 * Date: 04/05/2017
 * Time: 17:03
 */
class gglmsModelStatoContenuto extends JModelLegacy {

    public $completato ;
    public $data;
    public $descrizione;
    public $permanenza;
    public $visualizzazioni;

    public function format_default(){
        return $this;
    }

    public function format_quiz_deluxe($data){
        if(!is_object($data)){
            $this->completato= 0;
            $this->data = '0000-00-00';
            $this->descrizione = 'Non superato';
            $this->permanenza = 0;
            $this->bookmark = 0;
            $this->visualizzazioni = 0;
        }
        else {

            $this->completato = ($data->c_passed == 1) ? 1 : 0;

            $time = strtotime($data->c_date_time);
            $data = date("Y-m-d", $time);

            $this->data = ($data) ? $data  : null;

            $this->visualizzazioni = 0;

            if (!$this->completato) {
                if ($this->data)
                    $this->descrizione = 'Non superato';
                else
                    $this->descrizione = 'Mai provato';
            } else
                $this->descrizione = 'Superato il ' . date("d/m/y", strtotime($this->data));
        }

        return $this;
    }

    public function format_scorm($data){

        if($data && isset($data['cmi.core.lesson_status'])) {
            if ($data['cmi.core.lesson_status']->varValue == 'completed' || $data['cmi.core.lesson_status']->varValue == 'finish' || $data['cmi.core.lesson_status']->varValue == 'passed') {
                $this->completato = 1;
                $this->descrizione = 'Completato';
                $this->data = ($data['cmi.core.last_visit_date']->varValue!=null && strtotime($data['cmi.core.last_visit_date']->varValue)!=false) ? $data['cmi.core.last_visit_date']->varValue : $data['cmi.core.lesson_status']->TimeStamp;
                $this->permanenza = isset($data['cmi.core.total_time']) ? $data['cmi.core.total_time']->varValue : 0;
                $this->bookmark = isset($data['bookmark']) ? $data['bookmark']->varValue : 0;
                $this->visualizzazioni = isset($data['cmi.core.count_views']) ? $data['cmi.core.count_views']->varValue : 0;
            } else {
                $this->completato = 0;
                $this->descrizione = 'Non completato';
                $this->data =  isset($data['cmi.core.last_visit_date']) ? $data['cmi.core.last_visit_date']->varValue : '0000-00-00';//$data['cmi.core.last_visit_date']->varValue;
                $this->permanenza = isset($data['cmi.core.total_time']) ? $data['cmi.core.total_time']->varValue : 0;
                $this->bookmark = isset($data['bookmark']) ? $data['bookmark']->varValue : 0;
                $this->visualizzazioni = isset($data['cmi.core.count_views']) ? $data['cmi.core.count_views']->varValue : 0;
            }
        }else {
            $this->completato = 0;
            $this->descrizione = 'Non completato';
            $this->data = '0000-00-00';
            $this->permanenza = '0';
            $this->bookmark = 0;
            $this->visualizzazioni = 0;
        }

        return $this;
    }

    public function format_attestato($data){

        if($data) {
            if ($data['cmi.core.lesson_status']->varValue == 'completed' || $data['cmi.core.lesson_status']->varValue == 'finish'  || $data['cmi.core.lesson_status']->varValue == 'passed') {
                $this->completato = 1;
                $this->descrizione = 'Scaricato';
                $this->data = isset($data['cmi.core.last_visit_date']) ? $data['cmi.core.last_visit_date']->varValue : '';
                $this->permanenza = isset($data['cmi.core.total_time']) ? $data['cmi.core.total_time']->varValue : 0;
                $this->bookmark = 0;
                $this->visualizzazioni = isset($data['cmi.core.count_views']) ? $data['cmi.core.count_views']->varValue : 0;
            } else {
                $this->completato = 0;
                $this->descrizione = 'Non scaricato';
                $this->data = isset($data['cmi.core.total_time']) ? $data['cmi.core.total_time']->varValue : 0;
                $this->permanenza = isset($data['cmi.core.total_time']) ? $data['cmi.core.total_time']->varValue : 0;
                $this->bookmark = 0;
                $this->visualizzazioni = isset($data['cmi.core.count_views']) ? $data['cmi.core.count_views']->varValue : 0;
            }
        }else
        {
            $this->completato = 0;
            $this->descrizione = 'Non scaricato';
            $this->data = '0000-00-00';
            $this->permanenza = '0';
            $this->bookmark = 0;
            $this->visualizzazioni = 0;
        }
        return $this;
    }

    public function format_allegati($data){

        if($data) {
            if ($data['cmi.core.lesson_status']->varValue == 'completed' || $data['cmi.core.lesson_status']->varValue == 'finish'  || $data['cmi.core.lesson_status']->varValue == 'passed') {
                $this->completato = 1;
                $this->descrizione = 'Documento visionato';
                $this->data = isset($data['cmi.core.last_visit_date']) ? $data['cmi.core.last_visit_date']->varValue : '0000-00-00';
                $this->permanenza = '0';
                $this->bookmark = 0;
                $this->visualizzazioni = isset($data['cmi.core.count_views']) ? $data['cmi.core.count_views']->varValue : 0;
            } else {
                $this->completato = 0;
                $this->descrizione = 'Non visionato';
                $this->data = isset($data['cmi.core.total_time']) ? $data['cmi.core.total_time']->varValue : 0;
                $this->permanenza = '0';
                $this->bookmark = 0;
                $this->visualizzazioni = isset($data['cmi.core.count_views']) ? $data['cmi.core.count_views']->varValue : 0;
            }
        }else
        {
            $this->completato = 0;
            $this->descrizione = 'Non visionato';
            $this->data = '0000-00-00';
            $this->permanenza = '0';
            $this->bookmark = 0;
            $this->visualizzazioni = 0;
        }
 

        return $this;
    }
    
    public function format_sso(){

        $this->completato = 1;
        $this->descrizione = 'Accessibile';
        $this->data = '0000-00-00';
        $this->permanenza = '0';
        $this->bookmark = 0;
        $this->visualizzazioni = 0;
        
        return $this;
        
    }

    public function setStato($data){
        $db = $this->getDbo();

        $query = "insert into #__gg_scormvars (scoid, userid, varName, varValue)  VALUES ($data->scoid,$data->userid,'$data->varName','$data->varValue')";
        $query .= "ON DUPLICATE KEY UPDATE varValue = '$data->varValue' ";

        $db->setQuery($query);
        $db->execute();
    }

}

