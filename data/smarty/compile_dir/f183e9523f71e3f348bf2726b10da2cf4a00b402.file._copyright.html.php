<?php /* Smarty version Smarty-3.0.8, created on 2013-08-09 10:27:42
         compiled from "./templates/_copyright.html" */ ?>
<?php /*%%SmartyHeaderCode:2862371645204539ec9f8a0-37844277%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f183e9523f71e3f348bf2726b10da2cf4a00b402' => 
    array (
      0 => './templates/_copyright.html',
      1 => 1375951263,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2862371645204539ec9f8a0-37844277',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_modifier_date_format')) include '/www/web/dbo_so/public_html/include/ext/smarty/plugins/modifier.date_format.php';
?><div class="bottom_box">
    <div class="min_width">
        <div class="copyright">Copyright <?php echo smarty_modifier_date_format(time(),"%Y");?>
 Yannyo Inc., </div>
        <div class="menu"><span id="lang_box"></span> <span>&nbsp;<a href="#"><?php echo $_smarty_tpl->getVariable('Lang')->value['Blog'];?>
</a> | <a href="#"><?php echo $_smarty_tpl->getVariable('Lang')->value['Teams'];?>
</a> | <a href="#"><?php echo $_smarty_tpl->getVariable('Lang')->value['Privacy'];?>
</a> | <a href="#"><?php echo $_smarty_tpl->getVariable('Lang')->value['ContactUs'];?>
</a></span></div>
    </div>
</div>
<div style="display:none">
<script type="text/javascript" src="http://tajs.qq.com/stats?sId=21568642" charset="UTF-8"></script>
</div>