<?php
if(!defined('EMLOG_ROOT')) {exit('error!');}
function plugin_setting_view()
{
	include(EMLOG_ROOT.'/content/plugins/qqblog/qqblog_config.php');
?>
<script>
$("#qqblog").addClass('sidebarsubmenu1');
</script>
<div class=containertitle><b>同步到QQ空间</b>
<?php if(isset($_GET['setting'])):?><span class="actived">插件设置完成</span><?php endif;?>
</div>
<div class=line></div>
<div>
<form id="form1" name="form1" method="post" action="plugin.php?plugin=qqblog&action=setting">
<table width="540" border="0" cellspacing="0" cellpadding="0">
<tr>
<td width="90"><span style="width:300px;">QQ号码</span></td>
<td width="450"><input name="qq" type="text" id="qq" style="width:180px;" value="<?php echo QQ;?>"/></td>
</tr>
<tr>
<td>QQ邮箱密码</td>
<td><input type="password" name="pwd" value="<?php echo PWD;?>" style="width:180px;"/></td>
</tr>
<tr>
<td height="30">&nbsp;</td>
<td><input name="Input" type="submit" value="提交" /> <input name="Input" type="reset" value="取消" /></td>
</tr>
</table>
</form>
<br/>
说明：请确认本插件目录下“qqblog_config.php”文件据有可读写权限。如有疑问，请访问<a href="http://www.justintseng.com/qqblog" target="_blank">我的博客</a>留言，将尽量解答。
</div>
<?php
}
function plugin_setting()
{
  include(EMLOG_ROOT.'/content/plugins/qqblog/qqblog_config.php');
	$qqblog_fso = fopen(EMLOG_ROOT.'/content/plugins/qqblog/qqblog_config.php','r');
	$qqblog_config = fread($qqblog_fso,filesize(EMLOG_ROOT.'/content/plugins/qqblog/qqblog_config.php'));
	fclose($qqblog_fso);

	$qqblog_qq = htmlspecialchars($_POST['qq'], ENT_QUOTES);
	$qqblog_pwd = htmlspecialchars($_POST['pwd'], ENT_QUOTES);
	$interval = is_numeric($_POST['interval'])&&$_POST['interval'] > 0 ? $_POST['interval'] : '0';
	$qqblog_patt = array("/define\('QQ',(.*)\)/","/define\('PWD',(.*)\)/","/define\('INTERVAL',(.*)\)/");
	$qqblog_replace = array("define('QQ','".$qqblog_qq."')","define('PWD','".$qqblog_pwd."')","define('INTERVAL','".$interval."')");
	$qqblog_new_config = preg_replace($qqblog_patt, $qqblog_replace, $qqblog_config);
	$qqblog_fso =@fopen(EMLOG_ROOT.'/content/plugins/qqblog/qqblog_config.php','w');
	if(!$qqblog_fso) emMsg('数据存取失败，请确认本插件目录下"qqblog_config.php"文件为可读写权限(777)！');
	fwrite($qqblog_fso,$qqblog_new_config);
	fclose($qqblog_fso);
}
?>