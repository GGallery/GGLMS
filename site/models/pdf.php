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
class gglmsModelPdf extends JModelLegacy {

    private $_user_id;
    //    private $_user;
    private $_quiz_id;
    private $_item_id;

    public function __construct($config = array()) {
        parent::__construct($config);

        $this->id_elemento= JRequest::getInt('content', 0);

        $user = JFactory::getUser();
        $this->_user_id = $user->get('id');
    }

    public function __destruct() {
    }


    public function _generate_pdf($user, $orientamento,$attestato, $contenuto_verifica) {
        try {
            require_once JPATH_COMPONENT . '/libraries/pdf/certificatePDF.class.php';
            $orientation=$orientamento;
            $pdf = new certificatePDF($orientation);

            if (null === ($datetest = $contenuto_verifica->getStato()->data))
                throw new RuntimeException('L\'utente non ha superato l\'esame o lo ha fatto in data ignota', E_USER_ERROR);

            $info['data_superamento']=$datetest;
            $info['path_id'] = $attestato->id;
            $info['path'] = $_SERVER['DOCUMENT_ROOT'].'/mediagg/contenuti/';
            $info['content_path'] = $info['path'] . $info['path_id'];

            $template = "file:" . $_SERVER['DOCUMENT_ROOT'].'/mediagg/contenuti/'. $attestato->id . "/" . $attestato->id . ".tpl";

            $pdf->add_data((array)$user);
            $pdf->add_data($info);

            $nomefile = "attestato_" . $user->nome . "_" . $user->cognome . ".pdf";

            $pdf->fetch_pdf_template($template, null, true, false, 0);
            $pdf->Output($nomefile, 'D');

            return 1;
        } catch (Exception $e) {
            // FB::log($e);
            DEBUGG::error($e, 'error generate_pdf');
        }
        return 0;
    }

    public function _generate_libretto_pdf($data, $user){

        try{
            require_once JPATH_COMPONENT . '/libraries/pdf/certificatePDF.class.php';
            $pdf = new certificatePDF();



            $template = JPATH_COMPONENT . '/models/template/libretto_cicli.tpl';

            $data_array=array();
            $data_array['rows']=$data;
            $pdf->add_data($user);
            $pdf->add_data($data_array);


            $nomefile = "libretto_" . $user['cognome'] . "_.pdf";



            $pdf->fetch_pdf_template($template);

            $pdf->Output($nomefile, 'D');

            return 1;
        }catch (Exception $e) {
            // FB::log($e);
            DEBUGG::error($e, 'error generate_pdf');
        }


        return 0;

    }


}