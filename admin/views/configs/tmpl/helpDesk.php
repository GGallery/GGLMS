<?php
/**
 * Created by PhpStorm.
 * User: Tony
 * Date: 10/05/2017
 * Time: 17:18
 */

?>
<div class="row-fluid">
    <div class="span6">

        <div class="row-fluid">

            <div class="row-fluid">
                <?php echo $this->form->renderField('mail_riferimento_specifica'); ?>
            </div>
            <div class="row-fluid">
                <?php echo $this->form->renderField('mail_richiesta_tecnica'); ?>
            </div>
            <div class="row-fluid">
                <?php echo $this->form->renderField('mail_richiesta_didattica'); ?>
            </div>
            <div class="row-fluid">
                <?php echo $this->form->renderField('richiesta_privacy'); ?>
            </div>
            <div class="row-fluid">
                <?php echo $this->form->renderField('richiesta_privacy_link'); ?>
            </div>
        </div>
    </div>
</div>

