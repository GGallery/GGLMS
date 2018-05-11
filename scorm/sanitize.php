<?php


require "FirePHPCore/fb.php";

require "config.php";
dbConnect();

upgrade();



// ------------------------------------------------------------------------------------
// Database-specific code
// ------------------------------------------------------------------------------------

function dbConnect() {

	// database login details
	global $dbname;
	global $dbhost;
	global $dbuser;
	global $dbpass;

	// link
	global $link;

	// connect to the database
	$link = mysql_connect($dbhost,$dbuser,$dbpass);
	mysql_select_db($dbname,$link);

}

function upgrade() {

	global $link;

	$valori= array(0,1,2,2,3,3,3,3,3,3,3,3,3,3,3,3,3,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4);
	$users_id= mysql_query("SELECT DISTINCT UserID FROM #__gg_scormvars WHERE SCOInstanceID = 137 and UserID > 4208 Order by UserId ",$link )or die ("Query fail: " . mysqli_error());
	$new_element= array();

	while($row_user_id = mysql_fetch_assoc($users_id)) {
		$scormelement = mysql_query("SELECT varName, varValue FROM lok9d_gg_scormvars_upgrade",$link )or die ("Query fail: " . mysqli_error());
		echo "<br> -";
		while ($row_scorm_element = mysql_fetch_assoc($scormelement)) {
			$rnd = array_rand($valori, 1);
			$umore = $valori[$rnd];
			switch ($row_scorm_element["varName"]) {

				case 'cmi.interactions.0.student_response':
					$row_scorm_element["varValue"] = get_cmiinteractions0student_response($umore);
					break;

				case 'cmi.interactions.1.student_response':
					$row_scorm_element["varValue"] = get_cmiinteractions1student_response($umore);
					break;

				case 'cmi.interactions.2.student_response':
					$row_scorm_element["varValue"] = get_cmiinteractions2student_response($umore);
					break;

				case 'cmi.interactions.3.student_response':
					$row_scorm_element["varValue"] = get_cmiinteractions3student_response($umore);
					break;

				case 'cmi.interactions.4.student_response':
					$row_scorm_element["varValue"] = get_cmiinteractions4student_response($umore);
					break;


			}

			$row_scorm_element['UserID']= $row_user_id['UserID'];
			$row_scorm_element['SCOInstanceID']= '137';
			$new_element[] = $row_scorm_element;
			echo ".";
		}

	}

	insert_into_db($new_element);
//	print "<pre>";
//	print_r($new_element);
//	print "</pre>";


}

function insert_into_db($new_data){
	global $link;
	foreach ($new_data as $single)
	{

		$query = 'INSERT INTO lok9d_gg_scormvars (SCOInstanceID, UserID, VarName, varValue) VALUES('.$single["SCOInstanceID"].', '.$single["UserID"].', \''.$single["varName"].'\', \''.$single["varValue"].'\') ON DUPLICATE KEY UPDATE varValue=\''.$single["varValue"].'\'';
		echo "§";
		mysql_query($query,$link);
	}
echo "FINE";


}

function get_cmiinteractions0student_response($id){

	$prefisso = 'Come_valuta_la_rilevanza_degli_argomenti_trattati_rispetto_alle_sue_necessit__di_aggiornamento____(1=_non_rilevante;_5=_molto_rilevante)___';
	$risposte = array(
		0=>'Non_rilevante',
		1=>'Poco_rilevante',
		2=>'Mediamente_rilevante',
		3=>'Abbastanza_rilevante',
		4=>'Molto_rilevante'

	);

	return $prefisso.$risposte[$id];
}
function get_cmiinteractions1student_response($id){
	$prefisso = 'Come_valuta_la_qualit__educativa_di_questo_programma_FAD___(1=_insufficiente;_5=_eccellente)___';
	$risposte = array(
		0=>'Insufficiente',
		1=>'Sufficiente',
		2=>'Media',
		3=>'Molto_buona',
		4=>'Eccellente'

	);
	return $prefisso.$risposte[$id];
}
function get_cmiinteractions2student_response($id){

	$prefisso = 'Come_valuta_la_utilit__di_questo_evento_per_la_sua_formazione_aggiornamento___(1=_insufficiente;_5=_molto_utile)___';
	$risposte = array(
		0=>'Insufficiente',
		1=>'Poco_utile',
		2=>'Abbastanza_utile',
		3=>'Utile',
		4=>'Molto_utile'

	);
	return $prefisso.$risposte[$id];
}
function get_cmiinteractions3student_response($id){

	$prefisso = '';
	$risposte = array(
		0=>'Molto_inferiore',
		1=>'Poco_inferiore',
		2=>'Uguale_al_previsto',
		3=>'Poco_superiore',
		4=>'Molto_superiore'

	);
	return $prefisso.$risposte[$id];
}
function get_cmiinteractions4student_response($id){

	$prefisso = 'Questo_programma_FAD___stato_preparato_senza_alcun_supporto.__Ritiene_che_nel_programma_ci_siano_riferimenti__indicazioni_e_o_informazioni_non_equilibrate_o_non_corrette_per_influenza_di_altri_interessi_commerciali____';
	$risposte = array(
		0=>'Pochi',
		1=>'No',
		2=>'No',
		3=>'No',
		4=>'No'

	);
	return $prefisso.$risposte[$id];
}


?>