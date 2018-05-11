<?php

function logger($data, $label = null, $stringfy= false){
    echo $label;
    echo "<pre>";
    if($stringfy)
        echo (string)$data;
    else
        print_r($data);
    echo "</pre>";
}

function readElement($VarName) {

    global $SCOInstanceID;
    global $UserID;
    global $db;

    $query = $db->getQuery(true)
        ->select("varValue")
        ->from("#__gg_scormvars")
        ->where("scoid=$SCOInstanceID")
        ->where("userid=$UserID")
        ->where("varName= '". $VarName."'");

    $db->setQuery($query);

    $value = $db->loadResult();

    return $value;
}

function writeElement($VarName,$VarValue) {

    global  $db;
    global $SCOInstanceID;
    global $UserID;

    $safeVarName = $db->escape($VarName);
    $safeVarValue = $db->escape($VarValue);

    //INIZIO MODIFICA TONY
    if($VarName == 'cmi.core.lesson_status' ){
        $curr_VarValue= readElement($VarName);
        if(($curr_VarValue == 'completed') || ($curr_VarValue == 'passed'))
            return;
    }
    //FINE MODIFICA TONY

    $query = "INSERT INTO #__gg_scormvars (scoid, userid, varName, varValue) VALUES($SCOInstanceID, $UserID, '$safeVarName', '$safeVarValue') ON DUPLICATE KEY UPDATE varValue='$safeVarValue'";

    $db->setQuery($query);
    $db->execute();
//    }


    return;
}

function initializeElement($VarName,$VarValue) {

    global $db;
    global $SCOInstanceID;
    global $UserID;

    // look for pre-existing values



//    $interaction = strpos($VarName, 'cmi.interactions');
//    if(!$interaction) {
    $query = $db->getQuery(true)
        ->select("VarValue")
        ->from("#__gg_scormvars")
        ->where("scoid=$SCOInstanceID")
        ->where("userid=$UserID")
        ->where("varName = '" . $VarName . "'");

    $db->setQuery($query);
    $result = $db->loadResult();

    echo "Initializing: ".$VarName.$VarValue."|| ";
    logger($result, 'result read');
    logger($query, 'query read' , 1);


    if (!$result) {
        try {
            $new_sco = new stdClass();
            $new_sco->scoid = $SCOInstanceID;
            $new_sco->userid = $UserID;
            $new_sco->varName = "$VarName";
            $new_sco->varValue = $VarValue;
            $db->insertObject('#__gg_scormvars', $new_sco);
        } catch (Exception $e) {
            logger((string)$query);
            logger($result);
            logger('inizialize');
            logger($e);
        }
        logger($result, 'result insert');
        logger($query, 'query read' , 1);

    }
//}

}

function initializeSCO() {

    global $db;
    global $SCOInstanceID;
    global $UserID;

    $query = $db->getQuery(true)
        ->select("count(VarName)")
        ->from("#__gg_scormvars")
        ->where("scoid=$SCOInstanceID")
        ->where("userid=$UserID");

    $db->setQuery($query);
    $count = $db->loadResult();

    if (!$count)
    {
        // elements that tell the SCO which other elements are supported by this API
        initializeElement('cmi.core._children','student_id,student_name,lesson_location,credit,lesson_status,entry,score,total_time,exit,session_time');
        initializeElement('cmi.core.score._children','raw');

        // student information
        initializeElement('cmi.core.student_name',getFromLMS('cmi.core.student_name'));
        initializeElement('cmi.core.student_id',getFromLMS('cmi.core.student_id'));

        // test score
        initializeElement('cmi.core.score.raw','');
        initializeElement('adlcp:masteryscore',getFromLMS('adlcp:masteryscore'));

        // SCO launch and suspend data
        initializeElement('cmi.launch_data',getFromLMS('cmi.launch_data'));
        initializeElement('cmi.suspend_data','');

        // progress and completion tracking
        initializeElement('cmi.core.lesson_location','');
        initializeElement('cmi.core.credit','credit');
        initializeElement('cmi.core.lesson_status','not attempted');
        initializeElement('cmi.core.entry','ab-initio');
        initializeElement('cmi.core.exit','');

        // seat time
        initializeElement('cmi.core.total_time','0000:00:00');
        initializeElement('cmi.core.session_time','');

        // set data_svolgimento
        initializeElement('cmi.core.completed_date','');

    }

    initializeElement('cmi.interactions._children', 'id,objectives,time,type,correct_responses,weighting,student_response,result,latency, RO');
    initializeElement('cmi.interactions._count', 0);

    // new session so clear pre-existing session time
    writeElement('cmi.core.session_time','');

    // create the javascript code that will be used to set up the javascript cache,
    $initializeCache = "var cache = new Object();\n";

    $query = $db->getQuery(true)
        ->select("VarName,VarValue")
        ->from("#__gg_scormvars")
        ->where("scoid=$SCOInstanceID")
        ->where("userid=$UserID");
    $db->setQuery($query);

    $result = $db->loadObjectList();

    foreach ($result as $item){
        $jvarvalue = addslashes($item->VarValue);
        $initializeCache .= "cache['$item->VarName'] = '$jvarvalue';\n";
    }


    // return javascript for cache initialization to the calling program
    return $initializeCache;

}

// ------------------------------------------------------------------------------------
// LMS-specific code
// ------------------------------------------------------------------------------------
function setInLMS($varname,$varvalue) {
    return "OK";
}

function getFromLMS($varname) {
    global $db;
    global $SCOInstanceID;
    global $UserID;

    $query = $db->getQuery(true)
        ->select('varValue')
        ->from("#__gg_scormvars")
        ->where("scoid=$SCOInstanceID")
        ->where("userid=$UserID")
        ->where("varName like ('".$varname."')");

    $db->setQuery($query);
    $res = $db->loadResult();

    return $res;

}


