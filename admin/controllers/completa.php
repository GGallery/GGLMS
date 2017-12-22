<?php
/**
 * Created by PhpStorm.
 * User: Antonio
 * Date: 18/12/2017
 * Time: 12:59
 */


/**
 * @package		Joomla.Tutorials
 * @subpackage	Component
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		License GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');
require_once JPATH_COMPONENT . '/models/report.php';

class gglmsControllerCompleta extends JControllerAdmin
{


                            //ATTENZIONE IL CODICE E' IMPOSTATO PER IL CODICE_CORSO 111 MODIFICARE GLI ULTIMI DUE METODI

    private $db;

    public function do_it(){

        try {

            $user_ids = $this->get_user_id();
            $id_contenuti = $this->get_contenuti();
            $count=0;
            foreach ($user_ids as $user_id) {
                foreach ($id_contenuti as $contenuto) {
                    $db = JFactory::getDbo();
                    //$query = "INSERT INTO #__gg_scormvars (scoid, userid, VarName, varValue) VALUES($SCOInstanceID, $UserID, '$safeVarName', '$safeVarValue') ON DUPLICATE KEY UPDATE varValue='$safeVarValue'";
                    $queryscormlesson = 'INSERT IGNORE INTO #__gg_scormvars (scoid,userid,varName,varValue) ';
                    $queryscormlesson = $queryscormlesson . 'VALUES (' . $contenuto->id . ',' . $user_id->id . ',\'cmi.core.lesson_status\',\'completed\') ON DUPLICATE KEY UPDATE varValue=\'completed\'';
                    $db->setQuery($queryscormlesson);
                    $db->execute();

                    $queryscormvisit = 'INSERT IGNORE INTO #__gg_scormvars (scoid,userid,varName,varValue) ';
                    $queryscormvisit = $queryscormvisit . 'VALUES (' . $contenuto->id . ',' . $user_id->id . ',\'cmi.core.last_visit_date\',\'2017-12-22\')ON DUPLICATE KEY UPDATE varValue=\'completed\'';
                    $db->setQuery($queryscormvisit);
                    $db->execute();
                }
                $queryquizdeluxe=null;
                $id_quiz_deluxes=$this->is_last_content_quiz();

                foreach ($id_quiz_deluxes as $id_quiz_deluxe) {

                    if ((int)$id_quiz_deluxe->id_quizdeluxe > 0) {

                        $queryquizdeluxe = 'INSERT IGNORE INTO #__quiz_r_student_quiz (c_quiz_id,c_student_id,c_total_score,c_total_time,c_date_time,c_passed,unique_id,allow_review,c_order_id,c_rel_id,c_lid,unique_pass_id,c_finished,c_passing_score) VALUES(';
                        $queryquizdeluxe = $queryquizdeluxe . $id_quiz_deluxe->id_quizdeluxe . ',';
                        $queryquizdeluxe = $queryquizdeluxe . $user_id->id . ',';
                        $queryquizdeluxe = $queryquizdeluxe . '8,';
                        $queryquizdeluxe = $queryquizdeluxe . '200,';
                        $queryquizdeluxe = $queryquizdeluxe . '\'2017-12-22 00:00:00\',';
                        $queryquizdeluxe = $queryquizdeluxe . '1,';
                        $unique_id = (string)(time() . uniqid());
                        $queryquizdeluxe = $queryquizdeluxe . '\'' . $unique_id . '\',';
                        $queryquizdeluxe = $queryquizdeluxe . '0,';
                        $queryquizdeluxe = $queryquizdeluxe . '0,';
                        $queryquizdeluxe = $queryquizdeluxe . '0,';
                        $queryquizdeluxe = $queryquizdeluxe . '0,';
                        $queryquizdeluxe = $queryquizdeluxe . '\'' . $unique_id . '\',';
                        $queryquizdeluxe = $queryquizdeluxe . '1,';
                        $queryquizdeluxe = $queryquizdeluxe . '\'6,75\')';

                        $db->setQuery($queryquizdeluxe);

                        $db->execute();
                    }

                }
                $count++;
            }
            echo  $count;
            JFactory::getApplication()->close();

        }catch (exceptions $ex){

            echo $ex->getMessage().$queryscormlesson.' <br> '.$queryscormvisit.' <br> '.$queryquizdeluxe.' <br> ';
            JFactory::getApplication()->close();
        }

    }


    public function get_user_id(){

        try {

            $db = JFactory::getDbo();
            $query = 'select id from #__users_codicefiscale where codicefiscale in (\'CLLNRC88C01A859Q\',

\'LSCMNN89H59A859G\',
\'NCLNNA54M60C363T\',
\'BBBSFN73L61A859Q\',
\'PRTLNE81A68A859E\',
\'MNGCHR91A51A859Z\',
\'GBRFNC78C51A859B\',
\'BRTCLD67H50A859A\',
\'GGLSFN91H06F351K\',
\'GRMCTR58E59I822R\',
\'RSSLSE82L57L219U\',
\'LVRPLA67E18D205U\',
\'MAIPLA91L69F351W\',
\'RVTGNN54B20F351P\',
\'CMPRRT91C08F351X\',
\'TSSCLD83B48A940U\',
\'RSSVNC88B56I690F\',
\'GLLSLV88A49F205T\',
\'SRDPRI74T65F205O\',
\'BNFFBN74E57I625E\',
\'LNGFNC77D08G273E\',
\'FRLVGN77D63G273W\',
\'LNEGLI81M12H700J\',
\'RGGLTZ44R41G273Z\',
\'TTTLCU72C46G273V\',
\'BNMLNZ74S04G273Y\',
\'FRNLNI87E45L872B\',
\'SPRTZN71R48G273I\',
\'VTTGLL83E11I533X\',
\'BRNLCN54H46L736J\',
\'MNGRMR67P48Z114Z\',
\'MNRFRC81P55D205M\',
\'CRNNDA68M43L219W\',
\'FRTMRN59S64L219I\',
\'BFFLRI86H68L219U\',
\'LGGFLV54T56G463E\',
\'SNIRND56P04L219X\',
\'CRRNNA84M69L738Z\',
\'RNLCLD85E70G273W\',
\'RCCGNN75B46L219D\',
\'BRNMLN61C56B473B\',
\'PLNBBR73H53F952H\',
\'RNCVGN88B56B019Q\',
\'PNIKYZ88D68B019V\',
\'RNCSMN91L64B019K\',
\'CSNRRT89D20G062N\',
\'GBLSRN91S49G062G\',
\'DLLFNC83D48L746S\',
\'RDNPFR83A71A944K\',
\'CTTSLV88L54A859P\',
\'CNTVLR58T50A859E\',
\'GLRSFN86B50A859Y\',
\'BNLRTT91C44D205Y\',
\'SRNSLV77S56B573U\',
\'CRNNMR54A69A124Q\',
\'VNARNI83S64L219A\',
\'SFFLGU71H19B101E\',
\'BGNVNI70T66M055C\',
\'BRNLSE81M63D205G\',
\'GHBNLS70R47D205U\',
\'MNRNDR77B07D205I\',
\'MNSDNL77A60D205A\',
\'MRLMRA82H56D205I\',
\'MRTLRT84P01L219V\',
\'PLLSRA75P61D205S\',
\'PLRRTI74P47D205X\',
\'DCHMRA77P08H703T\',
\'STGPRN84L02F205H\',
\'LZZMLA88T63M102J\',
\'CTTPLA87H41F205X\',
\'VLLSLV83T58L319M\',
\'CNGGTN90M51I483M\',
\'CLDDTL62C58F093P\',
\'BRCCRN69A51H501R\',
\'DRSLSN75H49B019E\',
\'CRTSFN85A02B019E\',
\'PCCMCL68C49H037P\',
\'PRGMRC77E07F952N\',
\'VLSLNI78D64A429V\',
\'BRBLFR85M42A429L\',
\'NCTNNN71P04G273H\',
\'RCCGLI88M46G273S\',
\'CNTMMD82D70G273W\',
\'SGRMNL72P07G273D\',
\'BFFNNF64E69G273G\',
\'FCNFNC74C44G273W\',
\'LPRNTN89H19G273D\',
\'SLIMRA77H50D423A\',
\'RCLGDI82R52H294A\',
\'CSTNSC73R69H294T\',
\'LSIFRC68S47H294M\',
\'MCCLNE79A53H294Z\',
\'RMBGLI84P44C357E\',
\'GVNSFN63P18H294P\',
\'DPRLBT69B54H926N\',
\'RDNRNE50C67E522H\',
\'CHRBBR81D49H620L\',
\'MSRNLS92P50A539S\',
\'DMCMRA51L50L219C\',
\'CRCSST66D47C351J\',
\'MNTSRN87P62L219G\',
\'RGYCST55A08Z115S\',
\'NLDLGO70C48L219F\',
\'FCCFNC83L43E020O\',
\'DMTMNC73B62L219G\',
\'CNVCLD90A64L219I\',
\'DMTGNN36P18A084W\',
\'SNSFRZ69B14L219R\',
\'MRNLSS86A20L219H\',
\'CMPSRA88P62L219V\',
\'TVKMRK66L56Z224X\',
\'BSCRNT56T66L219P\',
\'RGGNNN58H49L219U\',
\'RNGDNL63D68L219C\',
\'CFRMNC77T51L219D\',
\'GRNTMR70D49F335B\',
\'FRGMCM61L02L219H\',
\'CMTLSN77D50D205V\',
\'BRNMNL78A61H727Z\',
\'RZOVNT88M49F335U\',
\'RCTCLD62R66I968D\',
\'TRCGLR58M66H727K\',
\'TRCLNE62R47L219Z\',
\'BRTSRA69A58L219O\',
\'RMBMRC56M11G196G\',
\'CVDLSN71M14L219X\',
\'RLAGRL69H60L219B\',
\'ZNLGZL59P56L219H\',
\'BGLCRL76A04L872C\',
\'RMAKTA73M68L750M\',
\'CNNFNC89T47L750G\',
\'BZZDNL77A58L750J\',
\'PRVPRZ64C57L750C\',
\'LBRGMN35P14L136A\',
\'LNZDTL76T70F918D\',
\'BGRCNZ80M54E512F\',
\'MNGLRA83E67F918J\',
\'CRPMRN81C60B107E\',
\'BRTCHR82D55E512G\',
\'PSNNLL51L58C498U\',
\'ZNTDTL63E71B886Z\',
\'PSQMRS60M68E512Q\',
\'GRDMLS82E60G273F\',
\'GRDRRT93C61G273N\',
\'MZZDNC61A62G273O\',
\'SPNPLA73A03G273P\') ';

            $db->setQuery($query);

            return $db->loadObjectList();

        }catch (exceptions $ex){

            echo $ex->getMessage();
        }
    }

    public function get_contenuti()
    {

        try {
            $db = JFactory::getDbo();
            $query = 'select c.id from #__gg_contenuti as c 
INNER JOIN #__gg_unit_map as um on c.id=um.idcontenuto
INNER JOIN #__gg_unit as u on um.idunita=u.id
where u.id=111 or u.unitapadre=111 order by c.id asc';
            $db->setQuery($query);
            return $db->loadObjectList();

            }catch (exceptions $ex){

            echo $ex->getMessage();
        }
    }

    private function is_last_content_quiz(){

        try {
            $db = JFactory::getDbo();
            $query = 'select id_quizdeluxe from #__gg_contenuti where id in(select c.id from #__gg_contenuti as c 
INNER JOIN #__gg_unit_map as um on c.id=um.idcontenuto
INNER JOIN #__gg_unit as u on um.idunita=u.id
where u.id=111 or u.unitapadre=111 order by c.id asc)';
            $db->setQuery($query);
            return $db->loadObjectList();

        }catch (exceptions $ex){

            echo $ex->getMessage();
        }

    }

}

