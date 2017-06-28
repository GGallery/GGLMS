<?php

include 'class.ezpdf.php';
include 'db_connection.php';
  

$v=base64_decode($_REQUEST['v']);
$v=explode("-",$v);



if($v[2]==md5(date("mdy"))&& check_data($v[3], $v[0]))
{
	$datatest=check_data($v[3], $v[0]);
		
	$userdata=userdata($v[0]);

	
	$coursedata= coursedata($v[1]);
 	
 		
	stampa($userdata,$coursedata, $datatest);
}	
else
{
	echo "Stampa non permessa";
}


function idelemento_to_idcorso($idelemento)
{
$query="
	SELECT
		jos_tt_corsi.id  as result
	FROM
		jos_tt_corsi
	LEFT JOIN jos_tt_moduli ON jos_tt_moduli.id_corso = jos_tt_corsi.id
	LEFT JOIN jos_tt_elementi ON jos_tt_elementi.id_modulo = jos_tt_moduli.id
	WHERE
		jos_tt_elementi.id = '".$idelemento."'
	LIMIT 1
	";


  $db_query = mysql_query($query) or die (mysql_error());
  $riga = mysql_fetch_array($db_query);
	return $riga['result'];
  
  
}

function check_data($id_elemento, $id_utente){


	
$query= "SELECT
jos_quiz_r_student_quiz.c_date_time,
DATE_FORMAT(jos_quiz_r_student_quiz.c_date_time, '%d/%m/%Y') as timemodified
FROM
jos_quiz_r_student_quiz
WHERE
jos_quiz_r_student_quiz.c_quiz_id = '".$id_elemento."' AND
jos_quiz_r_student_quiz.c_student_id = '".$id_utente."' AND
jos_quiz_r_student_quiz.c_passed = 1
";

/*
	$query="
			SELECT
				DATE_FORMAT(jos_tt_track.data, '%d/%m/%Y') as timemodified
			FROM
				jos_tt_track
			WHERE
				jos_tt_track.id_elemento = '".$id_elemento."' AND
				jos_tt_track.id_utente = '".$id_utente."' AND
				jos_tt_track.stato = 1
			ORDER BY
				jos_tt_track.data ASC
			LIMIT 1
";
*/

  $db_query = mysql_query($query) or die (mysql_error());
  $riga = mysql_fetch_array($db_query);
  if($riga['timemodified'])
  {
  	return $riga['timemodified'];
  }
  else
  {
  	return false;
  }
  
  
  
  
  
}

function userdata($id_utente)
{
	
	 $query ="SELECT *
			FROM jos_comprofiler
			WHERE id='".$id_utente."'
			LIMIT 1
			";
  
  		$db_query = mysql_query($query) or die (mysql_error());
  
 		$riga = mysql_fetch_array($db_query);
	
 		$userdata->nome=$riga['cb_nome'];
 		$userdata->cognome=$riga['cb_cognome'];
 		$userdata->nominativo = $userdata->nome." ".$userdata->cognome; 
  		$userdata->professione = $riga['cb_professione'];
		$userdata->datanascita = substr($riga['cb_datadinascita'],8,2)."-".substr($riga['cb_datadinascita'],5,2)."-".substr($riga['cb_datadinascita'],0,4);
  		$userdata->luogonascita = $riga['cb_luogodinascita'];
  		
  		
  		//$userdata->datanascita = "--";
  		//$userdata->luogonascita = "--";
  		
  		return $userdata;
 		
 		
}

function coursedata($id_elemento)
{
	
	$id_corso= idelemento_to_idcorso($id_elemento);
		  $query ="SELECT
					*
					FROM jos_tt_corsi
					WHERE id='".$id_corso."'
					LIMIT 1
					";
  
  		$db_query = mysql_query($query) or die (mysql_error());
  
 		$riga = mysql_fetch_array($db_query);
		
 		$course_data->titoloattestato=$riga["titoloattestato"];
 		$course_data->codice_ecm=$riga["codice_ecm"];
 		$course_data->edizione_num=$riga["edizione_nume"];
 		$course_data->datainizio=$riga["datainizio"];
 		$course_data->datafine=$riga["datafine"];
 		$course_data->obbiettivi=$riga["obbiettivi"];
 		$course_data->crediti=$riga["crediti"];
 		$course_data->crediti_testo=$riga["crediti_testo"];
 		$course_data->edizione_num=$riga["edizione_num"]; 		
 		
 		return $course_data;
 		
}

function stampa($user_data, $course_data, $datatest)	
{

  $pdf =& new Cezpdf('a4');
  
  $pdf->ezImage("header.jpg",-3,500,20,'left');
  $pdf->selectFont('./fonts/Helvetica.afm');
  $pdf->ezSetCmMargins(2,2,2,2);
  
  
  //stampo il titolo
  $text="\n <b>Programma nazionale per la formazione continua degli operatori della Sanita'</b> \n \n";
  
  $pdf->ezText($text,12,array('justification'=>'center', 'spacing'=>'1.5'));
  
  
  //stampo l'introduzione
  $text="Premesso che la <b>Commissione Nazionale per la Formazione Continua</b> ha accreditato provvisoriamente il Provider <b>ANMA</b>  accreditamento n. <b>670</b> valido fino al 22/04/2012 Premesso che il Provider ha organizzato l'evento formativo n. <b>$course_data->codice_ecm</b>, edizione n. $course_data->edizione_num denominato \n <b>$course_data->titoloattestato</b> e tenutosi  dal <b>$course_data->datainizio</b> al <b>$course_data->datafine</b>, avente come obiettivi didattico/formativo generali: <i>$course_data->obbiettivi</i>, assegnando all'evento stesso  N.<b> $course_data->crediti</b> ($course_data->crediti_testo) Crediti Formativi E.C.M.";
   
  $pdf->ezText($text,11,array('justification'=>'centre', 'spacing'=>'1.5'));
  
  $text=	"\n \n 
	Il sottoscritto <b>GIUSEPPE BRIATICO</b> \n 
	Rappresentate Legale dell'organizzatore \n
	Verificato l'apprendimento del partecipante \n
	<b> ATTESTA CHE </b> \n
	il Dott./la Dott.ssa \n
	<b> $user_data->nominativo </b> \n
	in qualita' di $user_data->professione \n 
	nato a $user_data->luogonascita \n
	il $user_data->datanascita \n
	ha conseguito \n 
	N. $course_data->crediti ($course_data->crediti_testo) Crediti formativi per l'anno 2011
  ";
  
  $pdf->ezText($text,11,array('justification'=>'centre', 'spacing'=>'1'));
  
  
  //stampo la data
  $text=" Genova, li $datatest \n \n";
  
  $pdf->ezText($text,11,array('justification'=>'left', 'spacing'=>'1.5'));
  
    //stampo la firma
  $text="IL RAPPRESENTANTE LEGALE DELL'ORGANIZZATORE \n Dott. Giuseppe Briatico' \n \n ";
  $pdf->ezText($text,11,array('justification'=>'centre', 'spacing'=>'1'));
  $pdf->ezImage("firma.jpg",-5,90,20,'center');
 
 //echo "non stampo";
 $pdf->ezStream();

}
?>



