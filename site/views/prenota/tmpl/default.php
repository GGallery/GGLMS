<?php
// no direct access

defined('_JEXEC') or die('Restricted access');

?>
<h3> Prenota Coupon </h3>

<div class="info-corso">
    <p class="main-info"><strong> TITOLO: </strong> <b> <?= $this->info_corso["titolo_corso"] ?> </b></br>
        <strong> CODICE CORSO: </strong> <b> <?= $this->info_corso["codice_corso"] ?> </b></p>
    <?= $this->info_corso["descrizione_corso"] ?>
</div>

<div class="mc-main">
    <div id="grid"></div>


    <div id="wrapper">
        <form autocomplete="off" id="form-prenota-coupon"
              action="<?php echo('index.php?option=com_gglms&task=prenotacoupon.prenotacoupon'); ?>"
              method="post" name="prenotaCouponForm" id="prenotaCouponForm" class="form-validate">

            <div class="form-group row">
                <label class="col-sm-3 col-form-label" for="yes_no">Sei associato
                    a <?= $this->info_piattaforma["name"] ?> ? *</label>
                <div class="col-sm-9">
                    <input type="radio" name="yes_no" value="false" checked> No </input>
                    <input type="radio" name="yes_no" value="true"> Sì </input>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-3 col-form-label" for="qty">Quanti Coupon? *</label>
                <div class="col-sm-9">
                    <input style="width: 50%" required id="qty" type="number" min="1"
                           placeholder="Inserisci il numero dei coupon per ottenere il prezzo">
                    <!--                    <button  id="btn_calcola"  disabled class="k-primary k-state-disabled" type="button" >Calcola Prezzo</button>-->

                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-3 col-form-label" for="prezzo">Prezzo: </label>
                <div class="col-sm-9">
                    <span id="price"></span>
                </div>
            </div>
            <hr>
            <h5> Dati per la fatturazione </h5>

            <div class="form-group row">
                <label class="col-sm-3 col-form-label" for="username">Partita Iva: *</label>
                <div class="col-sm-9">
                    <input required placeholder="Partita IVA dell'azienda" type="number" class="form-control"
                           id="username"
                           name="username">
                    <small class="validation-lbl" id="piva-msg"> Inserisici la partita iva dell'azienda</small>
                </div>
                <!--                <div class="col-sm-4">-->
                <!--                    <button type="button" id="confirm_piva" class="btn btn-sm">Conferma</button>-->
                <!--                    <button type="button" id="change_piva" class="btn btn-sm"> Reset</button>-->
                <!--                </div>-->

            </div>
            <div class="form-group row">
                <label class="col-sm-3 col-form-label  lbl_company_opt" for="ragioneSociale">Ragione Sociale: *</label>
                <div class="col-sm-9">
                    <input required placeholder="Ragione sociale dell'azienda" type="text"
                           class="form-control company_opt" id="ragione_sociale"
                           name="ragione_sociale">
                    <small class="validation-lbl" id="societa-msg"> Inserisici la ragione sociale dell'azienda</small>
                </div>
            </div>
            <div class="form-group row">
                <label disabled class="col-sm-3 col-form-label  lbl_company_opt" for="email">Email referente
                    Aziendale: *</label>
                <div class="col-sm-9">
                    <input required placeholder="Email del referente aziendale" type="email"
                           class="form-control company_opt" id="email"
                           name="email">

                    <small class="validation-lbl" id="email-msg"> Inserisici l'email del referente aziendale</small>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-3 col-form-label  lbl_company_opt" for="ateco">ATECO: *</label>
                <div class="col-sm-9">
                    <input placeholder="Codice ATECO" type="text" class="form-control company_opt" id="ateco"
                           name="ateco">
                    <small class="validation-lbl" id="ateco-msg"> Inserisici il codice ATECO dell'azienda</small>
                </div>
            </div>

            <ul id="panelbar">
                <li id="li_privacy">Informativa per la Tutela della Privacy *
                    <div style="padding: 10px;">
                        <p>
                            In riferimento al D.lgs. 196/2003 (Codice Privacy), <?= $this->info_piattaforma["name"] ?>
                            ,- titolare del presente trattamento -
                            comunica quanto segue:
                        </p>
                        <ol>
                            <li>
                                i dati da Lei forniti sono utilizzati da incaricati al trattamento per le finalità
                                connesse al servizio fornito e saranno inseriti in una banca dati informatica;
                            </li>
                            <li>
                                il conferimento dei Suoi dati, pur essendo facoltativo, si rende necessario ai fini
                                della corretta regolarizzazione della Sua iscrizione e per l'emissione della relativa
                                fattura;
                            </li>
                            <li> i dati da Lei forniti non saranno diffusi, né trasferiti all'estero, ma verranno
                                comunicati agli incaricati del servizio in oggetto;
                            </li>
                            <li> in qualsiasi momento l'interessato potrà esercitare i diritti di cui all'art. 7 del
                                decreto citato, per chiedere la conferma dell'esistenza dei propri dati personali,
                                chiederne la cancellazione od opporsi al loro utilizzo scrivendo o contattando:
                                <?= $this->info_piattaforma["name"] ?>
                                al tel. <?= $this->info_piattaforma["telefono"] ?> oppure
                                e-mail <?= $this->info_piattaforma["email"] ?>
                            </li>
                        </ol>
                        <label for="privacy" class="radio-inline">Acconsento: </label>
                        <!--                        <label class="radio-inline">-->
                        <!--                            <input type="radio" name="privacy" value="false"  id="privacy"> No </label>-->
                        <label class="radio-inline">
                            <input type="radio" class="radio-required" required name="privacy" value="true"
                                   id="privacy"> Sì </label>
                    </div>
                </li>
                <li id="li_condizioni_generali">Condizioni Generali di Adesione *
                    <div style="padding: 10px;">
                        <ol>
                            <li>
                                <b>Prenotazione</b> <br>
                                La prenotazione viene effettuata direttamente on-line ed è ritenuta valida senza il
                                successivo invio della "Scheda di prenotazione" (è consigliabile stampare la scheda di
                                prenotazione quale vostro promemoria). Una volta confermata l'iscrizione i coupon
                                saranno inviati all'indirizzo e-mail indicato, ma saranno utilizzabili solo dopo la
                                verifica di avvenuto pagamento. I coupon attivati hanno una validità di 60 giorni
                            </li>
                            <li>
                                <b> Caratteristiche del coupon</b> <br>
                                Le caratteristiche dei coupon, incluso il prezzo di vendita, sono riportate sul catalogo
                                on-line <a style="text-decoration: underline;color: #2e6da4;"
                                           href="<?= $this->info_piattaforma["dominio"] ?>"><?= $this->info_piattaforma["dominio"] ?></a>
                                Qualora e nell'ipotesi in cui uno o più coupon ordinati non
                                dovessero essere disponibili, <?= $this->info_piattaforma["name"] ?> si
                                impegnerà a darne tempestiva comunicazione all'acquirente, che potrà decidere se
                                modificare l'ordine o se annullarlo. Nel caso in cui l'acquirente non dovesse fornire
                                una risposta sia in senso positivo sia in senso negativo entro 7 (sette) giorni dalla
                                comunicazione di <?= $this->info_piattaforma["name"] ?> , l'ordine verrà
                                annullato d'ufficio ed eventuali somme riscosse verranno contestualmente restituite.
                            </li>
                            <li>
                                <b>Diritti d'autore</b> <br>
                                Tutti i contenuti dei corsi presenti su <a style="text-decoration: underline;color: #2e6da4;
                                        href="<?= $this->info_piattaforma["dominio"] ?>
                                "><?= $this->info_piattaforma["dominio"] ?></a>
                                sono proprietà letteraria
                                riservata e protetti dal diritto di autore. I proprietari di tali diritti sono i
                                rispettivi autori. Chi intende utilizzare i contenuti deve attenersi alle regole sul
                                diritto d'autore. La fruizione del corso è strettamente riservata all'assegnatario del
                                singolo coupon. La riproduzione, anche parziale, è vietata. Ogni violazione sarà
                                perseguita ai termini di legge.

                            </li>
                        </ol>
                        <label for="condizioni_generali" class="radio-inline">Acconsento: *</label>
                        <!--                        <label class="radio-inline">-->
                        <!--                            <input type="radio" name="condizioni_generali" value="false" > No </label>-->
                        <label class="radio-inline">
                            <input type="radio" class="radio-required" required name="condizioni_generali" value="true">
                            Sì </label>
                    </div>
                </li>
                <li id="li_consenso_informato">Consenso Informato *
                    <div style="padding: 10px;">
                        L'azienda acconsente all'invio di eventuali comunicazioni relative ai servizi e contenuti del
                        sito inerenti con il corso in questione.
                        <br>
                        <label for="consenso_informato" class="radio-inline">Acconsento: * </label>
                        <!--                        <label class="radio-inline">-->
                        <!--                            <input type="radio" name="consenso_informato" value="false" > No </label>-->
                        <label class="radio-inline">
                            <input type="radio" class="radio-required" required name="consenso_informato" value="true">
                            Sì </label>
                    </div>
                </li>
                <li id="li_condizioni_pagamento">Condizioni per il pagamento
                    <div style="padding: 10px;">
                     Il pagamento deve essere effettuato tramite bonifico sulla banca:   <b> <?= $this->info_piattaforma["info_pagamento"] ?></b>
                        <br>

                        <b>CAUSALE OBBLIGATORIA: CODICE DEL CORSO, NUMERO COUPON E SOCIETÀ DI FATTURAZIONE.</b>
                        <br>


                        <?= $this->info_piattaforma["name"] ?>  provvederà ad emettere regolare fattura per
                        l'importo corrispondente. Agli Enti pubblici è richiesto di trasmettere, contestualmente
                        all'invio della scheda di prenotazione, la dichiarazione di esenzione IVA in base all'art.14,
                        comma 10, della Legge 537/1993: in caso di omissione di tale documentazione, non sarà possibile
                        modificare le fatture già emesse.
                    </div>
                </li>
            </ul>
            <div class="form-group" style="margin-top: 20px; text-align: center">
                <button style="width: 80%" id="btn-prenota" type="submit" class="btn-block btn" onclick="prenota">
                    Prenota Coupon
                </button>
            </div>
        </form>

    </div>
</div>


<script type="application/javascript">

    var data = '<?= json_encode($this->prezzi) ?>';
    var piattaforma = '<?= json_encode($this->info_piattaforma) ?>';

    jQuery(document).ready(function () {
        _prenotaCoupon.init(data, piattaforma);
    });

</script>
