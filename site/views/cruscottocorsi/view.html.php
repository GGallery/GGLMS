<?php
/**
 * Created by IntelliJ IDEA.
 * User: Francesca
 * Date: 28/10/2020
 * Time: 17:55
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

require_once JPATH_COMPONENT . '/models/unita.php';
require_once JPATH_COMPONENT . '/models/report.php';

class gglmsViewCruscottoCorsi extends JViewLegacy {

    protected $params;

    function display($tpl = null)
    {
        $model = new gglmsModelReport();

        $this->corsi = $model->getCorsi(true);
        $this->dettaglio_corsi = utilityHelper::getDettaglioDurataByCorsi($this->corsi);

        JHtml::_('stylesheet','https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css');

        parent::display($tpl);
    }

}
