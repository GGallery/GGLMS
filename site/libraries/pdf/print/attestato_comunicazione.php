<?php

include 'class.ezpdf.php';
include 'db_connection.php';
  

$scoid="34";

$cf=base64_decode($_REQUEST['key']);

stampa($cf,$scoid);



function check_data($_scoid, $_id_utente){
$query= "SELECT
jos_lms_quiz_r_student_quiz.c_date_time,
DATE_FORMAT(jos_lms_quiz_r_student_quiz.c_date_time, '%d/%m/%Y') as timemodified
FROM
jos_lms_quiz_r_student_quiz
WHERE
jos_lms_quiz_r_student_quiz.c_quiz_id = '".$_scoid."' AND
jos_lms_quiz_r_student_quiz.c_student_id = '".$_id_utente."' AND
jos_lms_quiz_r_student_quiz.c_passed = 1
";

  $db_query = mysql_query($query) or die (mysql_error());
  $riga = mysql_fetch_array($db_query);
//echo   $riga['c_date_time'];
  if($riga['timemodified'])
  {
  	return $riga['timemodified'];
  }
  else
  {
  	return false;
  }
  
  
  
  
  
}
	
function stampa($cf, $scoid)	
{
	
	
  $query ="SELECT *
			FROM jos_comprofiler_2011
			WHERE cb_codicefiscale='".$cf."'
			LIMIT 1
			";
  
     
  
  $db_query = mysql_query($query) or die (mysql_error());
  
  $riga = mysql_fetch_array($db_query);
  //$riga=$riga[0];
  
  
  $firstname=$riga['firstname'];
  $lastname=$riga['lastname'];
  $nome= $lastname." ".$firstname;
  $professione = $riga['cb_professionedisciplina'];
  
  $datanascita=substr($riga['cb_datadinascita'],8,2)."-".substr($riga['cb_datadinascita'],5,2)."-".substr($riga['cb_datadinascita'],0,4);
  $luogonascita=$riga['cb_luogodinascita'];

  $pdf =& new Cezpdf('a4');
  
  $pdf->ezImage("header.jpg",-3,500,20,'left');
  $pdf->selectFont('./fonts/Helvetica.afm');
  $pdf->ezSetCmMargins(2,2,2,2);
  
  
  //stampo il titolo
  $text="\n <b>Programma nazionale per la formazione continua degli operatori della Sanita'</b> \n \n";
  
  $pdf->ezText($text,12,array('justification'=>'center', 'spacing'=>'1.5'));
  
  
  //stampo l'introduzione
  $text="Premesso che la <b>Commissione Nazionale per la Formazione Continua</b> ha accreditato provvisoriamente il Provider <b>GGALLERY SRL</b>  accreditamento n. <b>39</b> valido fino al 22/04/2012 Premesso che il Provider ha organizzato l'evento formativo n. <b>39-1012</b>, edizione n. 0 denominato \n <b>LA COMUNICAZIONE EFFICACE IN FARMACIA PER UN  CORRETTO APPROCCIO RELAZIONALE CON IL PAZIENTE</b> e tenutosi  dal <b>15/01/2011</b> al <b>31/12/2011</b>, avente come obiettivi didattico/formativo generali: <i>ASPETTI RELAZIONALI (COMUNICAZIONE INTERNA,  ESTERNA, CON PAZIENTE) E UMANIZZAZIONE CURE</i>, assegnando all'evento stesso  N.<b> 10</b> (dieci) Crediti Formativi E.C.M.";
   
  $pdf->ezText($text,11,array('justification'=>'centre', 'spacing'=>'1.5'));
  
  

  
  
  $text=	"\n \n 
	Il sottoscritto <b>PAOLO MACRI'</b> \n 
	Rappresentate Legale dell'organizzatore \n
	Verificato l'apprendimento del partecipante \n
	<b> ATTESTA CHE </b> \n
	il Dott./la Dott.ssa \n
	<b> $nome </b> \n
	in qualita' di $professione \n 
	nato a $luogonascita \n
	il $datanascita \n
	ha conseguito \n 
	N. 10 (dieci) Crediti formativi per l'anno 2011
	
  ";
  
  $pdf->ezText($text,11,array('justification'=>'centre', 'spacing'=>'1'));
  
  
  //stampo la data
  $data=check_data($scoid, $idutente);
  $text=" Genova, li $data \n \n";
  
  $pdf->ezText($text,11,array('justification'=>'left', 'spacing'=>'1.5'));
  
    //stampo la firma
  $text="IL RAPPRESENTANTE LEGALE DELL'ORGANIZZATORE \n Dott. Paolo Macri' \n \n ";
  $pdf->ezText($text,11,array('justification'=>'centre', 'spacing'=>'1'));
  $pdf->ezImage("firma.jpg",-5,90,20,'center');
  
//  echo "non stampo";
 $pdf->ezStream();

}
?>



