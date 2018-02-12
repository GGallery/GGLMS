<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Controller for single contact view
 *
 * @since  1.5.19
 */
class gglmsControllerContenuto extends JControllerLegacy
{

    public function updateTrack() {

        $japp = JFactory::getApplication();


        $secondi = JRequest::getVar('secondi');
        $stato = JRequest::getVar('stato');
        $id_elemento = JRequest::getVar('id_elemento');

        $user =  JFactory::getUser();
        $user_id = $user->get('id');


        $modelstato = new gglmsModelStatoContenuto();
        $tmp = new stdClass();

        $tmp->scoid = $id_elemento;
        $tmp->userid = $user_id;

        try{
            if($stato == 1){
                $tmp->varName = 'cmi.core.lesson_status';
                $tmp->varValue = 'completed';
                $modelstato->setStato($tmp);
            }

            $tmp->varName = 'cmi.core.last_visit_date';
            $tmp->varValue = date('Y-m-d');
            $modelstato->setStato($tmp);


            $tmp->varName = 'cmi.core.total_time';
            $tmp->varValue = $secondi;
            $modelstato->setStato($tmp);

            echo 1;
        } catch (Exception $e) {
            echo 0;
        }
        $japp->close();
    }

    public function updateBookmark() {

        $japp = JFactory::getApplication();


        $time = JRequest::getVar('time');
        $id_elemento = JRequest::getVar('id_elemento');

        $user =  JFactory::getUser();
        $user_id = $user->get('id');


        $modelstato = new gglmsModelStatoContenuto();
        $tmp = new stdClass();

        $tmp->scoid = $id_elemento;
        $tmp->userid = $user_id;

        try{
            $tmp->varName = 'bookmark';
            $tmp->varValue = $time;
            $modelstato->setStato($tmp);
        } catch (Exception $e) {
            DEBUGG::loo($e);
        }
        $japp->close();
    }




}
