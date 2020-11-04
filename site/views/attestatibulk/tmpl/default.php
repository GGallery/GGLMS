<?php
// no direct access

defined('_JEXEC') or die('Restricted access');

?>


<h1><?php echo  JText::_('COM_GGLMS_ATTESTATI_BULK_TITOLO') ?></h1>
<div>
    <form autocomplete="off" id="form-genera-coupon"
          method="post" name="downloadAttForm" id="dwlForm" style="padding: 20px 100px;" class="form-validate">
        <div class="form-group row">
            <label class="col-sm-2" for="id_corso"><?php echo  JText::_('COM_GGLMS_GLOBAL_CORSO') ?>:</label>
            <div class="col-sm-10">
                <select required placeholder="Corso" type="text" class="form-control cpn_opt"
                        id="id_corso" name="id_corso">
                    <option value="-1">
                        <?php echo  JText::_('COM_GGLMS_GLOBAL_SCEGLI_CORSO') ?>
                    </option>
                    <?php foreach ($this->lista_corsi as $c) { ?>
                        <option value="<?php echo $c->value; ?>">
                            <?php echo $c->text ?>
                        </option>
                    <?php } ?>

                </select>
            </div>
        </div>
        <?php
        // integrazione scelta azienda nello scaricamento degli attestati
        if (isset($this->lista_azienda)) :
            $_selected = "";
            $_default = "";
            if (count($this->lista_azienda) == 1)
                $_selected = "selected";
            else {
                $_option_label = JText::_('COM_GGLMS_GLOBAL_SCEGLI_AZIENDA');
                $_default = <<<HTML
                <option value="">{$_option_label}</option>
HTML;
            }

            ?>
            <div class="form-group row">
                <label class="col-sm-2" for="id_azienda"><?php echo  JText::_('COM_GGLMS_GLOBAL_COMPANY') ?>:</label>
                <div class="col-sm-10">
                    <select required placeholder="Azienda" type="text" class="form-control cpn_opt"
                            id="id_azienda" name="id_azienda">
                        <?php echo $_default; ?>
                        <?php foreach ($this->lista_azienda as $key => $az) { ?>
                            <option value="<?php echo $az['id_gruppo']; ?>" <?php echo $_selected; ?>>
                                <?php echo $az['azienda']; ?>
                            </option>
                        <?php } ?>

                    </select>
                </div>
            </div>
        <?php endif; ?>
        <div class="form-group row" id="calendar_startdate_div">
            <label class="col-sm-2" for="startdate"><?php echo  JText::_('COM_GGLMS_REPORT_COMPLETATI_GEN') ?>:*</label>
            <!--
            <div class="col-sm-4">
                <input type="date" id="startdate" name="startdate"  min="" />
            </div>
            <div class="col-sm-4">
                <input type="date" id="enddate" name="enddate"  min="" />
            </div>
            -->
            <div class="form-group col-sm-4">
                <label for="startdate"><?php echo  JText::_('COM_GGLMS_REPORT_COMPLETATI_FROM_SHORT') ?></label>
                <input type="date" class="form-control" id="startdate" min="" />
            </div>
            <div class="form-group col-sm-4">
                <label for="enddate"><?php echo  JText::_('COM_GGLMS_REPORT_COMPLETATI_TO_SHORT') ?></label>
                <input type="date" class="form-control" id="enddate" min="" />
            </div>
        </div>

        <div class="form-group row">
            <div class="col-sm-2">
                &nbsp;
            </div>
            <div class="col-sm-6">
                <small>*<?php echo  JText::_('COM_GGLMS_REPORT_COMPLETATI_ADV') ?></small>
            </div>
        </div>

        <?php /*
        <div class="form-group row" id="calendar_startdate_div">
            <label class="col-sm-2" for="enddate"><?php echo  JText::_('COM_GGLMS_REPORT_COMPLETATI_TO') ?></label>
            <div class="col-sm-10">
                <input type="date" id="enddate" name="enddate"  min="" >
            </div>
        </div>*/?>

        <div class="form-group row" id="calendar_startdate_div">
            <label class="col-sm-2" for="enddate"><?php echo  JText::_('COM_GGLMS_REPORT_SALVA_CON_NOME') ?></label>
            <div class="col-sm-10">
                <textarea id="salva_come" name="salva_come" cols="8" rows="5" placeholder="<?php echo  JText::_('COM_GGLMS_REPORT_SALVA_CON_NOME_PLC') ?>"></textarea><br />
                <p><code>Default: attestato_IDATTESTATO_COGNOME_NUMERORANDOM</code></p>
                <p><?php echo  JText::_('COM_GGLMS_REPORT_SALVA_CON_NOME_LGD') ?></p>
                <p><code>"nome", "cognome", "codice_fiscale", "codice_corso", "data_inizio_corso", "data_fine_corso"</code></p>
            </div>
        </div>

        <div class="form-group row">
            <span id="msg" class="alert alert-danger" style="display: none; width: 100%"></span>
        </div>

        <div class="form-group" style="text-align: center">
            <a id="btn-download" style="padding: 2px 32px;" type="button" target="_blank" href=""
               class="btn btn-lg disabled"><?php echo  JText::_('COM_GGLMS_GLOBAL_DOWNLOAD') ?></a>
        </div>
    </form>


</div>

<style>
    #salva_come {
        margin-top: 2em;
        height: 75px;
        width: 90%;
        border: 1px solid #000;
    }
</style>

<script type="application/javascript">
    // Helper functions
    function split(val) {
        return val.split( /,\s*/ );
    }

    function extractLast(term) {
        return split(term).pop();
    }

    jQuery(document).ready(function () {

        _scaricaattesati.init();

        var categoryTags = [
                            "nome",
                            "cognome",
                            "codice_fiscale",
                            "codice_corso",
                            "data_inizio_corso",
                            "data_fine_corso"
                            ];
        // State variable to keep track of which category we are in
        tagState = categoryTags;

        jQuery("#salva_come")

        // Create the autocomplete box
            .autocomplete({
                minLength : 0,
                autoFocus : true,
                source : function(request, response) {
                    // Use only the last entry from the textarea (exclude previous matches)
                    lastEntry = extractLast(request.term);

                    var filteredArray = jQuery.map(tagState, function(item) {
                        if (item.indexOf(lastEntry) === 0) {
                            return item;
                        } else {
                            return null;
                        }
                    });

                    // delegate back to autocomplete, but extract the last term
                    response(jQuery.ui.autocomplete.filter(filteredArray, lastEntry));
                },
                focus : function() {
                    // prevent value inserted on focus
                    return false;
                },
                select : function(event, ui) {
                    var terms = split(this.value);
                    // remove the current input
                    terms.pop();
                    // add the selected item
                    terms.push(ui.item.value);
                    // add placeholder to get the comma-and-space at the end
                    terms.push("");
                    this.value = terms.join(", ");
                    return false;
                }
            }).on("keydown", function(event) {
            // don't navigate away from the field on tab when selecting an item
            if (event.keyCode === jQuery.ui.keyCode.TAB /** && $(this).data("ui-autocomplete").menu.active **/) {
                event.preventDefault();
                return;
            }

        });

    });


</script>
