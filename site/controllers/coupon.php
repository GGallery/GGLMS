<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Controller for single contact view
 *
 * @since  1.5.19
 */
class gglmsControllerCoupon extends JControllerLegacy
{

	public function check_coupon() {

        $japp = JFactory::getApplication();

        $coupon = JRequest::getVar('coupon');
        $model = $this->getModel('coupon');
        $dettagli_coupon = $model->check_Coupon($coupon);

        if (empty($dettagli_coupon)) {
            $results['report'] = "<p> Il coupon inserito non è valido o è già stato utilizzato. (COD. 01)</p>";
            $results['valido'] = 0;
        } else {
            if (!$dettagli_coupon['abilitato']) {
                $results['report'] = "<p> Il coupon è in attesa di abilitazione. (COD. 03)</p>";
                $results['valido'] = 0;
            } else {
                $model->assegnaCoupon($coupon);
                $corsi_abilitati = $model->corsi_abilitati($coupon);

                $results['valido'] = 1;
                $results['report'] = "<p> Coupon valido. (COD.04)</p>";
                $results['report'] .= $model->get_listaCorsiFast($corsi_abilitati);
            }
        }

        echo json_encode($results);
        $japp->close();
	}
	
}
