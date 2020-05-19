<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

//require_once JPATH_COMPONENT . '/models/contenuto.php';
require_once JPATH_COMPONENT . '/models/unita.php';

/**
 * Controller for single contact view
 *
 * @since  1.5.19
 */
class gglmsControllerMobile extends JControllerLegacy
{

    public $_params;
    public $_db;
    public $generaCoupon;
    private $_user;
    private $_japp;


    public function __construct($config = array())
    {
        parent::__construct($config);
        $this->_japp = JFactory::getApplication();
        $this->_params = $this->_japp->getParams();

        $this->_user = JRequest::getVar('userid');
        $this->_db = &JFactory::getDbo();

        $this->unitsFields = 'id, titolo, descrizione, unitapadre, is_corso';
        $this->contentsFields = 'id, titolo, durata, tipologia, mod_track, prerequisiti';


    }


    public function data()
    {
        $units = $this->getCourses();
        $contents = $this->getContents($units);

        $contentsIds = [];
        foreach ($contents as $content) {
            array_push($contentsIds, $content->id);
        }
        $contentsIds = implode(",", $contentsIds);


        $scormvars = $this->getScormVars($contentsIds);
        $quizresults = $this->getQuizResults($contentsIds);

        $res['units'] = $units;
        $res['contents'] = $contents;
        $res['scormvars'] = $scormvars;
        $res['quizresults'] = $quizresults;

        echo json_encode($res);

        $this->_japp->close();
    }

    protected function getCourses()
    {
        $courses = array();
        $this->_db = JFactory::getDbo();
        $query = $this->_db->getQuery(true);

        $query->select($this->unitsFields);
        $query->from('#__gg_unit AS a');
        $query->where("is_corso=1");
//        $query->where("id = 191");
        $query->order("ordinamento");
        $query->setLimit(3);


        $this->_db->setQuery($query);

        // Check for a database error.
        if ($this->_db->getErrorNum()) {
            JError::raiseWarning(500, $this->_db->getErrorMsg());
        }

        $coursesTree = $this->_db->loadObjectList();

        foreach ($coursesTree as $item) {
            array_push($courses, $item);
            foreach ($this->getUnits($item->id, $item->id) as $item2) {
                $item2->unitapadre = $item->id;
                array_push($courses, $item2);
            }
        }
        unset($coursesTree);
        return $courses;

    }

    protected function getUnits($item = 0, $courseId = 0)
    {
        $units = array();
        $query = $this->_db->getQuery(true);

        $query->select($this->unitsFields);
        $query->from('#__gg_unit AS a');
        $query->where("unitapadre=" . $item);
        $query->order("ordinamento");

        $this->_db->setQuery($query);

        // Check for a database error.
        if ($this->_db->getErrorNum()) {
            JError::raiseWarning(500, $this->_db->getErrorMsg());
        }

        $unitTree = $this->_db->loadObjectList();

        foreach ($unitTree as $item) {
            array_push($units, $item);
            foreach ($this->getUnits($item->id, $courseId) as $item2) {
                $item2->unitapadre = $courseId;
                array_push($units, $item2);
            }
        }
        unset($unitTree);
        return $units;
    }

    protected function getContents($units)
    {

        $query = $this->_db->getQuery(true);

        $unitsList = [];

        foreach ($units as $unit) {
            array_push($unitsList, $unit->id);
        }

        $unitsList = implode(",", $unitsList);

        $query->select($this->contentsFields . ', u.idunita as unitapadre');
        $query->from('#__gg_unit_map AS u');
        $query->join('inner', '#__gg_contenuti as c on u.idcontenuto = c.id');
        $query->where("u.idunita in ($unitsList)");

        $this->_db->setQuery($query);

        // Check for a database error.
        if ($this->_db->getErrorNum()) {
            JError::raiseWarning(500, $this->_db->getErrorMsg());
        }

        $contents = $this->_db->loadObjectList();

        return $contents;

    }

    protected function getScormVars($contentsIds)
    {

        $query = $this->_db->getQuery(true);

        $contentsList = [];

        $query->select('s.*');
        $query->from('#__gg_scormvars AS s');
        $query->where("s.scoid in ($contentsIds)");
        $query->where("s.userId = " . $this->_user);
        $query->where("varName = 'cmi.core.lesson_status'");
        $query->where("varValue = 'completed'");

        $this->_db->setQuery($query);

        // Check for a database error.
        if ($this->_db->getErrorNum()) {
            JError::raiseWarning(500, $this->_db->getErrorMsg());
        }

        $scormVars = $this->_db->loadObjectList();

        return $scormVars;

    }

    protected function getQuizResults($contentsIds)
    {

        $query = $this->_db->getQuery(true);

        $query->select('DISTINCT c.id, qr.c_passed');
        $query->from('#__gg_contenuti AS c');
        $query->join('inner', '#__quiz_r_student_quiz AS qr ON qr.c_quiz_id = c.id_quizdeluxe AND qr.c_passed = 1');
        $query->where("tipologia = 7");
        $query->where("id in ($contentsIds)");
        $query->where("c_student_id = " . $this->_user);

        $this->_db->setQuery($query);

        // Check for a database error.
        if ($this->_db->getErrorNum()) {
            JError::raiseWarning(500, $this->_db->getErrorMsg());
        }

        $return = $this->_db->loadObjectList();

        return $return;

    }

    public function login()
    {

        jimport('joomla.user.authentication');

        $jinput = JFactory::getApplication()->input->json;
        $username = $jinput->get('username');
        $password = $jinput->get('password');

        $auth = &JAuthentication::getInstance();
        $credentials = array('username' => $username, 'password' => $password);
        $options = array();

        $response = null;
        $response = $auth->authenticate($credentials, $options);

        if ($response->status === JAuthentication::STATUS_SUCCESS) {

            $this->_db = JFactory::getDbo();
            $query = $this->_db->getQuery(true)
                ->select('id')
                ->from('#__users')
                ->where("username =" . $this->_db->quote($username));

            $this->_db->setQuery($query);
            $user_id = $this->_db->loadResult();

            $response->id = $user_id;

            echo json_encode($response);
        } else {
            echo json_encode($response);
        }

        $this->_japp->close();
    }
}
