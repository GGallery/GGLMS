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
require_once JPATH_COMPONENT . '/models/syncviewcarigelearningbatch.php';


/**
 * Controller for single contact view
 *
 * @since  1.5.19
 */
class gglmsControllerSyncViewCarigeLearningBatch extends JControllerLegacy
{
    protected $_db;
    private $_app;
    private $params;
    private $_filterparam;
    private $syncviewcarigelearningbatchModel;

    //private $unitas=array();
    //private $contenuti=array();

    public function __construct($config = array())
    {
        parent::__construct($config);
        $this->_app = JFactory::getApplication();
        $this->params = $this->_app->getParams();
        $this->_db = JFactory::getDbo();
        $this->_filterparam = new stdClass();
        $this->_filterparam->typeofcall=JRequest::getVar('typeofcall');
        $this->_filterparam->limit=JRequest::getVar('limit');
        $this->_filterparam->offset=JRequest::getVar('offset');
        $this->_filterparam->maxts=JRequest::getVar('maxts');
        $this->syncviewcarigelearningbatchModel=new gglmsModelSyncViewCarigeLearningBatch();

       JHtml::_('stylesheet', 'components/com_gglms/libraries/css/debugg.css');


    }

    public function sync(){

        try {


                $syncviewcarigelearningbatchModel = new gglmsModelSyncViewCarigeLearningBatch();
                $result = $syncviewcarigelearningbatchModel->syncViewCarigeLearningBatch(null, null, null, 'task');
                if ($result) {
                    DEBUGG::log('VIEWCARIGELEARNINGBATCH.SYNC CONCLUSA SENZA ERRORI','VIEWCARIGELEARNINGBATCH.SYNC',0,1);
                    echo json_encode('true');
                } else {
                    DEBUGG::log('!!! VIEWCARIGELEARNINGBATCH.SYNC CONCLUSA CON ERRORI !!!','VIEWCARIGELEARNINGBATCH.SYNC',0,1);
                    echo json_encode('false');
                }

            $this->_app->close();
        }catch (exceptions $ex){

            DEBUGG::log($ex->getMessage(),'ERRORE DA REPORT.SYNC',1,1);

        }
    }

    public  function GetMaxTimeStampforSession(){

        echo $this->syncviewcarigelearningbatchModel->MaxTimeStampforSession();
        $this->_app->close();
    }


    public function syncViewCarigeLearningBatch(){

        DEBUGG::log('INIZIO PROCEDURA CARIGE BATCH', ' ',0,1,0);
        $typeofcall=$this->_filterparam->typeofcall;
        $offset=$this->_filterparam->offset;
        $limit=$this->_filterparam->limit;
        $maxts=$this->_filterparam->maxts;
        $maxts=substr($maxts,0,4)."-".substr($maxts,4,2)."-".substr($maxts,6,2)." ".substr($maxts,8,2).":".substr($maxts,10,4).":".substr($maxts,12,2);
        $result= $this->syncviewcarigelearningbatchModel->syncViewCarigeLearningBatch($offset,$limit,$maxts,$typeofcall);
        echo $result;
        DEBUGG::log('FINE PROCEDURA CARIGE BATCH', ' ',0,1,0);

        $this->_app->close();
    }

}
