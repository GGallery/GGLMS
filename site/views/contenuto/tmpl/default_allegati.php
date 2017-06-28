<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

if($this->contenuto->_params->get('abilita_breadcrumbs', 1))
    echo $this->loadTemplate('breadcrumb');

$files= $this->contenuto->getFiles();
?>

<div id="gglmsheader" >
</div>

<div id="files" class="g-grid ">

    <ul>
        <?php
        foreach($files as $file){
            echo "<li>";
            echo '<a target="_blank" href="/mediagg/files/'.$file->id.'/'.$file->filename.'">'.$file->name.'</a>';
            echo "</li>";
        }
        ?>
    </ul>
</div>
