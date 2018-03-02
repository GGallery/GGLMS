<div class="alert alert-warning">
    <span class="icon-info-2" style="position: relative; float: left; font-size: 40px; margin-right: 35px; margin-top: 11px;">  </span>
    <h4 class="alert-heading">COMPILAZIONE ATTESTATI con CommunityBuilder/EventBooking</h4>
    <div class="alert-message">
        Nel compilare il file <b>.tpl</b> del template puoi utilizzare i campi come segue:<br><br>

        <b><i>{$data.nome}</i></b>, <b><i>{$data.cognome}</i></b>
        <br> CB: puoi utilizzare queste variabiliti SOLO se hai usufrito dei campi firstname/lastname già presenti nei campi disponibili.
        <br> EB: puoi utilizzare queste variabiliti SOLO se hai usufrito dei campi first_name/last_name già presenti nei campi disponibili.
        <br> Se non hai adottato questi campi, potrai richiamare nome e conogme come ogni altro campo aggiuntivo.<br><br>

        <b><i>{$data.altro_campo}</i></b>
        <br> CB: puoi accedere ad ogni campo digitanto direttamente il nome del campo dopo data.
        <br> EB: qui il discorso è un po' più complesso. Per una serie di campi puoi accedere come sopra mentre per tutti quelli aggiuntivi dovrai accedere tramita la variabile field.
        <br> i campi accessibili come {$data.nome_campo} sono: <i> first_name, last_name, organization, address, address2, city, state, country, zip, phone, fax, email </i>, ogni nuovo campo che hai creato sarà invece accessibile traminte {$data<b>.fields.</b>altro_campo}

        <br><br><b><i>{$data.content_path}/nomeimmagine.png</i></b> Inserisci le immagini utilizzando la dicitura come in esempio e posizionando l'immagine all'interno della cartella dell'attestato

        <br><br><b><i>{$data.data_superamento}</i></b>
        <br> Data di superamento del contenuto di riferimento impostato nel contenuto di tipo attestato

        <br><br><b><i>{$data|@var_dump}</i></b><br>Inserisci la dicitura  nel template per visualizzare tutte le varibili disponibili.

    </div>
</div>

<div class="row-fluid">

   <div>
        <?php echo $this->form->renderField('testo_invito_scaricare_attestato'); ?>
   </div>

</div>

