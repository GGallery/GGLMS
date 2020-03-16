<?php
// no direct access

defined('_JEXEC') or die('Restricted access');


echo "<h3 style='text-align: center; margin-top: 5px'> Report Utenti </h3>"; ?>

<div id="splitter" class="mc-main">
    <div id="filters-pane">
        <h5 style="text-align: center">
            Filtri
        </h5>
        <form id="theform" class="form-inline" action="index.php">


            <div class="form-group">
                <label for="corso_id">Corso:</label><br>
                <select id="corso_id" name="corso_id" class="refresh"> </select>

            </div>


            <div class="form-group">
                <label for="tipo_report">Tipo Report:</label><br>
                <select id="tipo_report" name="tipo_report" class="refresh"> </select>
            </div>

            <hr>
<!--            <h5 style="text-align: center">Filtri</h5>-->

            <div class="form-group">
                <label for="usergroups">Azienda:</label><br>
                <select id="usergroups" name="usergroups" class="refresh">

                </select>


            </div>

            <div class="form-group" id="searchPhrase_div">
                <label for="searchPhrase">Cerca:</label><br>
                <input  style="width: 90%"  type="text" id="searchPhrase">
            </div>

            <div class="form-group" id="filterstatodiv">
                <label for="filterstato">Stato corso</label>
                <select id="filterstato" name="filterstato" class="refresh">
                </select>
            </div>

            <div class="form-group" id="calendar_startdate_div" style="display:none;">
                <label for="startdate">Completato dal:</label><br>
                <input style="width: 90%" id="startdate" name="startdate"/>

            </div>

            <div class="form-group" id="calendar_finishdate_div" style="display: none;">
                <label for="finishdate">Completato al:</label><br>
                <input  style="width: 90%" id="finishdate" name="finishdate"/>
            </div>

            <!---->
            <!--            <input type="hidden" id="option" name="option" value="com_gglms">-->
            <!--            <input type="hidden" id="task" name="task" value="api.get_csv">-->
            <!---->
                        <div class="form-group">
                            <button type="button" id="update" class="width100 btn" onclick="loadData()">AGGIORNA DATI</button>
<!--                            <button type="button" id="get_csv" class="btn width100" onclick="loadCsv()">SCARICA REPORT CSV</button>-->
                        </div>
            <!--
                        <div class="form-group">
                            <button type="button" id="get_csv" class="btn btn-warning btn-lg width100" onclick="sendAllMail()">INVIA MAIL IN SCADENZA</button>
                        </div>

                        <div>
                            <button type="button" class="btn btn-info btn-lg width100" onclick="dataSyncUsers()">SINCRONIZZA TABELLA REPORT</button>
                        </div>
                -->
            <!--        <div class="form-group">-->
            <!--        -->
            <!--        </div>-->
        </form>


    </div>
    <div id="gird-pane">
        <h5 style="text-align: center">
            Dati
        </h5>
        <div id="grid"></div>

    </div>

</div>

<div id="cover-spin"></div>m
<div id="user-details">
    <div id="user_grid"></div>
</div>



<script type="application/javascript">
    jQuery(document).ready(function () {
        _reportkendo.init();
    });

</script>
