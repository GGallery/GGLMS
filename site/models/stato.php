<?php

/**
 * Created by PhpStorm.
 * User: Tony
 * Date: 04/05/2017
 * Time: 17:03
 */
class gglmsModelStatoContenuto extends JModelLegacy
{

    public $completato;
    public $data;
    public $descrizione;
    public $permanenza;
    public $visualizzazioni;

    public function format_default()
    {
        return $this;
    }

    public function format_quiz_deluxe($data)
    {
        if (!is_object($data)) {
            $this->completato = 0;
            $this->data = '0000-00-00';
            $this->descrizione = 'Non superato';
            $this->permanenza = 0;
            $this->bookmark = 0;
            $this->visualizzazioni = 0;
        } else {

            $this->completato = ($data->c_passed == 1) ? 1 : 0;

            $time = strtotime($data->c_date_time);
            $data = date("Y-m-d", $time);

            $this->data = ($data) ? $data : null;

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

    public function format_scorm($data)
    {

        if ($data && isset($data['cmi.core.lesson_status'])) {
            if ($data['cmi.core.lesson_status']->varValue == 'completed' || $data['cmi.core.lesson_status']->varValue == 'finish' || $data['cmi.core.lesson_status']->varValue == 'passed') {
                $this->completato = 1;
                $this->descrizione = 'Completato';
                $this->data = ($data['cmi.core.last_visit_date']->varValue != null && strtotime($data['cmi.core.last_visit_date']->varValue) != false) ? $data['cmi.core.last_visit_date']->varValue : $data['cmi.core.lesson_status']->TimeStamp;
                $this->permanenza = isset($data['cmi.core.total_time']) ? $data['cmi.core.total_time']->varValue : 0;
                $this->bookmark = isset($data['bookmark']) ? $data['bookmark']->varValue : 0;
                $this->visualizzazioni = isset($data['cmi.core.count_views']) ? $data['cmi.core.count_views']->varValue : 0;
            } else {
                $this->completato = 0;
                $this->descrizione = 'Non completato';
                $this->data = isset($data['cmi.core.last_visit_date']) ? $data['cmi.core.last_visit_date']->varValue : '0000-00-00';//$data['cmi.core.last_visit_date']->varValue;
                $this->permanenza = isset($data['cmi.core.total_time']) ? $data['cmi.core.total_time']->varValue : 0;
                $this->bookmark = isset($data['bookmark']) ? $data['bookmark']->varValue : 0;
                $this->visualizzazioni = isset($data['cmi.core.count_views']) ? $data['cmi.core.count_views']->varValue : 0;
            }
        } else {
            $this->completato = 0;
            $this->descrizione = 'Non completato';
            $this->data = '0000-00-00';
            $this->permanenza = '0';
            $this->bookmark = 0;
            $this->visualizzazioni = 0;
        }

        return $this;
    }

    public function format_attestato($data)
    {

        if ($data) {
            if ($data['cmi.core.lesson_status']->varValue == 'completed' || $data['cmi.core.lesson_status']->varValue == 'finish' || $data['cmi.core.lesson_status']->varValue == 'passed') {
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
        } else {
            $this->completato = 0;
            $this->descrizione = 'Non scaricato';
            $this->data = '0000-00-00';
            $this->permanenza = '0';
            $this->bookmark = 0;
            $this->visualizzazioni = 0;
        }
        return $this;
    }

    public function format_allegati($data)
    {

        if ($data) {
            if ($data['cmi.core.lesson_status']->varValue == 'completed' || $data['cmi.core.lesson_status']->varValue == 'finish' || $data['cmi.core.lesson_status']->varValue == 'passed') {
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
        } else {
            $this->completato = 0;
            $this->descrizione = 'Non visionato';
            $this->data = '0000-00-00';
            $this->permanenza = '0';
            $this->bookmark = 0;
            $this->visualizzazioni = 0;
        }


        return $this;
    }

    public function format_sso()
    {

        $this->completato = 1;
        $this->descrizione = 'Accessibile';
        $this->data = '0000-00-00';
        $this->permanenza = '0';
        $this->bookmark = 0;
        $this->visualizzazioni = 0;

        return $this;

    }

    public function setStato($data)
    {
        $db = $this->getDbo();

        $query = "insert into #__gg_scormvars (scoid, userid, varName, varValue)  VALUES ($data->scoid,$data->userid,'$data->varName','$data->varValue')";
        $query .= "ON DUPLICATE KEY UPDATE varValue = '$data->varValue' ";

        $db->setQuery($query);
        $db->execute();
    }

    public function setStatoQuiz($data, $student_id)
    {


        $values[] = sprintf("(%d, %d, %d, %d, '%s', %d, '%s', %d, %d, %d, %d, '%s', %d, '%s' ,%d, %d ,'%s','%s','%s','%s')",
            $data['c_id'], //quiz_id
            $student_id, //student_id
            $data['c_full_score'], //total_score
            3,// totaltime
            date('Y-m-d H:i:s', time()), // c_date_time
            1, // passed,
            uniqid(), // unique_id
            0, // allow_review
            0, //c_order_id
            0, // c_rel_id
            0, // c_lid
            uniqid(), // uniquepass__id
            1, // finished
            '', // user_email
            $data['c_full_score'], // passing_score
            $data['c_full_score'],  //max_store
            ' ', // user_name
            ' ', // surname
            '{}',// params
            date('Y-m-d H:i:s', time()) // timestamp

        );


        // li inserisco nel DB
        $query = 'INSERT INTO #__quiz_r_student_quiz(c_quiz_id, c_student_id, c_total_score, c_total_time, c_date_time, c_passed ,unique_id, allow_review, c_order_id, c_rel_id, c_lid, unique_pass_id,c_finished, user_email, c_passing_score, c_max_score, user_name, user_surname, params, timestamp ) VALUES ' . join(',', $values);
        $this->_db->setQuery($query);
        if (false === $this->_db->execute()) {
            throw new RuntimeException($this->_db->getErrorMsg(), E_USER_ERROR);
        }


    }

}

