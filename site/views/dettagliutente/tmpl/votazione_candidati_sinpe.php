
<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-4">
    <?php foreach($this->details_users as $user_id){
        ?>

      <div class=" col-sm-3 py-3  mb-4 d-flex">
        <div class="card">
          <img src="https://mdbcdn.b-cdn.net/img/new/standard/city/041.webp" class="card-img-top"
            alt="Hollywood Sign on The Hill" />
          <div class="card-body">
            <h5 class="card-title"><?php echo $user_id['nome_utente'] . ' '. $user_id['cognome_utente']; ?></h5>
            <p class="card-text">
    This is a longer card with supporting text below as a natural lead-in to
              additional content. This content is a little bit longer.
                <br>
                <button class="btn btn-primary btn-lg"
                        id="btn_candidato" data-valore="<?php echo $user_id['user_id']; ?>"
                        onclick="store('<?php echo $user_id['user_id']; ?>')">Vota
                </button>
                <br>
            </p>
          </div>
        </div>
      </div>

       <?php
    }
    echo "</div>";
    ?>

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

</script>
