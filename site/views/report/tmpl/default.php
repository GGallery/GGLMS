<?php
// no direct access

defined('_JEXEC') or die('Restricted access');

$header = $this->report[0];
$rows = array_slice($this->report, 1);


?>




<table class='table table-striped'>
    <thead>
    <th>Utenti</th>
    <?php
    foreach ($header as $item){
        echo "<th>$item->titolo </th>";
    }
    ?>
    <thead>

    <?php
    foreach($rows as $row){
        echo "<tr>";
        echo "<td>".$row[0]->first_name." ".$row[0]->last_name."</td>";

        for($i=1; $i<sizeof($row); $i++){
            echo "<td>";

//            print_r($row[$i]);
            if($row[1]->completato)
                echo "<span class='icon-publish'> </span> (".$row[$i]->data.")";
            else
                echo "<span class='icon-delete'> </span>";
            echo "</td>";
 
        }


    }
    ?>





</table>








