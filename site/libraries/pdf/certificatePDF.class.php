<?php

require_once JPATH_COMPONENT . '/libraries/pdf/certificatePDF.class.php';


require_once(JPATH_COMPONENT . '/libraries/pdf/tcpdf/config/lang/ita.php');
require_once(JPATH_COMPONENT . '/libraries/pdf/tcpdf/tcpdf.php');
require_once(JPATH_COMPONENT . '/libraries/smarty/EasySmarty.class.php');

class certificatePDF extends TCPDF {

    private $_data;

    public function __construct($orientation = 'L', $unit = 'mm', $format = 'A4', $unicode = true, $encoding = 'UTF-8', $diskcache = false, $pdfa = false) {
        parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache, $pdfa);
        $this->SetCreator('COM_GGLMS');
        $this->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $this->SetFont('helvetica', '', 10);
        $this->_data = array();
    }

    public function __destruct() {
        parent::__destruct();
        unset($this->_data);
    }

    /**
     * Imposta i dati da passare al template.
     * @param array $data Array associativo chiave => valore
     */
    public function set_data($data) {
        if (is_array($data))
            $this->_data = $data;
    }

    /**
     * Aggiunge parametri da passare al template
     * @param array $data Array associativo chiave => valore
     */
    public function add_data($data) {
        if (is_array($data))
            $this->_data = array_merge($this->_data, $data);
    }

    /**
     * Combina i dati e il template generando la parte una o più pagine PDF.
     * Nel template deve essere usata la variabile {$data} che contine tutti
     * i valori passati.
     * 
     * @param string $template 
     * @param string $cache_id identificatore univoco per la copia di cache e di compilazione (default NULL).
     * @param bool $check se TRUE Easy Smarty guarda se il file template &#232; cambiato e lo ricompila (default TRUE). Una volta che l'applicazione viene messa in produzione (quindi i template non cambieranno pi&ugrave;), il passo di check non &#232; pi&ugrave; necessario &#232; consigliabile, per massimizzare  le prestazioni, mettere questo parametro a FALSE.
     * @param bool $cache se TRUE l'output &#232; memorizzato nelle cache (default TRUE).
     * @param int $lifetime tempo di validit&#224; (in secondi) della copia in cache; per default &#232; 120 (2 minuti). $cache deve essere impostato a TRUE perch&#233; $lifetime abbia significato. Un valore di -1 significa cache senza scadenza. Il valore 0 far&#224; s&#236; che la cache venga sempre rigenerata (&#232; utile solo in fase di test, per disabilitare il caching un metodo pi&ugrave; efficiente &#232; impostare $cache a FALSE).
     */
    public function fetch_pdf_template($template, $cache_id = null, $check = true, $cache = false, $lifetime = 120) {
        $this->AddPage();
        $smarty = new EasySmarty();

        $smarty->assign('data', $this->_data);

        //FB::log($this->_data);
        $html = $smarty->fetch_template($template, $cache_id, $check, $cache, $lifetime);
        //FB::log($html);

        $this->writeHTML($html, true, true, true, false, '');
        $this->lastPage();
    }

}

?>