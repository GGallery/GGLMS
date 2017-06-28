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

class gglmsControllerGeneraCoupon extends JControllerForm
{
    /**
     * Save configuration data
     */
    public function save($key = NULL, $urlVar = NULL)
    {
        $model = $this->getModel();

        if($model->generate())
            $this->setRedirect('index.php?option=com_gglms&view=generacoupon&extension=com_gglms', JText::_('Coupon generati correttamente'));
        else
            $this->setRedirect('index.php?option=com_gglms&view=generacoupon&extension=com_gglms', JText::_('ERRORI NELLA GENERAZIONE','error'));

    }

    /**
     * Cancel configuration action, redirect back to dashboard
     */
    public function cancel($key = NULL, $urlVar = NULL)
    {
        $this->setRedirect('index.php?option=com_gglms', JText::_('Operazione annullata'));
    }
}
