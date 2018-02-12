<?php /* Smarty version Smarty-3.1.11, created on 2017-12-15 15:41:59
         compiled from "C:\xampp\htdocs\unico\components\com_gglms\models\template\libretto_cicli.tpl" */ ?>
<?php /*%%SmartyHeaderCode:196955a33df37b1c224-75960979%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '45f997757b2a98444d352a2a2652ed89ec2e3b4c' => 
    array (
      0 => 'C:\\xampp\\htdocs\\unico\\components\\com_gglms\\models\\template\\libretto_cicli.tpl',
      1 => 1513336384,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '196955a33df37b1c224-75960979',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'data' => 0,
    'row' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_5a33df37c2d962_59055291',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5a33df37c2d962_59055291')) {function content_5a33df37c2d962_59055291($_smarty_tpl) {?>



    <style>
        #container {
            text-align:center;
        }
        .attestato {
            margin: 0 auto;
            text-align:center;
        }

        h1, h2 {
            text-align:center;
            color: navy;
            font-family: times;
        }

        h1 {
            font-size: 24pt;
        }
        h2 {
            font-size: 18pt;
        }
        p {
            color: #000;
            font-family: times;
            font-size: 14pt;
            text-align:center;
        }
        p.small {
            font-size: 10pt;
        }
        p.big {
            font-size: 16pt;
            text-align:right;
        }
    </style>

<div id="container">



        <div>
            <h1>LIBRETTO FORMATIVO</h1>
            <p>
                di:
            </p>
            <h2><?php echo $_smarty_tpl->tpl_vars['data']->value['nome'];?>
 <?php echo $_smarty_tpl->tpl_vars['data']->value['cognome'];?>
</h2>

        </div>



</div>

    <div id="tracklog">


            <?php  $_smarty_tpl->tpl_vars['row'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['row']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['data']->value['rows']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['row']->key => $_smarty_tpl->tpl_vars['row']->value){
$_smarty_tpl->tpl_vars['row']->_loop = true;
?>

                <div>

                    <div class="attestato">
                        <h4><?php echo $_smarty_tpl->tpl_vars['row']->value['corso'];?>
</h4>
                    </div>
                        <div style="margin-left: 40%;">data fine corso:<?php echo $_smarty_tpl->tpl_vars['row']->value['data_fine'];?>
</div>
                        <div style="margin-left: 40%;">durata del corso:<?php echo $_smarty_tpl->tpl_vars['row']->value['durata'];?>
 giorni</div>



                </div>

                <hr  style="border-top dashed 3px;">
            <?php } ?>


 </div>





<?php }} ?>