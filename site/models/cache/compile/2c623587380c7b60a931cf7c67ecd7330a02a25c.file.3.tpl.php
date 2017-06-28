<?php /* Smarty version Smarty-3.1.11, created on 2017-05-18 12:28:30
         compiled from "/var/www/vhosts/axacollege.it/httpdocs/mediagg/contenuti/3/3.tpl" */ ?>
<?php /*%%SmartyHeaderCode:264072448591d774e717c70-84036421%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '2c623587380c7b60a931cf7c67ecd7330a02a25c' => 
    array (
      0 => '/var/www/vhosts/axacollege.it/httpdocs/mediagg/contenuti/3/3.tpl',
      1 => 1494950193,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '264072448591d774e717c70-84036421',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'data' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_591d774e729851_72004744',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_591d774e729851_72004744')) {function content_591d774e729851_72004744($_smarty_tpl) {?><style type="text/css">
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
        font-size: 22pt;
        text-align:center;
    }
    p {
        color: #000;
        font-family: times;
        font-size: 11pt;
        text-align:center;
    }
</style>
<div id="container">
    <div id="attestato">
              <div>
            <h1><img src="<?php echo $_smarty_tpl->tpl_vars['data']->value['content_path'];?>
/header.png" height="160" /></h1>
            <h3 align="center"><br />
             SI ATTESTA CHE</h3>
            <p><strong><?php echo $_smarty_tpl->tpl_vars['data']->value['cognome'];?>
 <?php echo $_smarty_tpl->tpl_vars['data']->value['nome'];?>
</strong></p>
            <p>ha partecipato al corso di formazione<br />
              &quot;AXA4broker: l'offerta internazionale di AXA, per le aziende e per i privati&quot;
                <br />
                tenutosi in aula a Padova c/o Four Points Sheraton il 16/05/2017
              </p>
            <p><strong>Docenti:</strong><br />
              Monica Ghirlandi (AXA), Mirko Formica (AXA Assistance), Monica Spinello (AXA ART), Laura Santoniccolo (AXA CS)</p>
            <p><strong>Argomenti:</strong><br />
              L'offerta internazionale di AXA per le aziende: soluzioni assicurative pe aziende internazionali (AXA, AXA CS)<br />
              L'offerta internazionale di AXA per i privati: nuovo prodotto tailorMade (AART), l'assistenza e la tutela  legale, con focus su Cyber Risk e Assistenza (AAssistance)</p>
            <p><strong>Durata:</strong><br />
              4 ORE in aula</p>
            <p>Il partecipante ha superato con esito positivo il test di fine corso per la verifica delle conoscenze acquisite.<br />
              Il presente attestato viene rilasciato in conformità a quanto previsto dall'art.8, comma 1 del Reg. IVASS n.6/2014. Si dichiara che AXA, in qualità di Ente Formatore, e i docenti di cui sopra sono in possesso dei requisiti di cui all'art.14 del Reg. IVASS n.6/2014.
            </p>
            <p align="left">Milano, <?php echo $_smarty_tpl->tpl_vars['data']->value['data_superamento'];?>
</p>
            <p align="right">
                Responsabile dell'Ente Formatore<p><img src="<?php echo $_smarty_tpl->tpl_vars['data']->value['content_path'];?>
/firma_axa.jpg" width="165" height="55" />
                <p align="right">Italo Carli<br />
                  Direttore Generale AXA ART
                
      </p></div>
    </div>
</div>

<!-- ABILITARE LA SEGUENTE RIGA PER VISUALIZZARE LE VARIABILI -->
<!-- <?php echo var_dump($_smarty_tpl->tpl_vars['data']->value);?>
  --><?php }} ?>