<?php

if ($this->_html != "") {
    echo $this->_html;
    return;
}
?>

<div class="container">
    <div class="border mt-3 pl-3 pt-2 shadow-sm rounded-1">
        <!-- Presidente -->
        <h4 class="border-bottom">
            <b>PRESIDENTE</b>
        </h4>
        
        <div class="d-flex flex-column mb-3">
        <?php foreach($this->details_users[1] as $user_id){
            ?>
            
                <div class="d-flex flex-row mb-3 align-items-center">
                    <div class="p-2"><input 
                            type="radio" 
                            name="pres_vote" 
                            id="pres_vote_<?php echo $user_id['id']; ?>" 
                            value="<?php echo $user_id['id']; ?>" /></div>
                    <div class="p-2 text-uppercase font-weight-bold"><?php echo $user_id['nome'] . ' '. $user_id['cognome']; ?></div>
            
                </div>
            

        <?php
        } ?>
        </div>
    </div>

    <div class="border mt-3 pl-3 pt-2 shadow-sm rounded-1">
        <!-- Consigliere -->
        <h4 class="border-bottom">
            <b>CONSIGLIERI</b> <span class="fs-6">
        </h4>
        *Puoi selezionare fino a <?php echo $this->votingLimit; ?> <?php echo $this->votingLimit > 1 ? 'candidati' : 'candidato' ?> </span>

            <div class="d-flex flex-column mb-3">
            <?php foreach($this->details_users[2] as $user_id){
                ?>

                <div class="d-flex flex-row mb-3 align-items-center">
                    <div class="p-2"><input 
                                class="consVotes"
                                type="checkbox" 
                                name="cons_vote" 
                                id="cons_vote_<?php echo $user_id['id']; ?>" 
                                value="<?php echo $user_id['id']; ?>" /></div>
                    <div class="p-2 text-uppercase font-weight-bold"><?php echo $user_id['nome'] . ' '. $user_id['cognome']; ?></div>
            
                </div>

            <?php
            } ?>
            </div>

    </div>

    <div class="d-flex flex-row mb-3 justify-content-center mt-3">
        <button 
            id="sendVoteBtn"
            type="button" 
            class="btn btn-primary btn-lg">INVIA VOTO</button>
    </div>

    <input type="hidden" id="uid" value="<?php echo $this->user_id; ?>" />
    <input type="hidden" id="codv" value="<?php echo $this->codice['codice']; ?>" />
    <input type="hidden" id="redir" value="<?php echo JURI::root(); ?>" />
</div>

<style type="text/css">
    @media (max-width: 576px) {
        .xs {color:red;font-weight:bold;}
    }

    /* Small devices (landscape phones, 576px and up) */
    @media (min-width: 576px) and (max-width:768px) {
        .sm {color:red;font-weight:bold;}
    }

    /* Medium devices (tablets, 768px and up) The navbar toggle appears at this breakpoint */
    @media (min-width: 768px) and (max-width:992px) {
        .md {color:red;font-weight:bold;}
    }

    /* Large devices (desktops, 992px and up) */
    @media (min-width: 992px) and (max-width:1200px) {
        .lg {color:red;font-weight:bold;}
    }

    /* Extra large devices (large desktops, 1200px and up) */
    @media (min-width: 1200px) {
        .xl {color:red;font-weight:bold;}
    }

    @media (max-width: 768px) {
        .btn-lg {
            padding: 10px 20px; /* Modifica le dimensioni su schermi più piccoli */
            font-size: 16px; /* Modifica la dimensione del testo su schermi più piccoli */
        }
    }
    @media (min-width: 1200px) {
        .btn-lg {padding: 10px 20px;
            font-size: 18px;
        }
    }

</style>

<script type="text/javascript">

    document.addEventListener('DOMContentLoaded', function () {
        const checkboxes = document.querySelectorAll('.consVotes');
        const maxChecked = <?php echo $this->votingLimit;?>; // Imposta il numero massimo di checkbox selezionabili

        checkboxes.forEach(function (checkbox) {
            checkbox.addEventListener('change', updateCount);
        });

        function updateCount() {
            const selectedCheckboxes = document.querySelectorAll('.consVotes:checked');
            const count = selectedCheckboxes.length;

            if (count > maxChecked) {
                // Hai superato il numero massimo di checkbox selezionabili
                alert('Puoi selezionare al massimo ' + maxChecked + ' checkbox.');
                this.checked = false; // Impedisci la selezione della checkbox
            }
            
        }
    });

    document.getElementById("sendVoteBtn").addEventListener('click', function (e) {

        e.preventDefault();
        const userId = document.getElementById("uid").value;
        const codVoto = document.getElementById("codv").value;
        const uRedir = document.getElementById("redir").value;

        if (userId == "" ) {
            customAlertifyAlertSimple("Identificativo utente non disponibile, impossibile continuare");
            return;
        }

        if (codVoto == "") {
            customAlertifyAlertSimple("Riferimento al codice voto non disponibile, impossibile continuare");
            return;
        }

        if (uRedir == "") {
            customAlertifyAlertSimple("Non è presente nessun indirizzo di redirect, impossibile continuare");
            return;
        }

        const selectedPresVote = getSelectedRadioButton("pres_vote");
        /*
        if (selectedPresVote == null) {
            customAlertifyAlertSimple("Per inviare il tuo voto devi almeno votare il presidente!");
            return;
        }
        console.log('selectedPresVote', selectedPresVote);
        */

        const selectedConsVotes = getSelectedCheckboxValues("cons_vote");
        //console.log('selectedConsVotes', selectedConsVotes);

        const postData = {
            "voto_presidente": (selectedPresVote != null ? selectedPresVote.value : null),
            "voti_consiglieri": selectedConsVotes,
            "user_id": userId,
            "codice_votazione": codVoto,
        };

        jQuery.get("index.php?option=com_gglms&task=api.store_votazione_candidati", postData,
            function (data) {

                if (data.valido) {

                    console.log("OK!");
                    customAlertifyAlertSimple("Votazione stata inserita con successo");
                    setTimeout(function(){
                        window.location.href = "<?php echo JURI::root(); ?>";
                    }, 5000);

                    return;
                } else {

                    customAlertifyAlertSimple(data.error);
                    return;
                }

        }, 'json');

    });


    function store(id_candidato) {

        var id_user = "<?php echo $this->user_id; ?>";
        var codice = "<?php echo $this->codice['codice']; ?>";

        jQuery.get("index.php?option=com_gglms&task=api.store_votazione_candidati", {id_candidato: id_candidato,user_id: id_user,codice: codice},
            function (data) {

                if (data.valido) {

                    console.log("OK!");
                    customAlertifyAlertSimple("Votazione stata inserita con successo");
                    setTimeout(function(){
                        window.location.href = "<?php echo JURI::root(); ?>";
                    }, 5000);

                    return;
                } else {

                    customAlertifyAlertSimple(data.error);
                    return;
                }

        }, 'json');

    }

    function customAlertifyAlertSimple(pMsg) {
        alertify.alert()
            .setting({
                'title': 'Attenzione!',
                'label': 'OK',
                'message': pMsg
            }).show();
    }

    function getSelectedRadioButton(groupName) {
        const radioButtons = document.getElementsByName(groupName);

        for (var i = 0; i < radioButtons.length; i++) {
            if (radioButtons[i].checked) {
                return radioButtons[i];
            }
        }

        return null; // Nessun radio button selezionato
    }

    function getSelectedCheckboxValues(groupName) {
        const checkboxes = document.getElementsByName(groupName);
        let selectedValues = [];

        for (var i = 0; i < checkboxes.length; i++) {
            if (checkboxes[i].checked) {
                selectedValues.push(checkboxes[i].value);
            }
        }

        return selectedValues;
    }

</script>
