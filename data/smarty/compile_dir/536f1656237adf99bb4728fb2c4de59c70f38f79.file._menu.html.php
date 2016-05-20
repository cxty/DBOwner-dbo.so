<?php /* Smarty version Smarty-3.0.8, created on 2013-08-09 10:27:42
         compiled from "./templates/_menu.html" */ ?>
<?php /*%%SmartyHeaderCode:3050678525204539ec67318-42835118%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '536f1656237adf99bb4728fb2c4de59c70f38f79' => 
    array (
      0 => './templates/_menu.html',
      1 => 1375951263,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '3050678525204539ec67318-42835118',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<div class="top_box">
    <div class="min_width">
        <div class="logo"></div>
        <div class="menu">
            <ul>
                <li><a href="#"><?php echo $_smarty_tpl->getVariable('Lang')->value['Apps'];?>
</a></li>
                <li><a href="http://dev.dbowner.com/"><?php echo $_smarty_tpl->getVariable('Lang')->value['Develop'];?>
</a></li>
                <li><a href="http://wiki.dbowner.com/"><?php echo $_smarty_tpl->getVariable('Lang')->value['Support'];?>
</a></li>
                <li><a href="#"><?php echo $_smarty_tpl->getVariable('Lang')->value['About'];?>
</a></li>
            </ul>
        </div>
        <div class="r_menu">
			<script type="text/javascript" language="javascript" src="http://auth.dbowner.com/provitejs/userbox?lang=<?php echo $_smarty_tpl->getVariable('ThisLang')->value;?>
" ></script>
            <!--
			<ul>
                <li><a href="#"><?php echo $_smarty_tpl->getVariable('Lang')->value['Login'];?>
</a></li>
                <li><a href="#"><?php echo $_smarty_tpl->getVariable('Lang')->value['Register'];?>
</a></li>
            </ul>
			-->
        </div>
    </div>
</div>