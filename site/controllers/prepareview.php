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
//require_once JPATH_COMPONENT . '/models/unita.php';
//require_once JPATH_COMPONENT . '/models/users.php';
require_once JPATH_COMPONENT . '/models/syncdatareport.php';
require_once JPATH_COMPONENT . '/models/report.php';
require_once JPATH_COMPONENT . '/models/contenuto.php';

/**
 * Controller for single contact view
 *
 * @since  1.5.19
 */
class gglmsControllerPrepareView extends JControllerLegacy
{
    protected $_db;
    private $_app;
    private $params;
    private $_filterparam;
    private $unitaObj;
    private $reportObj;
    //private $unitas=array();
    //private $contenuti=array();

    public function __construct($config = array())
    {
        parent::__construct($config);
        $this->_app = JFactory::getApplication();
        $this->params = $this->_app->getParams();
        $this->_db = JFactory::getDbo();
        $this->_filterparam = new stdClass();
        $this->_filterparam->groupid=JRequest::getVar('groupid');


       JHtml::_('stylesheet', 'components/com_gglms/libraries/css/debugg.css');


    }

    public function prepareView(){
        $unitaObj=new gglmsModelUnita();
        $reportObj=new gglmsModelReport();

        $groupid=$this->_filterparam->groupid;
        $corsi=$reportObj->getCorsi();


        jimport( 'joomla.access.access' );
        $users=JAccess::getUsersbyGroup($groupid);
        foreach ($users as $userid){



            foreach ($corsi as $corso){

                //if($corso->id=145)
                  $this->analizzaCorso($corso,$userid);


            }
        }
        $this->_app->close();
    }


    public function analizzaCorso($corso,$userid){


        $corso->units=$this->getSottoUnitaRic($corso);//CHIAMATA ALLA FUNZIONE RICORSIVA
        //$corso->contenuti=$this->getContenuti($corso);//QUI CARICHIAMO I CONTENUTI ALLA RADICE DELL'UNITA
        //var_dump($corso->units);

        $this->evaluete($corso,$userid);

    }

    public function getSottoUnitaRic($unita){
        $unita->contents=$this->getContenuti($unita);
        $result=$this->getSottoUnita($unita);

        if(count($result)==0)
                return;
        $unita->units=$result;


        foreach ($unita->units as $unit) {
            $this->getSottoUnitaRic($unit);
        }
        //var_dump($unita);
        return $unita->units;

    }
    public function getSottoUnita($pk)
    {
        $id = $pk->id;

        try
        {
            $query = $this->_db->getQuery(true)
                ->select('*')
                ->from('#__gg_unit as u')
                ->where('u.unitapadre  = ' . $id)
                ->where('u.pubblicato = 1')
                ->order('ordinamento')
            ;
            $this->_db->setQuery($query);
            $data = $this->_db->loadObjectList();
            return $data;
        }
        catch (Exception $e)
        {
            DEBUGG::log($e, 'getSottoUnita');
        }

//

    }



    public function getContenuti($unita)
    {
        try {
            $query = $this->_db->getQuery(true)
                ->select('c.*')
                ->from('#__gg_unit_map as m')
                ->where('m.idunita = ' . $unita->id)
                ->innerJoin('#__gg_contenuti as c on c.id = m.idcontenuto')
                ->where('c.pubblicato = 1')
                ->order('m.ordinamento');


            $this->_db->setQuery($query);
            $contents = $this->_db->loadObjectList();

            return $contents;


        }
        catch (Exception $e)
        {
            DEBUGG::log($e, 'getContenuti');

        }

    }

    private  function  evaluete($unita,$userid){

        //var_dump($unita->units);
        try {
            if($unita!=null) {


                    foreach ($unita->units as $unit) {

                        foreach ($unit->contents as $contenuto) {

                            $contenutoObj = new gglmsModelContenuto();
                            $obj = $contenutoObj->getContenuto($contenuto->id);

                            $contenuto->completato = $obj->getStato($userid)->completato;
                            //echo "unita: " . $unit->id . " contenuto:" . $contenuto->id . " completo:" . $obj->getStato($userid)->completato . "<br>";
                        }
                        if (isset($unit->units))
                            $this->evaluete($unit, $userid);
                    }

            }
        } catch (Exception $e) {

        }
    }

}
