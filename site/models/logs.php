<?php

/**
 * @package		Joomla.Tutorials
 * @subpackage	Component
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		License GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

class gglmsModellogs extends JModelList
{

    //Add this handy array with database fields to search in
    protected $searchInFields = array('id_utente');

//Override construct to allow filtering and ordering on our fields
    public function __construct($config = array())
    {
        $config['filter_fields'] = array_merge($this->searchInFields, array('a.id_utente'));

        parent::__construct($config);
    }

    public function getCorsi($id_utente)
    {
        // Create a new query object.
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);


/*        $query->select("anagrafica.id_user,anagrafica.nome, anagrafica.cognome, u.id, u.titolo, if(v.stato=1,'completato','non completato') as stato, date_format(v.data_inizio,'%d/%m/%Y') as 'data_inizio', date_format(v.data_fine,'%d/%m/%Y') as 'data_fine'")
            ->from("#__gg_view_stato_user_corso as v")
            ->join('inner', '#__gg_unit as u on v.id_corso=u.id')
            ->join('inner', '#__gg_report_users as anagrafica on v.id_anagrafica=anagrafica.id')
            ->where('anagrafica.id_user=' . $id_utente);

*/


        $query->select("anagrafica.id_user,anagrafica.nome, anagrafica.cognome, u.id, u.titolo, 
if((select stato from crg_gg_view_stato_user_corso as v where v.id_anagrafica=anagrafica.id and v.id_corso=u.id)=1,'completato',if((select date_format(v.data_inizio,'%d/%m/%Y') from crg_gg_view_stato_user_corso as v where v.id_anagrafica=anagrafica.id and v.id_corso=u.id) is null OR (select date_format(v.data_inizio,'%d/%m/%Y') from crg_gg_view_stato_user_corso as v where v.id_anagrafica=anagrafica.id and v.id_corso=u.id)='00/00/0000','non iniziato','non completato')) as stato, 
(select date_format(v.data_inizio,'%d/%m/%Y') from crg_gg_view_stato_user_corso as v where v.id_anagrafica=anagrafica.id and v.id_corso=u.id)as 'data_inizio',
(select date_format(v.data_fine,'%d/%m/%Y') from crg_gg_view_stato_user_corso as v where v.id_anagrafica=anagrafica.id and v.id_corso=u.id)as 'data_fine'
")
            ->from("#__user_usergroup_map as map")
            ->join('inner','#__ggif_edizione_unita_gruppo as e on e.id_gruppo=map.group_id')
            ->join('inner','#__gg_unit as u on e.id_unita=u.id')
            ->join('inner','#__gg_report_users as anagrafica on map.user_id=anagrafica.id_user');
            $query->where('map.user_id='. $id_utente);
            $query->order('data_inizio desc');

        $db->setQuery($query);
        return $db->loadObjectList();
    }

}

