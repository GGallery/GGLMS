<?php
/**
 * Created by PhpStorm.
 * User: Antonio
 * Date: 18/12/2017
 * Time: 12:59
 */


/**
 * @package		Joomla.Tutorials
 * @subpackage	Component
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		License GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');
require_once JPATH_COMPONENT . '/models/report.php';

class gglmsControllerAllineaReport extends JControllerAdmin
{

    //ALLINEA QUIZ DE LUXE
    public function allinea(){

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('id as id, id_quizdeluxe as id_quizdeluxe');
        $query->from('crg_gg_contenuti');
        $query->where('tipologia=7');
        $db->setQuery($query);

        $result=$db->loadAssocList();

        
        foreach ($result as $row){
        echo 'procedo con '.$row['id'];
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query='update crg_gg_report set stato = 1 where id_utente in (select anagrafica.id_user from crg_quiz_r_student_quiz as q
                    inner join crg_gg_report_users as anagrafica on q.c_student_id=anagrafica.id_user
                    where  q.c_quiz_id='.$row['id_quizdeluxe'].' and q.c_passed=1)
                    and id_contenuto='.$row['id'].'
                    and stato=0';
            $db->setQuery($query);
            $result=$db->execute();
            $num=$db->getAffectedRows();
            if($result==1 && $num>0){
                DEBUGG::log((string)$num.' in contenuto QUIZDELUXE: '.$row['id'],'ALLINEAMENTO REPORT: modificate righe ',0,1);
            }

        }

        //ALLINEA SCORM

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('id as id');
        $query->from('crg_gg_contenuti');
        $query->where('tipologia=4');
        $db->setQuery($query);

        $result=$db->loadAssocList();
        foreach ($result as $row){
            echo 'procedo con '.$row['id'];
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query='update crg_gg_report set stato=1 where id_utente in (select s.userid from crg_gg_unit as u inner join crg_gg_contenuti as c on c.id=u.id_contenuto_completamento 
                    inner join crg_gg_scormvars as s on s.scoid=c.id
                    where u.is_corso=1 and c.tipologia=4 and s.varName=\'cmi.core.lesson_status\' and s.varValue in (\'completed\',\'passed\') and c.id='.$row['id'].')
                    and id_contenuto='.$row['id'].' and stato=0';
            $db->setQuery($query);
            $result=$db->execute();
            $num=$db->getAffectedRows();
            if($result==1 && $num>0){
                DEBUGG::log((string)$num.' in contenuto SCORM: '.$row['id'],'ALLINEAMENTO REPORT: modificate righe ',0,1);
            }

        }

          $this->allinea_vista_con_report();

      }

      public function allinea_vista_con_report(){

          $db = JFactory::getDbo();
          $query = $db->getQuery(true);
          $query->select('report.id_anagrafica, u.id,report.data');
          $query->from('crg_gg_unit as u inner join crg_gg_contenuti as c on c.id=u.id_contenuto_completamento');
          $query->join('inner','crg_gg_report as report on c.id=report.id_contenuto');
          $query->join('inner','crg_gg_view_stato_user_corso as v on v.id_corso=u.id and v.id_anagrafica=report.id_anagrafica');
          $query->where('report.stato=1 and v.stato=0');
          $db->setQuery($query);

          $result=$db->loadAssocList();
          foreach ($result as $row) {

              $query = $db->getQuery(true);
              $query='update crg_gg_view_stato_user_corso set stato=1, data_fine=\''.$row['data'].'\' where 
                      id_anagrafica='.$row['id_anagrafica'].' and id_corso='.$row['id']
                   ;
              $db->setQuery($query);
              //echo $query;
              $result=$db->execute();

          }

            echo count($result);
          $this->insert_vista_con_report();
      }

    public function insert_vista_con_report(){

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('id, id_contenuto_completamento');
        $query->from('crg_gg_unit ');
        $query->where('is_corso=1');
        $db->setQuery($query);

        $result=$db->loadAssocList();
        foreach ($result as $row) {

            if ($row['id_contenuto_completamento']!=null && $row['id_contenuto_completamento']!=30) {
                //insert into crg_gg_view_stato_user_corso
                $query = 'insert ignore into crg_gg_view_stato_user_corso select id_anagrafica, id_corso, 1 as \'stato\', data as \'data_inizio\', data as \'data_fine\',now() as `timestamp` 
                    from crg_gg_report where id_contenuto=' . $row['id_contenuto_completamento'] . ' and stato=1 and id_anagrafica not in 
                    (select id_anagrafica from crg_gg_view_stato_user_corso where id_corso=' . $row['id'] . ' and stato=1)';
                $db->setQuery($query);
                //$result_=$db->loadAssocList();
                echo $query . ';<br>';
                $result_=$db->execute();
                echo "allineamento tra vista e report: ".$db->getAffectedRows().'<br>';


                /*if($result==1){
                    DEBUGG::log($query,'inserimento riuscito: ',0,1);
                }else{
                    DEBUGG::log($query,'ERRORE: ',0,1);
                }*/

            }


        }


        $query_="update crg_gg_view_stato_user_corso set data_fine=date(crg_gg_view_stato_user_corso.`timestamp`) where stato=1 and data_fine='0000-00-00'";
        $db->setQuery($query_);
        $result__=$db->execute();
        echo "data fine 0000: ".$db->getAffectedRows();




    }

    public function ver_vista_con_report(){

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('id, id_contenuto_completamento');
        $query->from('crg_gg_unit ');
        $query->where('is_corso=1');
        $db->setQuery($query);

        $result=$db->loadAssocList();
        foreach ($result as $row) {

            if ($row['id_contenuto_completamento']!=null && $row['id_contenuto_completamento']!=30) {
                //insert into crg_gg_view_stato_user_corso
                $query = 'select id_anagrafica, id_corso, 1 as \'stato\', data as \'data_inizio\', data as \'data_fine\',now() as `timestamp` 
                    from crg_gg_report where id_contenuto=' . $row['id_contenuto_completamento'] . ' and stato=1 and id_anagrafica not in 
                    (select id_anagrafica from crg_gg_view_stato_user_corso where id_corso=' . $row['id'] . ' and stato=1)';
                $db->setQuery($query);
                $result_=$db->loadAssocList();
                echo $query . ';<br>';
                //$result_=$db->execute($query);


                echo count($result_).'<br>';
            }


        }


    }

}

