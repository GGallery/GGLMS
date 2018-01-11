<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2016 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die;

class gglmsControllerConfigs extends JControllerForm
{
    /**
     * Save configuration data
     */
    public function save($key = NULL, $urlVar = NULL)
    {

        $app = JFactory::getApplication();
        $postData = $app->input->post;


        $data = $postData->get('jform', null, 'RAW'); //RAW  necessario per portare formattazione html testo presentazione attestato



        $model = $this->getModel();
        $model->store($data);

        $this->setRedirect('index.php?option=com_gglms&view=configs&extension=com_gglms', JText::_('Configurazioni salvate correttamente'));
    }

    /**
     * Cancel configuration action, redirect back to dashboard
     */
    public function cancel($key = NULL, $urlVar = NULL)
    {
        $this->setRedirect('index.php?option=com_gglms', JText::_('Nessuna configurazione salvata'));
    }
}
