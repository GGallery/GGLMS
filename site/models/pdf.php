<?php

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');


/**
 * GGlms Attestato Model
 *
 * @package    Joomla.Components
 * @subpackage GGLms
 * @author Diego Brondo <diego@ggallery.it>
 * @version 0.9
 */
class gglmsModelPdf extends JModelLegacy
{

    private $_user_id;
    //    private $_user;
    private $_quiz_id;
    private $_item_id;

    public function __construct($config = array())
    {
        parent::__construct($config);

        $this->id_elemento = JRequest::getInt('content', 0);

        $user = JFactory::getUser();
        $this->_user_id = $user->get('id');
    }

    public function __destruct()
    {
    }

    public function _generate_pdf($user,
                                  $orientamento,
                                  $attestato,
                                  $contenuto_verifica,
                                  $dg,
                                  $tracklog,
                                  $ateco ,
                                  $coupon,
                                  $multi = false,
                                  $dati_corso = null)
    {


        try {
            require_once JPATH_COMPONENT . '/libraries/pdf/certificatePDF.class.php';
            $orientation = $orientamento;
            $pdf = new certificatePDF($orientation);
            $datetest = $contenuto_verifica->getStato($user->id)->data;
            if ($datetest === null || $datetest == '0000-00-00')
                throw new RuntimeException('L\'utente non ha superato l\'esame o lo ha fatto in data ignota', E_USER_ERROR);

            // indice incrementale riferito all'id della riga di completamento del quiz
            $id_quiz_ref = $contenuto_verifica->getStato($user->id)->c_id;

            $info['data_superamento'] = $datetest;
            $info['path_id'] = $attestato->id;
            $info['path'] = $_SERVER['DOCUMENT_ROOT'] . '/mediagg/contenuti/';
            $info['content_path'] = $info['path'] . $info['path_id'];
            $info['quiz_id'] = $id_quiz_ref;

            // non va troppo bene
            if (DOMINIO == ""
                || DOMINIO == 'DOMINIO') {

                //$hostname = parse_url("http://".$_SERVER["HTTP_HOST"], PHP_URL_HOST);
                $_https = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) ? 'https' : 'http';
                $hostname = parse_url($_https . "://".$_SERVER["HTTP_HOST"], PHP_URL_HOST);

                $_arr_host = explode(".", $hostname);
                // indirizzi tipo https://dominio.it
                if (count($_arr_host) < 3) {
                    $hostname = $_arr_host[0] . "." . $_arr_host[1];
                }
                // altri tipo www.dominio.it oppure terzo.dominio.it
                else {
                    $hostname = $_arr_host[1] . "." . $_arr_host[2];
                }

                define('DOMINIO', $hostname);

            }

            $info['logo'] = DOMINIO;
            $info['firma'] = DOMINIO;
            $info['dg'] = $dg;
            $info['ateco'] = $ateco;
            $info['tracklog'] = $tracklog;
            $info['coupon'] = $coupon;

            // header aggiuntivi per data inizio/fine corso
            if (!is_null($dati_corso)
                && !empty($dati_corso)) {
                $info['data_inizio_corso'] = isset($dati_corso[0]->data_inizio_corso) ? $dati_corso[0]->data_inizio_corso : "";
                $info['data_fine_corso'] = isset($dati_corso[0]->data_fine_corso) ? $dati_corso[0]->data_fine_corso : "";
            }

            // modifica per integrare il template in base alla tipologia
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                ->select("COALESCE(c.tipologia_coupon, 'nonimpostato') AS tipologia_coupon")
                ->from('#__gg_coupon c')
                ->where("coupon = '" . $coupon . "'");
            $db->setQuery($query);
            $_tipologia = $db->loadResult();
            $_tipologia_coupon_ext = (!is_null($_tipologia) && $_tipologia != '' && $_tipologia != 'nonimpostato') ? $_tipologia : "";

            // se vengo da una generazione multipla di coupon ritorno coupon vuoto come da comportamento originale
            if ($multi)
                $info['coupon'] = '';

            $template = "file:" . $_SERVER['DOCUMENT_ROOT'] . '/mediagg/contenuti/' . $attestato->id . "/" . $attestato->id . $_tipologia_coupon_ext . ".tpl";

            $customTemplate = $this->customTemplate(); //check sul campo usergroups_details => attestati_custom. Se == 1 cerco il template con l'alias dell'associato
            // modifica in base alla tipologia anche per i custom template
            if ($customTemplate) {
                $customFile = $_SERVER['DOCUMENT_ROOT'] . '/mediagg/contenuti/' . $attestato->id . "/" . $customTemplate . $_tipologia_coupon_ext . ".tpl";

                if (file_exists($customFile)) {
                    $template = 'file:' . $customFile;
                } else {
                    throw new RuntimeException($customTemplate . " NOT EXIST", E_USER_ERROR);
                }
            }

            $pdf->add_data((array)$user);
            $pdf->add_data($info);



            $nomefile = "attestato_" . $user->nome . "_" . $user->cognome . ".pdf";
            $pdf->fetch_pdf_template($template, null, true, false, 0);

            if ($multi == true) {
                // se è un download di attesati multipli ritorno l'oggetto pdf
                return $pdf;

            } else {
                //altrimenti lo scarico
                ob_end_clean();
                // così facendo rinomina due volte .pdf
                //$pdf->Output($nomefile . '.pdf', 'D');
                $pdf->Output($nomefile, 'D');
                return 1;
            }


        } catch (Exception $e) {
            // FB::log($e);
            DEBUGG::error($e, 'error generate_pdf');
        }
        return 0;
    }

    private function customTemplate()
    {
        try {
            $query = $this->_db->getQuery(true)
                ->select('alias, attestati_custom')
                ->from('#__usergroups_details as a')
                ->where('a.dominio = "' . DOMINIO . '"');
//                ->where('a.dominio = "formazione.assiterminal.it"'); // force example domain

            $this->_db->setQuery($query);
            if (false === ($results = $this->_db->loadAssoc()))
                throw new RuntimeException($this->_db->getErrorMsg(), E_USER_ERROR);

            return $results['attestati_custom'] == 1 ? $results['alias'] : '';


        } catch (Exception $e) {
            DEBUGG::error($e, 'customTemplate');
        }
    }

    public function _generate_libretto_pdf($data, $user)
    {

        try {
            require_once JPATH_COMPONENT . 'components/com_gglms/models/libraries/pdf/certificatePDF.class.php';
            $pdf = new certificatePDF();


            $template = JPATH_COMPONENT . '/models/template/libretto_cicli.tpl';

            $data_array = array();
            $data_array['rows'] = $data;
            $pdf->add_data($user);
            $pdf->add_data($data_array);


            $nomefile = "libretto_" . $user['cognome'] . "_.pdf";


            $pdf->fetch_pdf_template($template);

            $pdf->Output($nomefile, 'D');

            return 1;
        } catch (Exception $e) {
            // FB::log($e);
            DEBUGG::error($e, 'error generate_pdf');
        }


        return 0;

    }


}
