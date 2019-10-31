<?php
// no direct access

defined('_JEXEC') or die('Restricted access');


echo "<h1> Help Desk " . $this->info_piattaforma->alias . "</h1>";

?>

<div>
    <h4>Per informazioni o per l'acquisto rivolgersi a <?php echo $this->info_piattaforma->name ?> </h4>
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
                    ></span>   <b><a href="<?php echo $this->info_piattaforma->email_riferimento ?>"><?php echo $this->info_piattaforma->email_riferimento ?></a></b></div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div> <span title='email' class='glyphicon glyphicon-shopping-cart info-icon'
                    ></span>   <b> <a href="<?php echo $this->info_piattaforma->link_ecommerce ?>">catalogo e-commerce</a></b></div>

            </div>

        </div>

    </div>


    <!---->
    <!--    <div class="container">-->
    <!--        <h2 class="text-center">Contac Form</h2>-->
    <!--        <div class="row justify-content-center">-->
    <!--            <div class="col-12 col-md-8 col-lg-6 pb-5">-->
    <!---->
    <!---->
    <!--                Form with header-->
    <!---->
    <!--                <form action="mail.php" method="post">-->
    <!--                    <div class="card border-primary rounded-0">-->
    <!--                        <div class="card-header p-0">-->
    <!--                            <div class="bg-info text-white text-center py-2">-->
    <!--                                <h3><i class="fa fa-envelope"></i> Contactanos</h3>-->
    <!--                                <p class="m-0">Con gusto te ayudaremos</p>-->
    <!--                            </div>-->
    <!--                        </div>-->
    <!--                        <div class="card-body p-3">-->
    <!---->
    <!--                            Body-->
    <!--                            <div class="form-group">-->
    <!--                                <div class="input-group mb-2">-->
    <!--                                    <div class="input-group-prepend">-->
    <!--                                        <div class="input-group-text"><i class="fa fa-user text-info"></i></div>-->
    <!--                                    </div>-->
    <!--                                    <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre y Apellido" required>-->
    <!--                                </div>-->
    <!--                            </div>-->
    <!--                            <div class="form-group">-->
    <!--                                <div class="input-group mb-2">-->
    <!--                                    <div class="input-group-prepend">-->
    <!--                                        <div class="input-group-text"><i class="fa fa-envelope text-info"></i></div>-->
    <!--                                    </div>-->
    <!--                                    <input type="email" class="form-control" id="nombre" name="email" placeholder="ejemplo@gmail.com" required>-->
    <!--                                </div>-->
    <!--                            </div>-->
    <!---->
    <!--                            <div class="form-group">-->
    <!--                                <div class="input-group mb-2">-->
    <!--                                    <div class="input-group-prepend">-->
    <!--                                        <div class="input-group-text"><i class="fa fa-comment text-info"></i></div>-->
    <!--                                    </div>-->
    <!--                                    <textarea class="form-control" placeholder="Envianos tu Mensaje" required></textarea>-->
    <!--                                </div>-->
    <!--                            </div>-->
    <!---->
    <!--                            <div class="text-center">-->
    <!--                                <input type="submit" value="Enviar" class="btn btn-info btn-block rounded-0 py-2">-->
    <!--                            </div>-->
    <!--                        </div>-->
    <!---->
    <!--                    </div>-->

    <!---->
    <!---->
    <!--            </div>-->
    <!--        </div>-->
    <!--    </div>-->
</div>


<!--<script type="application/javascript">pippo()</script>-->
