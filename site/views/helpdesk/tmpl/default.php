<?php
// no direct access

defined('_JEXEC') or die('Restricted access');


echo "<h1 class='hd-title'> Help Desk " . $this->info_piattaforma->alias . "</h1>";

?>

<div class="help-desk-container">
    <h4 class="hd-title">Per informazioni o per l'acquisto rivolgersi
        a <?php echo $this->info_piattaforma->name ?> </h4>
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

                    ></span> <b> <a href="<?php echo $this->info_piattaforma->link_ecommerce ?>">catalogo e-commerce</a></b>
                </div>
            </div>
        </div>


    </div>
    <hr>
    <h4 class="hd-title">Per ricevere assistenza in merito ai corsi utilizza il seguente modulo:</h4>

    <form action="<?php echo JRoute::_('index.php?option=com_gglms&task=helpDesk.sendMailRequest.php'); ?>"
          method="post" name="contactForm" id="contactForm" class="form-validate">
        <div class="form-group row">
            <label class="col-sm-3 col-form-label" for="type">Tipologia assistenza:</label>
            <div class="col-sm-9">
                <fieldset class="">
                    <input type="radio" class="hd" name="request_type"  value="tecnica" checked="checked"> Tecnico
                    <input type="radio" class="hd" name="request_type"   value="didattica" > Didattico <br>
                </fieldset>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-3 col-form-label" for="nominativo">Nominativo:</label>
            <div class="col-sm-9">
                <input placeholder="Nominativo" type="text" class="form-control" id="nominativo" name="nominativo" required>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-3 col-form-label" for="email">Email:</label>
            <div class="col-sm-9">
                <input placeholder="Email" type="email" class="form-control" id="email"
                       name="email" required>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-3 col-form-label" for="question">Domanda:</label>
            <div class="col-sm-9">
                    <textarea placeholder="Scrivi il testo della domanda" class="form-control" id="question" required
                              name="question" rows="5"></textarea>
            </div>
        </div>
        <div class="form-group">
            <button id="btn_invia" type="submit" class="btn-block btn">Invia</button>
        </div>
        <input type="hidden" name="alias" value="<?php echo $this->info_piattaforma->alias?>"/>
    </form>
</div>

