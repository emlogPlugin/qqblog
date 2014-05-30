<?php
/*
Plugin Name: 同步到QQ空间
Version: 0.1
Plugin URL:http://www.justintseng.com
Description: 发表日志时可选择是否拷备一份至QQ空间，基于寒川版Qzone日志插件制作。
Author: Justin Tseng
Author Email: admin@justintseng.com
Author URL: http://www.justintseng.com
*/
!defined('EMLOG_ROOT') && exit('access deined!');

function qqblog_hide()
{
?>
    <input type="checkbox" id="qzone" value="1" name="qzone" /><label for="qzone">同步到QQ空间</label>
<?php
}
    addAction('adm_writelog_head','qqblog_hide');//挂载

function qqblog_publish()//发布
{
global $logData,$action,$blogid,$qzone_hide;
$qzone_hide = isset($_POST['qzone']) ? 'y' : 'n';
include('../content/plugins/qqblog/qqblog_config.php');
if($action == 'add')
{
  if($logData['password'] != '')
  {
//    $logData['content'] ='此日志为加密日志，请<a href='.BLOG_URL.'?post='.$blogid.' target="_blank">点击此处</a>查看';
	  $logData['hide'] ='y';
  }
  if($logData['hide'] != 'y' && $GLOBALS["qzone_hide"] != 'n')
  {
    $host=$_SERVER['HTTP_HOST'];
    $post='qq='.rawurlencode(QQ);
    $post.='&pwd='.rawurlencode(PWD);
    $post.='&title='.rawurlencode($logData['title']);
    $post.='&content='.rawurlencode(stripslashes($logData['content']));
    $len =strlen($post);
    $file=BLOG_URL."/content/plugins/qqblog/qqblog_publish.php";
    $fp=fsockopen($host, 80, $errno, $errstr, 30);
      if (!$fp) 
      {
        echo "$errstr ($errno)\n";
      }
      else 
      {
        $out = "POST $file HTTP/1.1\r\n";
        $out .= "Host: $host\r\n";
        $out .= "Content-type: application/x-www-form-urlencoded\r\n";
        $out .= "Connection: Close\r\n";
        $out .= "Content-Length: $len\r\n";
        $out .="\r\n";
        $out .= $post."\r\n";
        fwrite($fp, $out);
        fclose($fp);     
      }
    }
 }
  
}
addAction('save_log', 'qqblog_publish');//挂载

function qqblog_menu()
{
	echo '<div class="sidebarsubmenu" id="qqblog"><a href="./plugin.php?plugin=qqblog">同步到QQ空间</a></div>';
}
	addAction('adm_sidebar_ext', 'qqblog_menu');
?>