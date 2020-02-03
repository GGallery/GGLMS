<?php
// no direct access

defined('_JEXEC') or die('Restricted access');


echo "<h1> Monitora Coupon</h1>"; ?>

<div class="mc-main">
    <div id="filtri" class="filtri">
        <h5>Filtri</h5>
<!--        <hr>-->
        <form id="form-monitora-coupon" name="form-monitora-coupon" class="form-validate">
            <div class="form-group">
                <label for="id_gruppo_azienda">Azienda:</label>
                <select placeholder="Azienda" class="form-control" id="id_gruppo_azienda" name="id_gruppo_azienda">
                    <?php foreach ($this->societa as $s) { ?>
                        <option value="<?php echo $s->id; ?>">
                            <?php echo $s->title ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <label for="id_gruppo_corso">Corso:</label>
                <select placeholder="Corso" class="form-control" id="id_gruppo_corso" name="id_gruppo_corso">
                    <option value="-1">Tutti i corsi</option>
                    <?php foreach ($this->lista_corsi as $s) { ?>
                        <option value="<?php echo $s->value; ?>">
                            <?php echo $s->text ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group">
                <label for="stato_coupon">Stato:</label>
                <select placeholder="Stato" class="form-control" id="stato_coupon" name="stato_coupon">
                    <option value="-1">Qualsiasi stato</option>
                    <option value="0">Liberi</option>
                    <option value="1">Assegnati</option>
                    <option value="2">Scaduti</option>
                </select>
            </div>

            <div class="form-group">
                <label for="coupon">Cerca Coupon:</label>
                <input placeholder="Cerca Coupon" class="" type="text" id="coupon" name="coupon">
            </div>

            <div class="form-group">
                <label for="venditore">Cerca Vendtore:</label>
                <input placeholder="Cerca Vendtore" class="" type="text" id="venditore" name="venditore">
            </div>

            <div class="form-group">
                <label for="utente">Cerca Utente:</label>
                <input placeholder="Cerca Utente" class="" type="text" id="utente" name="utente">
            </div>

            <button type="button" id="btn_export_csv" class="btn btn-primary">Esporta in CSV</button>
        </form>
    </div>
    <div class="data">
        <h5>Coupon</h5>
<!--        <hr>-->
        <div class="table-container">

            <span id="no-data-msg" >Non ci sono coupon per i filtri selezionati</span>
            <table id="coupon-table" class="table table-striped table-bordered" style="width:100%">
                <thead>
                <tr class="header-row">
                </tr>
                </thead>
                <tbody>
                </tbody>

            </table>

            <div id="pagination-container">
                <ul class="pagination">
                    <li class="first" aria-disabled="true">
                        <a data-page="first" class="button button-page">«</a></li>
                    <li class="prev" aria-disabled="true">
                        <a data-page="prev" class="button button-page">&lt;</a></li>
                    <li class="page-1" aria-disabled="false" aria-selected="false">
                        <a data-page="1" class="button button-page">1</a></li>
                    <li class="page-2" aria-disabled="false" aria-selected="false">
                        <a data-page="2" class="button button-page">2</a></li>
                    <li class="page-3" aria-disabled="false" aria-selected="false">
                        <a data-page="3" class="button button-page">3</a></li>
                    <li class="page-4" aria-disabled="false" aria-selected="false">
                        <a data-page="4" class="button button-page">4</a></li>
                    <li class="page-5" aria-disabled="false" aria-selected="false">
                        <a data-page="5" class="button button-page">5</a></li>
                    <li class="next" aria-disabled="false ">
                        <a data-page="next" class="button button-page">&gt;</a></li>
                    <li class="last" aria-disabled="false">
                        <a data-page="last" class="button button-page">»</a></li>
                    <li class="last" aria-disabled="false">
                        <span id="totalcount"></span></li>
                </ul>

            </div>
        </div>

    </div>

</div>
<div id="cover-spin"></div>

<script type="application/javascript">
    jQuery(document).ready(function () {
        _monitoraCoupon.init();
    });

</script>
