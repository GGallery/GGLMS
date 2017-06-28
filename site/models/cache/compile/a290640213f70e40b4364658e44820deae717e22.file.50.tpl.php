<?php /* Smarty version Smarty-3.1.11, created on 2017-06-15 17:51:40
         compiled from "/var/www/vhosts/unicollege.it/httpdocs/mediagg/contenuti/50/50.tpl" */ ?>
<?php /*%%SmartyHeaderCode:21318475225942c92c1d1ba2-85092365%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a290640213f70e40b4364658e44820deae717e22' => 
    array (
      0 => '/var/www/vhosts/unicollege.it/httpdocs/mediagg/contenuti/50/50.tpl',
      1 => 1497548512,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '21318475225942c92c1d1ba2-85092365',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'data' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_5942c92c203718_31138813',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5942c92c203718_31138813')) {function content_5942c92c203718_31138813($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_date_format')) include '/var/www/vhosts/unicollege.it/httpdocs/home/components/com_gglms/libraries/smarty/smarty/plugins/modifier.date_format.php';
?><style type="text/css">
#container #attestato div {
}
</style>
<style>
    #container {
        text-align:center;
    }
    #attestato {
        margin: 0 auto;
        text-align:center;
    }
    h1 {
        color: navy;
        font-family: times;
        font-size: 24pt;
        text-align:center;
    }
    p {
        color: #000;
        font-family: times;
        font-size: 13pt;
        text-align:center;
    }
</style>
<div id="container">
    <div id="attestato">
              <div>
            <h1><img src="<?php echo $_smarty_tpl->tpl_vars['data']->value['content_path'];?>
/header.png" height="52" /></h1>
            <h3 align="center">Programma nazionale per la formazione continua degli operatori della sanità</h3>
            <p>
                Premesso che la <strong>Commissione Nazionale per la Formazione Continua</strong> ha accreditato 
                il Provider standard <strong>GGALLERY Srl</strong> accreditamento n.39.<br />

                Premesso che il Provider ha organizzato l'evento formativo<strong>

         

                </strong>, edizione n. 1 denominato <strong>PERCORSO INTERDISCIPLINARE PER IL FARMACISTA</strong> e tenutosi dal <strong>  15/06/2017</strong> al<strong> 31/12/2017</strong>,
                avente come obiettivi didattico/formativo generali:<em> Contenuti tecnico-professionali (conoscenze e competenze) specifici di ciascuna professione, di ciascuna specializzazione e di ciascuna attivit&agrave; ultraspecialistica, malattie rare</em>, assegnando all'evento stesso 
            </p>
          <p>N.<strong> 30 </strong>(trenta) Crediti Formativi E.C.M.
          </p>

          <p>
                Il sottoscritto <strong>PAOLO MACRI'</strong><br />
                Rappresentante Legale del Provider<br />
            <br />
            Verificato l'apprendimento del participante, ATTESTA CHE</p>
            <p>
                il Dott./la Dott.ssa<br />
                <strong><?php echo $_smarty_tpl->tpl_vars['data']->value['cognome'];?>
 <?php echo $_smarty_tpl->tpl_vars['data']->value['nome'];?>
</strong><br /> 
                in qualità di farmacista<br />
                nato a <?php echo $_smarty_tpl->tpl_vars['data']->value['fields']['Luogodinascita'];?>
  <br />
                il <?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['data']->value['fields']['Datadinascita'],'%d-%m-%Y');?>
                
                <br />
               <!-- iscritto all'Ordine Professionale di <?php echo $_smarty_tpl->tpl_vars['data']->value['Ordinedi'];?>
 num. 
            <?php echo $_smarty_tpl->tpl_vars['data']->value['NnumeroIscrizione'];?>
                -->
                <br />
              ha acquisito<br />
          N. 30 (trenta) Crediti formativi per l'anno 2017</p>
            <p>Genova, li 31/12/2017</p>
            <p>
                IL RAPPRESENTANTE LEGALE DELL'ORGANIZZATORE<br />
                Dott. Paolo Macrì
            <p><img src="<?php echo $_smarty_tpl->tpl_vars['data']->value['content_path'];?>
/FIRMA_PAOLO.jpg" width="165" height="55" /><p></div>
    </div>
</div>

<!-- ABILITARE LA SEGUENTE RIGA PER VISUALIZZARE LE VARIABILI -->
 <!--<?php echo var_dump($_smarty_tpl->tpl_vars['data']->value);?>
 --><?php }} ?>