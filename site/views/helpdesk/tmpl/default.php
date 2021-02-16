<?php
// no direct access

defined('_JEXEC') or die('Restricted access');


echo "<h1 class='hd-title'> Help Desk " . $this->info_piattaforma->alias . "</h1>";

?>

<div class="help-desk-container">
    <h4 class="hd-title"><?php echo  JText::_('COM_GGLMS_HELP_DESK_TITLE') ?> <?php echo $this->info_piattaforma->name ?> </h4>
    <div id="info_associazione" class="container-fluid">

        <div class="row">
            <div class="col-sm-12">
                <div> <span title='telefono' class='glyphicon glyphicon-phone-alt info-icon'

                    ></span> <?php echo $this->info_piattaforma->telefono ?></div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div> <span title='email' class='glyphicon glyphicon-envelope info-icon'
                    ></span> <b><a
                                href="<?php echo $this->info_piattaforma->email_riferimento ?>"><?php echo $this->info_piattaforma->email_riferimento ?></a></b>
                </div>
            </div>
        </div>
        <div class="<?php echo empty($this->info_piattaforma->link_ecommerce) ? 'row hidden' : 'row' ?> ">
            <div class="col-sm-12">
                <div>

                    <span title='email' class='glyphicon glyphicon-shopping-cart info-icon'

                    ></span> <b> <a href="<?php echo $this->info_piattaforma->link_ecommerce ?>"><?php echo  JText::_('COM_GGLMS_HELP_DESK_ECOMMERCE_CATALOGO') ?></a></b>
                </div>
            </div>
        </div>


    </div>
    <hr>
    <h4 class="hd-title"><?php echo  JText::_('COM_GGLMS_HELP_DESK_FORM_DESCRIPTION') ?></h4>

    <form action="<?php echo JRoute::_('index.php?option=com_gglms&task=helpDesk.sendMailRequest.php'); ?>"
          method="post" name="contactForm" id="contactForm" class="form-validate">
        <div class="form-group row">
            <label class="col-sm-3 col-form-label" for="type"> <?php echo  JText::_('COM_GGLMS_HELP_DESK_REQUEST_TYPE') ?> :</label>
            <div class="col-sm-9">
                <fieldset class="">
                    <input type="radio" class="hd" name="request_type"  value="tecnica" checked="checked"> <?php echo  JText::_('COM_GGLMS_HELP_DESK_REQUEST_TYPE_TECH') ?>
                    <input type="radio" class="hd" name="request_type"   value="didattica" > <?php echo  JText::_('COM_GGLMS_HELP_DESK_REQUEST_TYPE_DID') ?>  <br>
                </fieldset>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-3 col-form-label" for="nominativo"><?php echo  JText::_('COM_GGLMS_HELP_DESK_NAME') ?> :</label>
            <div class="col-sm-9">
                <input placeholder="<?php echo  JText::_('COM_GGLMS_HELP_DESK_NAME') ?>" type="text" class="form-control" id="nominativo" name="nominativo" required>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-3 col-form-label" for="email"><?php echo  JText::_('COM_GGLMS_GLOBAL_EMAIL') ?> :</label>
            <div class="col-sm-9">
                <input placeholder="Email" type="email" class="form-control" id="email"
                       name="email" required>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-3 col-form-label" for="question"><?php echo  JText::_('COM_GGLMS_HELP_DESK_QUESTION') ?> :</label>
            <div class="col-sm-9">
                    <textarea placeholder="<?php echo  JText::_('COM_GGLMS_HELP_DESK_QUESTION_PH') ?> " class="form-control" id="question" required
                              name="question" rows="5"></textarea>
            </div>
        </div>

        <?php if ($this->richiesta_privacy == 1) : ?>
            <div class="form-group row">
                <div class="col-sm-12">
                    <input type="checkbox" value="1" id="accetta_privacy" name="accetta_privacy" /><?php echo $this->richiesta_privacy_link; ?>
                </div>
            </div>

            <hr />
        <?php endif; ?>

        <div class="form-group">
            <button id="btn_invia" type="submit" class="btn-block btn"><?php echo  JText::_('COM_GGLMS_GLOBAL_SEND') ?></button>
        </div>
        <input type="hidden" name="alias" value="<?php echo $this->info_piattaforma->alias?>"/>
    </form>
</div>

<?php if ($this->richiesta_privacy == 1) : ?>

<script type="text/javascript">

    jQuery('#contactForm').on('submit', function (e) {
        e.preventDefault();
        var pChecked = jQuery('#accetta_privacy').attr('checked');

        if (pChecked != "checked")
            alert(Joomla.JText._('COM_GGLMS_HELP_DESK_PRIVACY'));
        else
            jQuery('#contactForm')[0].submit();
    })

</script>

<?php endif; ?>

