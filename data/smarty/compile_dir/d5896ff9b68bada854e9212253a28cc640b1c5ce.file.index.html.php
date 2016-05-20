<?php /* Smarty version Smarty-3.0.8, created on 2013-08-21 14:25:52
         compiled from "./templates/index/index.html" */ ?>
<?php /*%%SmartyHeaderCode:87869353352145d702e9a31-27588593%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd5896ff9b68bada854e9212253a28cc640b1c5ce' => 
    array (
      0 => './templates/index/index.html',
      1 => 1377066349,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '87869353352145d702e9a31-27588593',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php $_template = new Smarty_Internal_Template("_header.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate(); $_template->rendered_content = null;?><?php unset($_template);?>
<?php $_template = new Smarty_Internal_Template("_menu.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate(); $_template->rendered_content = null;?><?php unset($_template);?>
<script type="text/javascript" src="<?php echo $_smarty_tpl->getVariable('__ROOT__')->value;?>
/public/js/index.js"></script>

<form action="" id="form_1" name="form_1" method="post" enctype="multipart/form-data">
    <div class="center_box">
        <div class="in_box">
    		<div class="title_box"><?php echo $_smarty_tpl->getVariable('Lang')->value['s_Title'];?>
</div>
            <div class="input_box">
            	<div class="l_box"><input type="text" name="url" id="url" value="<?php echo $_smarty_tpl->getVariable('url')->value;?>
" tabindex="1"></div>
                <div class="r_box">
                	<input type="button" id="sub_go" name="sub_go" value="<?php echo $_smarty_tpl->getVariable('Lang')->value['sub_go_txt'];?>
" />
                </div>
            </div>
            <div class="msg_box"><?php echo $_smarty_tpl->getVariable('Lang')->value['s_msg'];?>
</div>
            
            <div class="out_box">
                
                <div class="new_url"><input type="text" value="<?php echo $_smarty_tpl->getVariable('s_url')->value;?>
" readonly="readonly" />
                	<div class="qrcode"></div>
                </div>
                
                <div class="m">=</div>
                
            </div>
    	</div>
        
    </div>
</form>   
<script language="javascript" type="text/javascript">
var _s_url = '<?php echo $_smarty_tpl->getVariable('s_url')->value;?>
';
</script>
<?php $_template = new Smarty_Internal_Template("_copyright.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate(); $_template->rendered_content = null;?><?php unset($_template);?>
<?php $_template = new Smarty_Internal_Template("_bottom.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate(); $_template->rendered_content = null;?><?php unset($_template);?>
