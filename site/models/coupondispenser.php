<?php

/**
 * WebTVContenuto Model
 *
 * @package    Joomla.Components
 * @subpackage WebTV
 */
defined('_JEXEC') or die('Restricted access');
require_once(JPATH_COMPONENT . '/libraries/smarty/EasySmarty.class.php');

jimport('joomla.application.component.model');

/**
 * WebTVContenuto Model
 *
 * @package    Joomla.Components
 * @subpackage WebTV
 */
class gglmsModelcoupondispenser extends JModelLegacy
{

    public $_params;
    protected $_db;
    private $_japp;
    private $_coupon;
    private $_userid;
    private $_user;

    public function __construct($config = array())
    {
        parent::__construct($config);
        $this->_dbg = JRequest::getBool('dbg', 0);
        $this->_japp = JFactory::getApplication();
        $this->_db = JFactory::getDbo();
        $this->_user = JFactory::getUser();
        $this->_userid = $this->_user->get('id');
        $this->_params = $this->_japp->getParams();

//        define('SMARTY_DIR', JPATH_COMPONENT . '/libraries/smarty/smarty/');
        define('SMARTY_COMPILE_DIR', JPATH_COMPONENT . '/models/cache/compile/');
        define('SMARTY_CACHE_DIR', JPATH_COMPONENT . '/models/cache/');
        define('SMARTY_TEMPLATE_DIR', JPATH_COMPONENT . '/models/templates/');
        define('SMARTY_CONFIG_DIR', JPATH_COMPONENT . '/models/');
        define('SMARTY_PLUGINS_DIRS', JPATH_COMPONENT . '/libraries/smarty/extras/');

    }

    public function getDispenser($id = null)
    {
        $this->_id = (!empty($id)) ? $id : (int)$this->getState('coupondispenser.id');
        try {
            $query = $this->_db->getQuery(true);
            $query->select('*')
                ->from('#__gg_coupon_dispenser as c')
                ->where('c.id = ' . (int)$this->_id);

            $this->_db->setQuery($query);

            $data = $this->_db->loadObject();

            if (empty($data)) {
                throw new Exception(JText::_('COM_GGLMS_COUPON_DISPENSER_WRONG_ID'));
            }

        } catch (Exception $e) {
            DEBUGG::query($query, 'query getDispenser');
            DEBUGG::log($e->getMessage(), 'error in getDispenser', 0, 1, 0);
        }
        return $data;
    }

    public function getCoupon($id_iscrizione)
    {

        try {
            $query = $this->_db->getQuery(true);
            $query->select('c.coupon')
                ->from('#__gg_coupon as c')
                ->join('left', '#__gg_coupon_dispenser_log as l ON l.coupon = c.coupon')
                ->where('c.id_iscrizione = "' . $id_iscrizione . '"')
                ->where('l.coupon IS NULL')
                ->where('c.id_utente IS NULL')
                ->where('c.abilitato = 1')
                ->limit(1);

            $this->_db->setQuery($query);

            $data = $this->_db->loadResult();

            if (empty($data)) {
                throw new Exception(JText::_('COM_GGLMS_COUPON_DISPENSER_NO_COUPON'));
            }

            return $data;


        } catch (Exception $e) {
            DEBUGG::log($e->getMessage(), 'error in getCoupon', 0, 1, 0);
        }

    }

    public function checkAlreadyTaken($email, $id_dispenser)
    {

        try {
            $query = $this->_db->getQuery(true);
            $query->select('*')
                ->from('#__gg_coupon_dispenser_log as c')
                ->where('c.email = ' . $this->_db->quote($email))
                ->where('c.id_dispenser = ' . $id_dispenser);

            $this->_db->setQuery($query);
            $data = $this->_db->loadResult();

            return $data;

        } catch (Exception $e) {
            DEBUGG::query($query, 'query checkAlreadyTaken');
            DEBUGG::log($e->getMessage(), 'error in checkAlreadyTaken', 0, 1, 0);
        }

    }

    public function setDispenserLog($email, $id_dispenser, $coupon)
    {

        try {
            $log = new stdClass();
            $log->email = $email;
            $log->id_dispenser = $id_dispenser;
            $log->coupon = $coupon;

            $result = JFactory::getDbo()->insertObject('#__gg_coupon_dispenser_log', $log);

        } catch (Exception $e) {
            DEBUGG::log($e->getMessage(), 'error in setDispenserLog', 0, 1, 0);

        }

    }

    public function sendMail($email, $coupon)
    {

        $mailer = JFactory::getMailer();
        $config = JFactory::getConfig();

        $sender = array(
            $config->get( 'mailfrom' ),
            $config->get( 'fromname' )
        );
        $mailer->setSender($sender);
        $recipient = array($email);
        $mailer->addRecipient($recipient);
        $mailer->setSubject('Coupon piattaforma elearning');


        $template = JPATH_COMPONENT . '/models/template/coupondispenser.tpl';


        $smarty = new EasySmarty();
        $smarty->assign('coupon', $coupon);

        $mailer->setBody($smarty->fetch_template($template, null, true, false, 0));
        $mailer->isHTML(true);

        if (!$mailer->Send())
            throw new RuntimeException('Error sending mail coupon dispenser', E_USER_ERROR);
        return true;
    }


    protected function populateState()
{
    $app = JFactory::getApplication('site');

    // Load state from the request.
    $pk = $app->input->getInt('id');
    $this->setState('coupondispenser.id', $pk);

    // Load the parameters.
    $params = $app->getParams();
    $this->setState('params', $params);


    $this->setState('filter.language', JLanguageMultilang::isEnabled());
}

}
