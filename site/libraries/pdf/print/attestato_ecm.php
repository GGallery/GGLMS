<?php

include 'class.ezpdf.php';
include 'db_connection.php';

$ku = base64_decode($_REQUEST['ku']);
$kq = base64_decode($_REQUEST['kq']);
$ki = base64_decode($_REQUEST['ki']);

$dbg = ($_REQUEST['dbg']);


if ($dbg == "1") {
    echo "<br> ku=" . $ku;
    echo "<br> kq=" . $kq;
    echo "<br> ki=" . $ki;
}

if (check_data($ku, $kq)) {

    $datatest = check_data($ku, $kq);
    $userdata = userdata($ku);
    $coursedata = coursedata($ki);

    if ($datatest && $userdata && $coursedata) {
        stampa($userdata, $coursedata, $datatest);
    } else {
        echo "Dati non corretti";
    }
}

function check_data($ku, $kq) {



    $query = "
         SELECT
         DATE_FORMAT(c_date_time,'%d-%m-%Y') as datatest
         FROM
            jos_quiz_r_student_quiz
         WHERE c_student_id= $ku 
         AND c_quiz_id = $kq 
         AND c_passed=1";
    $db_query = mysql_query($query) or die(mysql_error());
    $riga = mysql_fetch_array($db_query);

    return $riga['datatest'];
}

function userdata($ku) {

    $query = "SELECT *
			FROM jos_comprofiler
			WHERE id='" . $ku . "'
			LIMIT 1
			";
    $db_query = mysql_query($query) or die(mysql_error());

    $riga = mysql_fetch_array($db_query);

    $userdata->nome = $riga['firstname'];
    $userdata->cognome = $riga['lastname'];
    $userdata->nominativo = $userdata->nome . " " . $userdata->cognome;
    $userdata->professione = $riga['cb_professionedisciplina'];
    $userdata->datanascita = substr($riga['cb_datadinascita'], 8, 2) . "-" . substr($riga['cb_datadinascita'], 5, 2) . "-" . substr($riga['cb_datadinascita'], 0, 4);
    $userdata->luogonascita = $riga['cb_luogodinascita'];


    //$userdata->datanascita = "--";
    //$userdata->luogonascita = "--";

    return $userdata;
}

function coursedata($ki) {

    $id_corso = idelemento_to_idcorso($ki);
    $query = "
                      SELECT
                        *
                      FROM jos_tt_corsi
                      WHERE id='" . $id_corso . "'
                      LIMIT 1
					";
    $db_query = mysql_query($query) or die(mysql_error());

    $riga = mysql_fetch_array($db_query);

    $course_data->titoloattestato = $riga["titoloattestato"];
    $course_data->codice_ecm = $riga["codice_ecm"];
    $course_data->edizione_num = $riga["edizione_num"];
    $course_data->datainizio = $riga["datainizio"];
    $course_data->datafine = $riga["datafine"];
    $course_data->obbiettivi = $riga["obbiettivi"];
    $course_data->crediti = $riga["crediti"];
    $course_data->crediti_testo = $riga["crediti_testo"];
    $course_data->edizione_num = $riga["edizione_num"];
    $course_data->durata = $riga["durata"];

    return $course_data;
}

function idelemento_to_idcorso($ki) {
    $query = "
	SELECT
		jos_tt_corsi.id  as result
	FROM
		jos_tt_corsi
	LEFT JOIN jos_tt_moduli ON jos_tt_moduli.id_corso = jos_tt_corsi.id
	LEFT JOIN jos_tt_elementi ON jos_tt_elementi.id_modulo = jos_tt_moduli.id
	WHERE
		jos_tt_elementi.id = '" . $ki . "'
	LIMIT 1
	";

    $db_query = mysql_query($query) or die(mysql_error());
    $riga = mysql_fetch_array($db_query);
    return $riga['result'];
}

function stampa($user_data, $course_data, $datatest) {
    $pdf = & new Cezpdf('a4');

    $pdf->ezImage("header.jpg", -3, 500, 20, 'left');
    $pdf->selectFont('./fonts/Helvetica.afm');
    $pdf->ezSetCmMargins(2, 2, 2, 2);


    //stampo il titolo
    $text = "\n <b>Programma nazionale per la formazione continua degli operatori della Sanita'</b> \n \n";

    $pdf->ezText($text, 12, array('justification' => 'center', 'spacing' => '1.5'));


    //stampo l'introduzione
    $text = "Premesso che la <b>Commissione Nazionale per la Formazione Continua</b> ha accreditato provvisoriamente il Provider <b>GGALLERY SRL</b>  accreditamento n. <b>39</b> valido fino al 22/04/2012 Premesso che il Provider ha organizzato l'evento formativo n. <b>$course_data->codice_ecm</b>, edizione n. $course_data->edizione_num denominato \n <b>$course_data->titoloattestato</b> e tenutosi  dal <b>$course_data->datainizio</b> al <b>$course_data->datafine</b>, avente come obiettivi didattico/formativo generali: <i>$course_data->obiettivi</i>, assegnando all'evento stesso  N.<b>$course_data->crediti</b> ($course_data->crediti_testo) Crediti Formativi E.C.M.";

    $pdf->ezText($text, 11, array('justification' => 'centre', 'spacing' => '1.5'));





    $text = "\n \n 
	Il sottoscritto <b>PAOLO MACRI'</b> \n 
	Rappresentate Legale dell'organizzatore \n
	Verificato l'apprendimento del partecipante \n
	<b> ATTESTA CHE </b> \n
	il Dott./la Dott.ssa \n
	<b> $user_data->nominativo </b> \n
	in qualita' di $user_data->professione \n 
	nato a $user_data->luogonascita \n
	il $user_data->datanascita \n
	ha conseguito \n 
	N. $course_data->crediti ($course_data->crediti_testo) Crediti formativi per l'anno " . substr($datatest, 6) . "
	
  ";

    $pdf->ezText($text, 11, array('justification' => 'centre', 'spacing' => '1'));


    //stampo la data
    $text = " Genova, li $datatest \n \n";

    $pdf->ezText($text, 11, array('justification' => 'left', 'spacing' => '1.5'));

    //stampo la firma
    $text = "IL RAPPRESENTANTE LEGALE DELL'ORGANIZZATORE \n Dott. Paolo Macri' \n \n ";
    $pdf->ezText($text, 11, array('justification' => 'centre', 'spacing' => '1'));
    $pdf->ezImage("firma.jpg", -5, 90, 20, 'center');

//  echo "non stampo";
    $dbg = ($_REQUEST['dbg']);

    if (!$dbg) {
        $pdf->ezStream();
    }
}
?>



