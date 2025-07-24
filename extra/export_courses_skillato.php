<?php
/**
 * Created by IntelliJ IDEA.
 * User: Luca
 * Date: 18/02/2021
 * Time: 16:54
 */

// Set flag that this is a parent file.
define('_JEXEC', 1);

error_reporting(E_ALL | E_NOTICE);
ini_set('display_errors', 1);
define('JDEBUG', 1);

// Load system defines
if (file_exists(dirname(__DIR__) . '/defines.php'))
{
    require_once dirname(__DIR__) . '/defines.php';
}

if (!defined('_JDEFINES'))
{
    define('JPATH_BASE', dirname(__DIR__));
    require_once JPATH_BASE . '/includes/defines.php';
}

if (!defined('JPATH_COMPONENT')) {
    define('JPATH_COMPONENT', JPATH_SITE . '/components/com_gglms');
}

require_once JPATH_LIBRARIES . '/import.legacy.php';
require_once JPATH_LIBRARIES . '/cms.php';

// Load the configuration
require_once JPATH_CONFIGURATION . '/configuration.php';

class exportCoursesSkillato extends JApplicationCli {

    public function doExecute()
    {

        //$con_report = $this->input->get('con_report', 0);

        try {

            // Database connector
            $db = JFactory::getDBO();

            $this->out(date('d/m/Y H:i:s') . ' - inizio esecuzione script');

            $siteUrl = 'https://ilnostropunto.lafarmacia.it/';
            //$relativeFolderPath = rtrim(getcwd(), DIRECTORY_SEPARATOR) . '/';
            $arrCorsiCategoria = [];
            $arrCorsoModuliCategoria = [];
            $arrModuliLearningObject = [];

            $selCorsi = "SELECT * 
                        from #__gg_unit 
                        where is_corso = 1 
                        and pubblicato = 1";

            //$selCorsi .= " and id = 1468";
            //$selCorsi .= " and id = 1019";

            $db->setQuery($selCorsi);
            $rsCorsi = $db->loadAssocList();

            if (empty($rsCorsi))
                throw new Exception("Nessun corso disponibile", E_USER_ERROR);

            foreach ($rsCorsi as $corso) {

                $this->out(date('d/m/Y H:i:s') . ' - elaboro corso: ' . $corso['titolo']);
                $indexCorsiCategoria = 0;

                $selBox = "SELECT jgbd.description 
                            from #__gg_box_details jgbd 
                            join #__gg_box_unit_map jgbum on jgbd.id = jgbum.box 
                            where jgbum.id_unita = " . $corso['id'];

                $db->setQuery($selBox);
                $rsBox = $db->loadAssoc();

                $courseBox = $rsBox['description'] ?? '';

                // verifico la presenza di sottounita
                $selSottoUnita = "SELECT * 
                    from #__gg_unit jgu 
                    where jgu.unitapadre = " . $corso['id'];

                $db->setQuery($selSottoUnita);
                $rsSottoUnita = $db->loadAssocList();

                // selezione sotto unità o direttamente contenuti se non ci sono sotto unità
                if (!empty($rsSottoUnita)) {

                    $arrCorsiCategoria[$indexCorsiCategoria] = array(
                        'code'          => $corso['alias'],
                        'name_IT'       => $corso['titolo'],
                        'description_IT'    => '',
                        'color'             => '#000000',
                        'published_at'      => $corso['data_inizio'],
                        'section_code'      => $courseBox,
                        'user_set_code'     => $corso['titolo'],
                        'degree_type'       => '',
                    );

                    $this->out(date('d/m/Y H:i:s') . ' - ci sono sotto unità per ' . $corso['titolo']);

                    foreach($rsSottoUnita as $sottoUnita) {

                        $selContenuti = "SELECT jgc.* from 
                                        #__gg_unit jgu 
                                        join #__gg_unit_map jgum on jgum.idunita = jgu.id 
                                        join #__gg_contenuti jgc on jgum.idcontenuto = jgc.id
                                        where jgum.idunita = " . $sottoUnita['id'] . "
                                        and jgc.pubblicato = 1
                                        and jgc.tipologia in (1,2,3,5,6,9)
                                        order by jgum.ordinamento ";

                        $db->setQuery($selContenuti);
                        $rsContenuti = $db->loadAssocList();

                        if (empty($rsContenuti)) {
                            $this->out(date('d/m/Y H:i:s') . ' - NON ci sono contenuti per ' . $sottoUnita['titolo']);
                            continue;
                        }

                        $arrCorsoModuliCategoria[] = array(
                            'learning_object_code'  => $sottoUnita['alias'],
                            'category_code'         => $corso['alias']
                        );

                        $this->processCourseContents($rsContenuti, $sottoUnita, $siteUrl, $arrCorsiCategoria, $arrCorsoModuliCategoria, $arrModuliLearningObject, $indexCorsiCategoria, $corso, $db);

                    }

                }
                else {

                    $this->out(date('d/m/Y H:i:s') . ' - NON ci sono sotto unità per ' . $corso['titolo']);

                    $arrCorsiCategoria[$indexCorsiCategoria] = array(
                        'code'          => $corso['alias'],
                        'name_IT'       => $corso['titolo'],
                        'description_IT'    => '',
                        'color'             => '#000000',
                        'published_at'      => $corso['data_inizio'],
                        'section_code'      => $courseBox,
                        'user_set_code'     => $corso['titolo'],
                        'degree_type'       => '',
                    );

                    $selContenuti = "SELECT jgc.* from 
                                        #__gg_unit jgu 
                                        join #__gg_unit_map jgum on jgum.idunita = jgu.id 
                                        join #__gg_contenuti jgc on jgum.idcontenuto = jgc.id
                                        where jgum.idunita = " . $corso['id'] . "
                                        and jgc.pubblicato = 1
                                        and jgc.tipologia in (1,2,3,5,6,9)
                                        order by jgum.ordinamento ";

                    $db->setQuery($selContenuti);
                    $rsContenuti = $db->loadAssocList();

                    if (empty($rsContenuti)) {
                        $this->out(date('d/m/Y H:i:s') . ' - NON ci sono contenuti per ' . $corso['titolo']);
                        continue;
                    }

                    $this->processCourseContents($rsContenuti, $corso, $siteUrl, $arrCorsiCategoria, $arrCorsoModuliCategoria, $arrModuliLearningObject, $indexCorsiCategoria, [], $db);

                }

                $indexCorsiCategoria++;

            }
            
            $dir = __DIR__ . '/../tmp';
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }

            $fileCorsiCategoria = $dir . '/corsi-categoria_' . date('Ymd_His') . '.csv';
            $fileCorsoModuliCategoria = $dir . '/corsi-moduli-categoria_' . date('Ymd_His') . '.csv';
            $filenameModuliLearningObject = $dir . '/moduli-learningobject_' . date('Ymd_His') . '.csv';

            $writeCorsiCategoria = $this->writeCsv($arrCorsiCategoria, $fileCorsiCategoria);
            if (isset($writeCorsiCategoria['error'])) {
                $this->out(date('d/m/Y H:i:s') . " - Si è verificato un errore durante la scrittura di " . $fileCorsiCategoria);
            }
            
            $writeCorsoModuliCategoria = $this->writeCsv($arrCorsoModuliCategoria, $fileCorsoModuliCategoria);
            if (isset($writeCorsoModuliCategoria['error'])) {
                $this->out(date('d/m/Y H:i:s') . " - Si è verificato un errore durante la scrittura di " . $fileCorsoModuliCategoria);
            }

            $writeModuliLearningObject = $this->writeCsv($arrModuliLearningObject, $filenameModuliLearningObject);
            if (isset($writeModuliLearningObject['error'])) {
                $this->out(date('d/m/Y H:i:s') . " - Si è verificato un errore durante la scrittura di " . $filenameModuliLearningObject);
            }

            $this->out(date('d/m/Y H:i:s') . " - Procedimento completato");

        }
        catch (Exception $e) {

            $this->out(date('d/m/Y H:i:s') . ' - ERRORE: ' . $e->getMessage());
        }
    }

    function processCourseContents(
        array $rsContenuti,
        array $corso,
        string $siteUrl,
        array &$arrCorsiCategoria,
        array &$arrCorsoModuliCategoria,
        array &$arrModuliLearningObject,
        int $indexCorsiCategoria,
        array $corsoRef = [],
        $db = null
    ): void {

        foreach($rsContenuti as $contenuto) {

            $learningObjectCode = $contenuto['alias'];
            $categoryCode = $corso['alias'];
            $loType = 'Activity';
            $loTitle = $contenuto['titolo'];
            $commontContentPath = 'mediagg/contenuti/' . $contenuto['id'] . '/' . $contenuto['id']; 
            $imgUrl = $siteUrl . $commontContentPath . '.jpg';

            if (!empty($corsoRef)) {
                $learningObjectCode = $corso['alias'];
                $categoryCode = $corsoRef['alias'];
            }

            if (empty($corsoRef)) {
                $arrCorsoModuliCategoria[] = array(
                    'learning_object_code'  => $learningObjectCode,
                    'category_code'         => $categoryCode,
                );
            }

            // gestione delle varie tipologie
            switch($contenuto['tipologia']) {

                case 1:
                    // videoslide
                    $slideType = '';
                    $ext = '';
                    if (file_get_contents($siteUrl . $commontContentPath . '.mp4')) {

                        $this->out(date('d/m/Y H:i:s') . ' - video slide - MP4 esistente: ' . $siteUrl . $commontContentPath . '.mp4');

                        $slideType = 'VideoUpload';
                        $ext = 'mp4';
                    }
                    else if (file_get_contents($siteUrl . $commontContentPath . '.pdf')) {

                        $this->out(date('d/m/Y H:i:s') . ' - video slide - PDF esistente: ' . $siteUrl . $commontContentPath . '.pdf');

                        $slideType = 'DocumentUpload';
                        $ext = 'pdf';
                    }

                    if ($slideType != '') {
                        $arrModuliLearningObject[] = array(
                            'lo_type'   => $loType,
                            'lo_code'   => $learningObjectCode,
                            'lo_title'  => $loTitle,
                            'image_url' => $imgUrl,
                            'extracted_slide_points'    => '',
                            'extracted_slide_num'       => '',
                            'min_points'                => '',
                            'pool_ids'                  => '',
                            'lo_publication_date'       => '',
                            'slide_type'                => $slideType,
                            'slide_title'               => $contenuto['titolo'],
                            'resource_url'              => $siteUrl . $commontContentPath . '.' . $ext,
                            'resource_text'             => '',
                        );
                    }

                    break;
                
                case 2:
                    // video
                    $arrModuliLearningObject[] = array(
                        'lo_type'   => $loType,
                        'lo_code'   => $learningObjectCode,
                        'lo_title'  => $loTitle,
                        'image_url' => $imgUrl,
                        'extracted_slide_points'    => '',
                        'extracted_slide_num'       => '',
                        'min_points'                => '',
                        'pool_ids'                  => '',
                        'lo_publication_date'       => '',
                        'slide_type'                => 'VideoUpload',
                        'slide_title'               => $contenuto['titolo'],
                        'resource_url'              => $siteUrl . $commontContentPath . '.mp4',
                        'resource_text'             => '',
                    );
                    break;

                case 3:
                    // allegati 
                    // files di riferimento
                    $selFiles = "SELECT id as file_id, filename 
                                from #__gg_files
                                where id in (" . $contenuto['files'] . ")";

                    $db->setQuery($selFiles);
                    $rsFiles = $db->loadAssocList();

                    if(!empty($rsFiles)) {

                        $this->out(date('d/m/Y H:i:s') . ' - allegati - contenuto ' . $contenuto['titolo'] . ' ci sono file associati');

                        foreach($rsFiles as $singleFile) {

                            $fileUrl = $siteUrl . 'mediagg/files/' . $singleFile['file_id'] . '/' . $singleFile['filename'];
                            if (@file_get_contents($fileUrl)) {

                                $this->out(date('d/m/Y H:i:s') . ' - allegati - contenuto ' . $contenuto['titolo'] . ' file esistente all\'url ' . $fileUrl);

                                $arrModuliLearningObject[] = array(
                                    'lo_type'   => $loType,
                                    'lo_code'   => $learningObjectCode,
                                    'lo_title'  => $loTitle,
                                    'image_url' => $imgUrl,
                                    'extracted_slide_points'    => '',
                                    'extracted_slide_num'       => '',
                                    'min_points'                => '',
                                    'pool_ids'                  => '',
                                    'lo_publication_date'       => '',
                                    'slide_type'                => 'DocumentUpload',
                                    'slide_title'               => $contenuto['titolo'],
                                    'resource_url'              => $fileUrl,
                                    'resource_text'             => '',
                                );
                            }
                            else {
                                $this->out(date('d/m/Y H:i:s') . ' - allegati - contenuto ' . $contenuto['titolo'] . ' file non esistente all\'url ' . $fileUrl);
                            }

                        }
                    }
                    else {
                        $this->out(date('d/m/Y H:i:s') . ' - allegati - contenuto ' . $contenuto['titolo'] . ' non ci sono file associati');
                    }

                    break;

                case 5:
                    // attestato
                    $this->out(date('d/m/Y H:i:s') . ' - attestato - al contenuto ' . $contenuto['titolo'] . ' è associato un attestato');

                    $arrCorsiCategoria[$indexCorsiCategoria]['degree_type'] = $contenuto['titolo'];
                    break;

                case 6:
                    // solo testo

                    $this->out(date('d/m/Y H:i:s') . ' - solo testo');

                    $arrModuliLearningObject[] = array(
                        'lo_type'   => $loType,
                        'lo_code'   => $learningObjectCode,
                        'lo_title'  => $loTitle,
                        'image_url' => $imgUrl,
                        'extracted_slide_points'    => '',
                        'extracted_slide_num'       => '',
                        'min_points'                => '',
                        'pool_ids'                  => '',
                        'lo_publication_date'       => '',
                        'slide_type'                => 'DocumentText',
                        'slide_title'               => $contenuto['titolo'],
                        'resource_url'              => '',
                        'resource_text'             => $contenuto['descrizione'],
                    );
                    break;

                case 9:
                    // pdfsingolo
                    $this->out(date('d/m/Y H:i:s') . ' - pdfsingolo');

                    if (file_get_contents($siteUrl . $commontContentPath . '.pdf')) {

                        $this->out(date('d/m/Y H:i:s') . ' - pdfsingolo - contenuto: ' . $contenuto['titolo'] . ' - esiste documento');

                        $arrModuliLearningObject[] = array(
                            'lo_type'   => $loType,
                            'lo_code'   => $learningObjectCode,
                            'lo_title'  => $loTitle,
                            'image_url' => $imgUrl,
                            'extracted_slide_points'    => '',
                            'extracted_slide_num'       => '',
                            'min_points'                => '',
                            'pool_ids'                  => '',
                            'lo_publication_date'       => '',
                            'slide_type'                => 'DocumentUpload',
                            'slide_title'               => $contenuto['titolo'],
                            'resource_url'              => $siteUrl . $commontContentPath . '.pdf',
                            'resource_text'             => '',
                        );
                    }

                    break;

            }

        }

    }

    // private function listFiles(string $dir, array $allowedExtensions = ['pdf', 'mp4', 'jpg'])
    // {
    //     try {

    //         if (!is_dir($dir)) {
    //             throw new \Exception("La directory '$dir' non esiste.");
    //         }

    //         $result = [];
    //         $entries = array_diff(scandir($dir), ['.', '..']);

    //         foreach ($entries as $entry) {
    //             $path = $dir . DIRECTORY_SEPARATOR . $entry;
    //             if (is_file($path)) {
    //                 $ext = strtolower(pathinfo($entry, PATHINFO_EXTENSION));
    //                 if (in_array($ext, $allowedExtensions, true)) {
    //                     $result[] = $entry;
    //                 }
    //             }
    //         }

    //         return $result;
    //     }
    //     catch(\Exception $e) {
    //         return [];
    //     }
    // }

    private function writeCsv($data, $filename)
    {
        $retArr = [];
        try {

            $fp = fopen($filename, 'w');
            if ($fp === false) {
                throw new \Exception(STDERR, "Errore: impossibile aprire $filename per scrittura");
            }

            // 3. Scrivi il BOM UTF-8
            fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF));

            // 4. Headers
            $headers = array_keys($data[0] ?? []);
            fputcsv($fp, $headers);

            // 6. Scrittura di ogni riga
            foreach ($data as $row) {
                // Selezioniamo solo i campi stabiliti da headers
                $line = [];
                foreach ($headers as $field) {
                    $line[] = $row[$field];
                }
                fputcsv($fp, $line);
            }

            // 7. Chiusura del file
            fclose($fp);

            $retArr['status'] = 'success';

        }
        catch(\Exception $e) {
            $retArr['status'] = 'error';
            $retArr['msg'] = $e->getMessage();
        }

        return $retArr;
    }
}
JApplicationCli::getInstance('exportCoursesSkillato')->execute();
