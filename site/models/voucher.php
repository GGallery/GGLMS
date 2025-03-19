<?php

defined('_JEXEC') or die('Restricted access');

class gglmsModelVoucher extends JModelLegacy {

	protected $params;
	protected  $_db;


	public function __construct($config = array()) {
		parent::__construct($config);

		$this->_db = JFactory::getDbo();
		$this->_app = JFactory::getApplication('site');
		$this->params = $this->_app->getParams();

	}

    // controllo se il codice del voucher esiste e non è già stato speso
    public function checkVoucherByCode(string $requestedVoucher) {

        try {

            $checkVoucher = "SELECT *
                            FROM #__gg_quote_voucher
                            WHERE code = " . $this->_db->quote(trim(strtoupper($requestedVoucher))) . "
                            AND user_id IS NULL";
            $this->_db->setQuery($checkVoucher);

            return $this->_db->loadResult();

        }
        catch(\Exception $e) {
            utilityHelper::make_debug_log(__FUNCTION__, $e->getMessage() , __FUNCTION__);

            return null;
        }

    }

    // controllo se l'utente ha già utilizzato un voucher per l'anno corrente
    public function checkUsedVoucher(int $userId, int $annoCorrente) {

        try {

            $checkUserForYear = "SELECT *
                                    FROM #__gg_quote_voucher
                                    WHERE user_id = " . $this->_db->quote($userId) . "
                                    AND date LIKE " . $this->_db->quote("%" . $annoCorrente . "%");

            $this->_db->setQuery($checkUserForYear);
            return $this->_db->loadResult();

        }
        catch(\Exception $e) {
            utilityHelper::make_debug_log(__FUNCTION__, $e->getMessage() , __FUNCTION__);

            return null;
        }

    }

}

