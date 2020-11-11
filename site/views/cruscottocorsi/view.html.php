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
require_once JPATH_COMPONENT . '/controllers/api.php';

class gglmsViewCruscottoCorsi extends JViewLegacy {

    protected $params;

    function display($tpl = null)
    {
        $model = new gglmsModelReport();

        $this->corsi = $model->getCorsi(true);

        $api_controller = new gglmsControllerApi();
        $this->arr_date_descrizione = $api_controller->get_date_per_contenuto();
        $this->con_orari = count($this->arr_date_descrizione) > 0 ? true : false;

        $this->dettaglio_corsi = utilityHelper::getDettaglioDurataByCorsi($this->corsi, $this->arr_date_descrizione);

        JHtml::_('stylesheet','https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css');

        parent::display($tpl);
    }

}
