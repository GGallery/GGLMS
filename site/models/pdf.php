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

    public function _generate_pdf($user, $orientamento, $attestato, $contenuto_verifica, $dg, $tracklog,$ateco , $multi = false )
    {


        try {
            require_once JPATH_COMPONENT . '/libraries/pdf/certificatePDF.class.php';
            $orientation = $orientamento;
            $pdf = new certificatePDF($orientation);
            $datetest = $contenuto_verifica->getStato($user->id)->data;
            if ($datetest === null || $datetest == '0000-00-00')
                throw new RuntimeException('L\'utente non ha superato l\'esame o lo ha fatto in data ignota', E_USER_ERROR);


            $info['data_superamento'] = $datetest;
            $info['path_id'] = $attestato->id;
            $info['path'] = $_SERVER['DOCUMENT_ROOT'] . '/mediagg/contenuti/';
            $info['content_path'] = $info['path'] . $info['path_id'];
            $info['logo'] = DOMINIO;
            $info['firma'] = DOMINIO;
            $info['dg'] = $dg;
            $info['ateco'] = $ateco;
            $info['tracklog'] = $tracklog;


            $template = "file:" . $_SERVER['DOCUMENT_ROOT'] . '/mediagg/contenuti/' . $attestato->id . "/" . $attestato->id . ".tpl";

            $customTemplate = $this->customTemplate(); //check sul campo usergroups_details => attestati_custom. Se == 1 cerco il template con l'alias dell'associato
            if ($customTemplate) {
                $customFile = $_SERVER['DOCUMENT_ROOT'] . '/mediagg/contenuti/' . $attestato->id . "/" . $customTemplate . ".tpl";

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
                $pdf->Output($nomefile . '.pdf', 'D');
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
