<?php
/**
 * Created by IntelliJ IDEA.
 * User: Francesca
 * Date: 26/01/2021
 * Time: 09:10
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.model');
require_once JPATH_COMPONENT . '/models/contenuto.php';
require_once JPATH_COMPONENT . '/models/report.php';
require_once JPATH_COMPONENT . '/models/unita.php';
require_once JPATH_COMPONENT . '/models/config.php';
require_once JPATH_COMPONENT . '/models/generacoupon.php';
require_once JPATH_COMPONENT . '/models/syncdatareport.php';
require_once JPATH_COMPONENT . '/models/syncviewstatouser.php';
require_once JPATH_COMPONENT . '/controllers/zoom.php';

class gglmsControllerMt extends JControllerLegacy {

    private $_user;
    private $_japp;
    public $_params;
    public $_db;
    private $_config;
    private $_filterparam;
    public $mail_debug;

    public function __construct($config = array())
    {
        parent::__construct($config);
        $this->_japp = JFactory::getApplication();
        $this->_params = $this->_japp->getParams();

        $this->_user = JFactory::getUser();
        $this->_db = JFactory::getDbo();
        $this->_config = new gglmsModelConfig();

        $this->_filterparam->id_utente = JRequest::getVar('id_utente');
        $this->_filterparam->id_corso = JRequest::getVar('id_corso');

        $this->mail_debug = $this->_config->getConfigValue('mail_debug');
        $this->mail_debug = ($this->mail_debug == "" || is_null($this->mail_debug)) ? "luca.gallo@gallerygroup.it" : $this->mail_debug;


    }

    private function encrypt_decrypt($action, $string, $secret_key, $secret_iv) {
        //echo "entrato<br>";
        //echo $string;die;
        $output = null;
        // metodo di crypt
        $encrypt_method = "AES-256-CBC";
        // hash
        $key = hash('sha256', $secret_key);
        // AES-256-CBC si aspetta 16 bytes
        $iv = substr(hash('sha256', $secret_iv), 0, 16);

        // cripta la chiave
        if ( $action == 'encrypt' ) {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        } // decripta la chiave
        else if( $action == 'decrypt' ) {
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }

        return $output;
    }

    public function test_() {

        echo __FUNCTION__;

    }

    public function sinpe_new_extra() {

        try {

            $cf_s = array(
                'LNZCST64C68A859L',
                'BRNRRT87S66I419A',
                'PRLMRC75L15H501C',
                'PGNMCR81H57A512L',
                'CRRGSY73C41L845S',
                'CMPPTR81D18L781T',
                'SCRLND79H59C351J',
                'GLZMML67R11A662R',
                'DSTSLV71D49E884Q',
                'RSSCCT67P53B674D',
                'DNTGLI85E63H501O',
                'DLLFRC91E47L781V',
                'SPRLSE91T55A089Q',
                'NSLCHR86P54L364X',
                'RNCFRC91H60L117M',
                'CNTBBR78B59G224T',
                'MRRLSU79P70A757G',
                'MRZNLN58T24H501V',
                'DRNLCU61M52F548J',
                'SNTMNL77H69A123D',
                'GCMRRT80B42G224Z',
                'DNTSRN82D52B157E',
                'CRLLSN66L48L781Q',
                'PNIFRN90H70D940G',
                'ZRASBN89M67A345J',
                'LVRLCU80E44F912M',
                'VRNGLN57L28I462N',
                'GRZDNC67H62C342S',
                'SVNCLD73C67H501A',
                'PNLGNN75S50F205S',
                'GHRLRD56T06I632B',
                'PNZLDN63R71I138D',
                'BRBGLR90E45E648D ',
                'CMNSFN83D59G273F',
                'MMMLNR89H51G273P',
                'PLLCST67D54D205L',
                'RCCVNT84B59H620T',
                'SCTRFL73B50D969Q',
                'SCLRMN75T59D223P',
                'SDOVLR78R44H501E',
                'TRVLRI75C50L840G',
                'VLNNTN68C12L219X',
                'LNEFMN66B63D643V',
                'DSBCHR89S68A944E',
                'SLAMLD58L52F704F',
                'RMNCLD65RO2F158H',
                'TRCMNL66A60B2961',
                'PZZDBR77C63A859C',
                'MLCMRM65L67H836B',
                'GRGSRN82a61l117n',
                'Mrclsn67t46f205y',
                'BNUSVT62A18G964A',
                'BLLVNT94M66I480J',
                'PNZVNT84S46L219J',
                'PRRMHL89D43E514L',
                'MRNGLI73E61A479P',
                'NZDNNA83D54H501S',
                'CLMVLR82H65L049O',
                'DCPCHL64D48G309W',
                'MCCCLD76E62L219L',
                'NPLNNA83T41C129H',
                'PRLLSS76C59L424U',
                'SGMDTL80T60H501U',
                'SNCLPU72E51H501N',
                'MDNFNC89H65L781M',
                'SPSRSL90D43I483U ',
                'RNZMRA66L48E885A',
                'SGRLVO82E62D205T',
                'Rrdmpl83p62d969p',
                'CVLLSS70C44L682P',
                'RLNMRK97B56L219Z',
                'BRSNTN69H18A662A',
                'MLNMSM59S30A703Q',
                'CHRLSS85R71H355C',
                'MNCMVT69L48L219K',
                'FRNFLV76C47H792L',
                'MRSMCL80T68F839Z',
                'MNDMNO74A56E783G',
                'MSNMCH72S65G702C',
                'PCRNLT73D49H501R',
                'PRTVNT86A68E256S',
                'PRRSRA85R53C309Y',
                'FLMPLA68R27H501B',
                'ZCCNRT59E63I712P',
                'MGNTZN55H68H501Y',
                'GRNLCU61B05C770O',
                'BNNCNZ86A61L191C',
                'MCRMSL63T11F158V',
                'BGGNNA78P59L682R',
                'MZUFLV56C17F205Y',
                'MGGMRT92L61G337O',
                'BRLGPL73H30B885T',
                'RMRTMS68H19B963Y',
                'CSTVCN65R03G812M',
                'LBNFLR72T51F839H',
                'GRSFTR95B53H501I',
                'CRLPLG56D28E506A',
                'BRRRTI65R70H501Q',
                'FRLLRA69M59C351F',
                'RMENRT74A50H501N',
                'DLLCLO79M67B111H',
                'MLSCTT61A57E441G',
                'GRSNTA68A41E972S',
                'MZZMTN90R14F915C',
                'PTNDDM60E68F631G',
                'RSLNNL76D61D086H',
                'MCHSST58C18A544C',
                'NFNGNN89B59H223D',
                'DVLGGS56S51F205A',
                'BNNLNZ67C48Z133S',
                'DLTLML78L68L086W',
                'RLFDNL62P70L219H',
                'DLICRN76C70A225M',
                'BSCGNN86P68B819B',
                'SPGCRD53B10G337C',
                'NCLRNN85L51L418T',
                'VVIMNG73D58I462L',
                'CLNMNV68C43H501S',
                'CMNLCU59S45F203L',
                'CSLMHL66E30G687R',
                'MLTGDU73L12L103U',
                'PSQMLN80A64B519X',
                'MNTMLD85M56F839R',
                'BRNMHL60H25A662U',
                'BRCLRT73E69B111O',
                'DPRVNT88S58E889X',
                'FLLFNC86M49D612R',
                'GRVSLV72A51L219S',
                'MGHSLL92P44D284I',
                'MRNNMR56DA512Q',
                'PCCLCU56H48H501V',
                'PLTSLV68T48D61',
                'RGGNRC59E14G920T',
                'TNVMCM63H28L219E',
                'FVRFLV75A59D969Q',
                'FRRLDN55L66G478X',
                'GRVRNI78L51Z154E',
                'NSAFNC94C60A794I',
                'RPSMLS69L44C351U',
                'STPGDI88E68A462G',
                'TRRVLR63R55F839R',
                'CRVGPP68S25C514B',
                'ZRZMML73A41L157F',
                'PTTSRG57T17C034F',
                'GRVGTA86L53F839V',
                'DLRLDI65S58C224A',
                'CSSBRC72T53F952X',
                'BRTVNT66P03L781C',
                'GGGRLL65H58B429G',
                'LBNDNL64B53E506S',
                'NDRMRC79A24B157K',
                'BRBMRA72L19H501Y',
                'BLLLBI67D62M059J',
                'BLTSFN84D62D458Q',
                'BRTLNE94M42G224K',
                'BRSBDT95D65A390V',
                'BRNLRT73A26C413C',
                'BNUMTT62C06I158N',
                'BSNDBR67R68C573M',
                'cpllcc85s43a479w',
                'CPRGRL57T44B885L',
                'CMRMNL92E51E335C',
                'CCCBMN61L19L424N',
                'CROFNC56D22G749L',
                'DLCPLA69T65D969U',
                'DLPCCT64D52G478U',
                'DZOGLG63M41G388J',
                'FRNDEH85S51E445J',
                'FRRCDV73C49C351C',
                'GLLMCL80R68B963P',
                'GVZLDI72E67F205D',
                'GRCNGL77A27B963Z',
                'LRLCMN60L20A783I',
                'LMRLDE88R62G273N',
                'LRNFNC62M44E379Z',
                'MSASRA93L51M109Z',
                'MRLSVN72E51L259Z',
                'MSCNDR61T14E715X',
                'DRNTLI71C45F913T',
                'Rlncll83e63d969v',
                'PSLGPP65T07B157T',
                'PNDRLE92H41C800L',
                'PNNDTL79C69L219E',
                'PSCRVI72M65D969G',
                'RGHMSS85C56C573V',
                'rscrrt61a10c351e',
                'RSLNSC82C45H727N',
                'Rvtlnz96b14l840p',
                'RSSLLL67D01F839L',
                'SFRLSE77H62D969Z',
                'TRMCRN92H63C710Q',
                'TRTMCR66B45H501H',
                'RSULSN82M69G920S',
                'vggmtr79t61a662q',
                'VNCFNC76P66G337F',
                'VLUGPP58D07H703H',
                'DLSLCU82M11F839Y',
                'TRNFNC73C41C351R',
                'RSTCRL66R11A462H',
                'BGDLRT67C30G702G',
                'LBNLMR77H58B019E',
                'LTRRSL61T51A755L',
                'BRCBNC92A70F952J',
                'CSLMGV66M62I608L',
                'DBSVNT66B51F839Y',
                'DLBNDR88H30H501T',
                'FTTCRI67T27F839L',
                'FMLDNL73M24A271U',
                'GRDLSN88R66D451S',
                'GDURND69L25A271Z',
                'MRNFPP62C01F301I',
                'FRTLNI89H51A783J',
                'PRSGLN78R05F839T',
                'TRLMLS68S58G388M',
                'PLMFNC93B48F257I',
                'CCRLRA80P58F205Z',
                'LPUGSI64A61G273I',
                'NGRMNL80S62L219B',
                'LNTLNE92R60B111C',
                'GNLLNE75H51L219T',
                'MLVMRS54S56L219i',
                'SMDSNJ70M42Z129O',
                'RVCGPP90H25F839D',
                'PVRMNC62T69I480U',
                'FRCNNL61E55B748H',
                'NFRSMN82C69D969E',
                'CRLNMR64H63D969S',
                'PRTLSE93P52D969T',
                'CRCMLS91D51M109Y',
                'GRNLCA94R50L746Y',
                'RPTDNL81E53B042T',
                'MGGGLI93B63D969P',
                'LMBSNT89M66D969J',
                'CSRRRT87M04D391R');
            $extra_arr = [];

            $imp = "'" . implode( "','", $cf_s) . "'";
            $check_ug = [20,21,23];

            $query = "SELECT usr.id AS user_id, usr.username, comp.cb_codicefiscale
                        FROM #__comprofiler comp
                        JOIN #__users usr ON comp.user_id = usr.id
                        WHERE comp.cb_codicefiscale IN (" . $imp . ")";

            $this->_db->setQuery($query);
            $rows = $this->_db->loadAssocList();


            foreach ($rows as $key_row => $user) {


                $query_ug = "SELECT user_id
                                FROM #__user_usergroup_map
                                WHERE user_id = " . $this->_db->quote($user['user_id']) . "
                                AND group_id IN (" . implode(',', $check_ug) . ")"
                                ;
                $this->_db->setQuery($query_ug);
                $result = $this->_db->loadResult();

                if (is_null($result)
                    || !$result)
                    $extra_arr[] = $user['user_id'];

                /*
                if (($key = array_search($user['cb_codicefiscale'], $cf_s)) !== false) {
                    unset($cf_s[$key]);
                }
                */

            }

            echo implode("\n", $extra_arr);

        }
        catch (Exception $e) {
            echo "ERRORE: " . $e->getMessage();
        }

        $this->_japp->close();

    }

    public function sinpe_ug_move() {

        try {

            $arr_ids = [
                        1462,
                        3293,
                        3621,
                        4507,
                        4584,
                        4599,
                        4605,
                        4614,
                        4629,
                        4630,
                        4634,
                        4646,
                        4648,
                        4650,
                        4654,
                        4660,
                        4665,
                        4670,
                        4671,
                        4673,
                        4680,
                        4689,
                        4693,
                        4734,
                        4738,
                        4745,
                        4751,
                        4774,
                        4871,
                        4876,
                        4879,
                        4904,
                        4920,
                        4941,
                        4949,
                        4957,
                        4975,
                        4990,
                        4992,
                        5002,
                        5003,
                        5004,
                        5005,
                        5006,
                        5007,
                        5008,
                        5009,
                        5010,
                        5011,
                        5012,
                        5013,
                        5014,
                        5015,
                        5016,
                        5018,
                        5019,
                        5020,
                        5021,
                        5022,
                        5023,
                        5026,
                        5027,
                        5028,
                        5031,
                        5035,
                        5036,
                        5067
                        ];

            $del_ug = [28,30];
            $new_ug = [23,25];
            $completed = 0;

            $this->_db->transactionStart();
            foreach ($arr_ids as $key_user => $user_id) {

                // rimuovo utente da gruppi evento
                $query_del = "DELETE
                                FROM #__user_usergroup_map
                                WHERE user_id = " . $this->_db->quote($user_id) . "
                                AND group_id IN (" . implode(',', $del_ug) . ")";

                // aggiungo utente a gruppi istituzionali
                $query_ins = "INSERT INTO #__user_usergroup_map
                                (user_id, group_id) VALUES ";
                foreach ($new_ug as $key_ug => $ug) {
                    $query_ins .= "(" . $this->_db->quote($user_id) . ", " . $this->_db->quote($ug) . "),";
                }

                // cb_ultimoannoinregola
                $query_update = "UPDATE #__comprofiler
                                SET cb_ultimoannoinregola = 2022
                                WHERE user_id = " . $this->_db->quote($user_id);
                $this->_db->setQuery($query_update);
                if (!$this->_db->execute())
                    throw new Exception("update query ko -> " . $query_update, E_USER_ERROR);

                $this->_db->setQuery($query_del);
                if (!$this->_db->execute())
                    throw new Exception("delete query ko -> " . $query_del, E_USER_ERROR);

                $query_ins = rtrim(trim($query_ins), ",") . ";";
                $this->_db->setQuery($query_ins);
                if (!$this->_db->execute())
                    throw new Exception("insert query ko -> " . $query_ins, E_USER_ERROR);

                $now = date('Y-m-d H:i:s');
                $query_quote = "INSERT INTO #__gg_quote_iscrizioni (
                                                user_id,
                                                anno,
                                                tipo_quota,
                                                tipo_pagamento,
                                                data_pagamento,
                                                dettagli_transazione
                                                )
                                            VALUES (
                                                " . $this->_db->quote($user_id) . ",
                                                2022,
                                                'quota',
                                                'bonifico',
                                                " . $this->_db->quote($now) . ",
                                                'Socio straordinario da Congresso 2021'
                                            )";
                $this->_db->setQuery($query_quote);
                if (!$this->_db->execute())
                    throw new Exception("insert quote query ko -> " . $query_quote, E_USER_ERROR);

                $completed++;

            }

            $this->_db->transactionCommit();

            echo "TOTALI: " . count($arr_ids) . " | ELABORATI: " . $completed;

        }
        catch(Exception $e) {
            $this->_db->transactionRollback();
            echo "ERRORE: " . $e->getMessage();
        }

        $this->_japp->close();

    }



    public function get_tz() {

        $oggi = '2020-11-16T07:21:36Z';
        $dt = new DateTime($oggi, new DateTimeZone('UTC'));
        $dt->setTimezone(new DateTimeZone('Europe/Rome'));
        echo $dt->format('Y-m-d H:i:s');

        $this->_japp->close();

    }

    public function get_user_dt() {

        try {

            $modelUser = new gglmsModelUsers();
            $tmpuser = $modelUser->get_user($this->_filterparam->id_utente, 0, "cp");

            echo json_encode($tmpuser);

        }
        catch (Exception $e) {
            echo $e->getMessage();
        }

        $this->_japp->close();
    }

    public function get_last_insert_coupon() {

        try {

            $_ret = array();

            $query = $this->_db->getQuery(true)
                    ->select('messaggio')
                    ->from('#__gg_error_log')
                    ->where('messaggio LIKE ' . $this->_db->quote('%api_genera_coupon_response%'))
                    ->order('id DESC');

            $this->_db->setQuery($query);
            $result = $this->_db->loadAssoc();

            if (is_null($result)
                || !isset($result['messaggio'])
                || $result['messaggio'] == "")
                throw new Exception("Nessun riferimento trovato", 1);

            $_response = preg_replace('/\s/', '', $result['messaggio']);
            $_response = str_replace("api_genera_coupon_response:", "", $_response);

            $_decode = json_decode($_response);

            if (
                (is_object($_decode) && !isset($_decode->id_iscrizione))
                    || (is_array($_decode) && !isset($_decode['id_iscrizione']))
                )
                throw new Exception("Il riferimento ha un valore non valido", 1);


            $_ret['success'] = (is_object($_decode)) ? $_decode->id_iscrizione : $_decode['id_iscrizione'];

        }
        catch (Exception $e) {
            $_ret['error'] = $e->getMessage();
        }

        echo json_encode($_ret);
        $this->_japp->close();

    }

}
