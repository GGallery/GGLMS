<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;


require_once JPATH_COMPONENT . '/models/helpdesk.php';
require_once JPATH_COMPONENT . '/models/users.php';

/**
 * Controller for single contact view
 *
 * @since  1.5.19
 */
class gglmsControllerSummaryReport extends JControllerLegacy
{

    private $_user;
    private $_japp;
    public $_params;
    public $_db;

    public $__piattaforme;


    public function __construct($config = array())
    {
        parent::__construct($config);
        $this->_japp = JFactory::getApplication();
        $this->_params = $this->_japp->getParams();

        $this->_user = JFactory::getUser();
        $this->_db = &JFactory::getDbo();

        $this->_piattaforme = utilityHelper::getPiattaformeByUser(false);

    }

    public function getData()
    {
        $model_user = new gglmsModelUsers();

        $piattaforme = $model_user->get_user_piattaforme($this->_user->id);

        $p_list = array();

        foreach ($piattaforme as $p) {

            array_push($p_list, $p->value);
        }

        $query = $this->_db->getQuery(true)
            ->select('*')
            ->from('#__view_report')
            ->where("id_piattaforma  in (" . implode(', ', $p_list) . ")")
            ->order('id_piattaforma');

        $this->_db->setQuery($query);
        $result = $this->_db->loadAssocList();


        echo json_encode($result);
        $this->_japp->close();
//
    }

    public function is_tutor_aziendale()
    {

        $model = new gglmsModelUsers();
        $is_tutor_az = $model->is_tutor_aziendale($this->_user->id);

        echo(json_encode($is_tutor_az));
        $this->_japp->close();

        // is_tutor_aziendale

    }

    public function getDetails()
    {
        $filter_params = JRequest::get($_POST);
        $id_gruppo_societa = $filter_params['id_gruppo_azienda'];
        $id_corso = $filter_params['id_corso'];
        $id_user = $filter_params['id_user'];


        $query = $this->_db->getQuery(true)
            ->select('r.id_contenuto, r.data as last_visit ,r.permanenza_tot as permanenza ,r.visualizzazioni as visualizzazioni,contenuti.titolo as titolo_contenuto')
            ->from('#__gg_view_stato_user_corso as report')
            ->join('inner', '#__gg_unit AS c ON report.id_corso = c.id')
            ->join('inner', '#__gg_report_users AS u ON report.id_anagrafica = u.id')
            ->join('inner', '#__user_usergroup_map AS um ON um.user_id = u.id_user ')
            ->join('inner', '#__gg_report as r on r.id_corso = c.id and r.id_utente = u.id_user')
            ->join('inner', '#__gg_contenuti as contenuti on contenuti.id = r.id_contenuto')
            ->join('inner', '#__gg_unit_map as umap on umap.idcontenuto = contenuti.id')
            ->where('um.group_id =' . $id_gruppo_societa)
            ->where('c.id =' . $id_corso)
            ->where('u.id_user =' . $id_user);


        $this->_db->setQuery($query);
        $data = $this->_db->loadAssocList();

//        echo json_encode((string)$query);
        echo json_encode($data);
        $this->_japp->close();
    }

}

