<?php

require_once('smarty/Smarty.class.php');

/**
 * Percorso di sistema delle directory e delle librerie di Smarty.class.php; la costante SMARTY_DIR
 * viene utilizzata anche da Smarty.class.php, non deve essere rinominata e deve contenere lo "/" (o "\") finale.
 * Esempio struttura di Smarty in SMARTY_DIR:
 * - Smarty.class.php
 * - Smarty_Compiler.class.php
 * - Config_File.class.php
 * - debug.tpl
 * - internals/*.php
 * - plugins/*.php
 * .
 * Smarty necessita di altre quattro directory (templates, templates_c, configs e cache),
 * ma poich&#233; &#232; altamente raccomandato di impostare un insieme separato di queste directory
 * per ogni applicazione che user&#224; smarty, non verranno definite qui come costanti, ma potranno essere
 * impostate come parametri all'interno dell'applicazione che usa @see EasySmarty::.
 */
defined('SMARTY_DIR') || define('SMARTY_DIR', './');

/**
 * @brief EasySmarty: estende le funzionalit&#224; di Smarty.
 * Classe che estende le funzionalit&#224; di Smarty all'interno di un sito o di un singolo progetto,
 * configurando alcune variabili utilizzate da Smarty al fine di rendere pi&#249; veloce e facile il suo utilizzo.
 * Fornisce inoltre un sistema per messaggistica di debug ed implementa nuovi plugin Smarty.
 * Ottimizzato per PHP 5.0
 *
 
 * @version $Version$
 * @since 01/dic/2011
 * @license GNU Public License 3 (GPL3) {@link http://www.gnu.org/licenses/gpl-3.0.txt}
 */
class EasySmarty extends Smarty {

    /**
     * Costruttore
     */
    function __construct() {
        parent::__construct();
        $this->cache_dir = SMARTY_CACHE_DIR;
        $this->config_dir = SMARTY_CONFIG_DIR;
        $this->compile_dir = SMARTY_COMPILE_DIR;
        $this->template_dir = SMARTY_TEMPLATE_DIR;
        if (is_dir(SMARTY_PLUGINS_DIRS)) {
            $this->plugins_dir[] = SMARTY_PLUGINS_DIRS;
        }
        /*
          if (DEBUGGING) {
         */
        $this->debugging = true;
        //$this->debug_tpl = SMARTY_DIR . 'debug.tpl';
        /*
          }
         */
    }

    /**
     * Distruttore
     */
    function __destruct() {
        parent::__destruct();
    }

    /**
     * Visualizza il template. Dovete fornire un tipo e percorso corretti per la risorsa del template.
     * Come secondo parametro opzionale potete passare una cache id.
     * In caso di errore viene mostrato un messaggio di errore.
     *
     * @param string $template nome del template da utilizzare.
     * @param string $cache_id identificatore univoco per la copia di cache e di compilazione (default NULL).
     * @param bool $check se TRUE Easy Smarty guarda se il file template &#232; cambiato e lo ricompila (default TRUE). Una volta che l'applicazione viene messa in produzione (quindi i template non cambieranno pi&ugrave;), il passo di check non &#232; pi&ugrave; necessario; &#232; consigliabile, per massimizzare  le prestazioni, mettere questo parametro a FALSE.
     * @param bool $cache se TRUE l'output &#232; memorizzato nelle cache (default TRUE).
     * @param int $lifetime tempo di validit&#224; (in secondi) della copia in cache; per default &#232; 120 (2 minuti). $cache deve essere impostato a TRUE perch&#233; $lifetime abbia significato. Un valore di -1 significa cache senza scadenza. Il valore 0 far&#224; s&#236; che la cache venga sempre rigenerata (&#232; utile solo in fase di test, per disabilitare il caching un metodo pi&#249; efficiente &#232; impostare $cache a FALSE).
     * @throws Exception
     */
    public function display_template($template, $cache_id = null, $check = false, $cache = true, $lifetime = 120) {
        echo $this->fetch_template($template, $cache_id, $check, $cache, $lifetime);
    }

    /**
     * Come @see easy_smarty::display_template(), ma l'output viene restituito sotto forma di stringa.
     * In caso di errore viene restituita una stringa di errore.
     * @param string $template nome del template da utilizzare.
     * @param string $cache_id identificatore univoco per la copia di cache e di compilazione (default NULL).
     * @param bool $check se TRUE Easy Smarty guarda se il file template &#232; cambiato e lo ricompila (default TRUE). Una volta che l'applicazione viene messa in produzione (quindi i template non cambieranno pi&ugrave;), il passo di check non &#232; pi&ugrave; necessario &#232; consigliabile, per massimizzare  le prestazioni, mettere questo parametro a FALSE.
     * @param bool $cache se TRUE l'output &#232; memorizzato nelle cache (default TRUE).
     * @param int $lifetime tempo di validit&#224; (in secondi) della copia in cache; per default &#232; 120 (2 minuti). $cache deve essere impostato a TRUE perch&#233; $lifetime abbia significato. Un valore di -1 significa cache senza scadenza. Il valore 0 far&#224; s&#236; che la cache venga sempre rigenerata (&#232; utile solo in fase di test, per disabilitare il caching un metodo pi&ugrave; efficiente &#232; impostare $cache a FALSE).
     * @return string
     * @throws Exception
     */
    public function fetch_template($template, $cache_id = null, $check = false, $cache = true, $lifetime = 120) {
        $this->caching = $cache ? 2 : 0;
        $this->compile_check = $check;
        if ($cache) {
            try {
                if (!is_int($lifetime)) {
                    throw new DomainException('Cache lifetime must be a valid int', E_USER_WARNING);
                }
                $this->cache_lifetime = $lifetime;
            } catch (Exception $e) {
                debug::exception($e);
                $this->cache_lifetime = 0;
            }
        }
        if (!$this->templateExists($template)) {
            throw new SmartyException('Template file not found:' . ' ' . $template, E_USER_ERROR);
        }
        return $this->fetch($template, $cache_id, $cache_id);
    }

}

?>