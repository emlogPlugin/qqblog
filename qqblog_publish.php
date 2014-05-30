<?php
set_time_limit(60);
ignore_user_abort(true);
$qq = isset($_POST['qq']) ? $_POST['qq'] : '';
$pwd = isset($_POST['pwd']) ? $_POST['pwd'] : '';
$title = isset($_POST['title']) ? $_POST['title'] : '';
$content = isset($_POST['content'])  ? stripslashes($_POST['content']) : '';
if($qq !='' && $pwd != '' && $title != '' && $content != '')
  {
    $email= array(
    'user'=>$qq,
    'pw'=>$pwd,
    'from'=>$qq.'@qq.com',
	'to'=>$qq.'@qzone.qq.com',
    'subject'=>$title,
    'content'=>$content,
    'charset'=>'utf8'
    );
    $qqblog = new bigmail($email);
  }
  else
  {
    header("HTTP/1.0 404 Not Found");
  }

/*
$fp = fopen("log.txt","a");	
fwrite($fp,$qq.' '.$pwd.' '.$title.' '.$content." 执行时间：".strftime("%Y%m%d%H%I%S",time())."\r\n");
fclose($fp);
*/


class bigmail {//The Class From the Internet.
	var $host;		//SMTP server
	var $port;	//SMTP port
 	var $auth;
	var $user;		//SMTP user
	var $pw;		//SMTP password
	var $charset="utf-8";
	var $errno;
	var $error;
	var $fp;
	var $lim;
	var $mode = 1;
	

	var $html;
	var $show;
	var $header;
	var $from;
	var $to;
	var $subject;
	var $content;
	var $document;
	var $sended;
	
	function bigmail($email=array()){
		
		$mail = array (
			
			'host' => 'smtp.qq.com',
			'port' => '25',
			'auth' => '1',
			'lim' => '0',
			'mode' => '2',
			'showuser' => '0',
			'html' => '1',
		);

		$this->host		= $mail['host'];
		$this->port		= isset($mail['port'])?$mail['port']:25;
		$this->auth		= $mail['auth'];
		$this->user		= $email['user'];
		$this->pw		= $email['pw'];
		$this->lim		= $email['lim'] == 1 ? "\n" : ($mail['lim'] == 2 ? "\r" : "\r\n");
		$this->mode		= $mail['mode'];
		$this->charset	= $email['charset'];

		$this->html		= ($email['html']?$email['html']:$mail['html']) ? "text/html" : "text/plain";
		$this->show		= isset($mail['showuser']) ? $mail['showuser'] : 1;
		$this->from = $email['from'] == '' ? '=?'.$this->charset.'?B?'.basecode($options['sitename']) : (preg_match('/^(.+?) \<(.+?)\>$/',$email['from'], $from) ? '=?'.$this->charset.'?B?'.basecode($from[1])."?= <$from[2]>" : $email['from']);
		$this->header = "From: $this->from{$this->lim}X-Priority: 3{$this->lim}X-Mailer: Bigqi Emailer! {$this->lim}MIME-Version: 1.0{$this->lim}Content-type: {$this->html}; charset={$this->charset}{$this->lim}Content-Transfer-Encoding: base64{$this->lim}";
		
		foreach(explode(',', $email['to']) as $touser) {
			$tousers[] = preg_match('/^(.+?) \<(.+?)\>$/',$touser, $to) ? ($this->show ? '=?'.$this->charset.'?B?'.basecode($to[1])."?= <$to[2]>" : $to[2]) : $touser;
		}
		$this->to = implode(',', $tousers);
		$this->subject = '=?'.$this->charset.'?B?'.basecode(str_replace("\r", '', str_replace("\n", '', $email['subject']))).'?=';
		$this->content = chunk_split(basecode(str_replace("\r\n.", " \r\n..", str_replace("\n", "\r\n", str_replace("\r", "\n", str_replace("\r\n", "\n", str_replace("\n\r", "\r", $email['content'])))))));
		$this->sended = $this->send();
	}
	function close(){
		@fclose($this->fp);
	}
	function send(){
		if ($this->mode == 0){
			@mail($this->to, $this->subject, $this->content, $this->header);
		}elseif ($this->mode == 1) {
			ini_set('SMTP', $this->host);
			ini_set('smtp_port', $this->port);
			ini_set('sendmail_from', $this->from);
			@mail($this->to, $this->subject, $this->content, $this->header);
		}elseif ($this->mode== 2) {
			if(!$this->fp = fsockopen($this->host, $this->port, $this->errno, $this->error, 30)) {
				$this->error("($this->host:$this->port) Connect - Unable to connect to the SMTP server ");
			}
			stream_set_blocking($this->fp, true); //set blocking mode 
			$server_remsg = fgets($this->fp, 512);
			if(substr($server_remsg, 0, 3) != '220') {
				$this->error("($this->host:$this->port) Connect - $server_remsg ");				
			}
			fputs($this->fp, ($this->auth ? 'EHLO' : 'HELO')." bigqi\r\n");
			$server_remsg = fgets($this->fp, 512);
			if(substr($server_remsg, 0, 3) != 220 && substr($server_remsg, 0, 3) != 250) {
				$this->error("($this->host:$this->port) HELO/EHLO - $server_remsg ");	
			}

			while(1) {
				if(substr($server_remsg, 3, 1) != '-' || empty($server_remsg)) {
					break;
				}
				$server_remsg = fgets($this->fp, 512);
			}
			if($this->auth) {
				fputs($this->fp, "AUTH LOGIN\r\n");
				$server_remsg = fgets($this->fp, 512);
				if(substr($server_remsg, 0, 3) != 334) {
					$this->error("($this->host:$this->port) AUTH LOGIN - $server_remsg ");
				}

				fputs($this->fp, basecode($this->user)."\r\n");
				$server_remsg = fgets($this->fp, 512);
				if(substr($server_remsg, 0, 3) != 334) {
					$this->error("($this->host:$this->port) AUTH USERNAME - $server_remsg ");
				}
				fputs($this->fp, basecode($this->pw)."\r\n");
				$server_remsg = fgets($this->fp, 512);
				if(substr($server_remsg, 0, 3) != 235) {
					$this->error("($this->host:$this->port) AUTH PASSWORD - $server_remsg ");
				}
			}
			fputs($this->fp, "MAIL FROM: <".preg_replace("/.*\<(.+?)\>.*/", "\\1", $this->from).">\r\n");
			$server_remsg = fgets($this->fp, 512);
			if(substr($server_remsg, 0, 3) != 250) {
				fputs($this->fp, "MAIL FROM: <".preg_replace("/.*\<(.+?)\>.*/", "\\1", $this->from).">\r\n");
				$server_remsg = fgets($this->fp, 512);
				if(substr($server_remsg, 0, 3) != 250) {
					$this->error("($this->host:$this->port) MAIL FROM - $server_remsg ");
				}
			}
			

			foreach(explode(',', $this->to) as $touser) {
				$touser = charsfilter($touser,'T');
				if($touser) {
					fputs($this->fp, "RCPT TO: <".preg_replace("/.*\<(.+?)\>.*/", "\\1", $touser).">\r\n");
					$server_remsg = fgets($this->fp, 512);
					if(substr($server_remsg, 0, 3) != 250) {
						fputs($this->fp, "RCPT TO: <".preg_replace("/.*\<(.+?)\>.*/", "\\1", $touser).">\r\n");
						$server_remsg = fgets($this->fp, 512);
						$this->error("($this->host:$this->port) RCPT TO - $server_remsg ");
						
					}
				}
			}
			
			
			fputs($this->fp, "DATA\r\n");
			$server_remsg = fgets($this->fp, 512);

			if(substr($server_remsg, 0, 3) != 354) {
				$this->error("($this->host:$this->port) DATA - $server_remsg ");
			}
			$msg  = "Date: ".Date("r")."\r\n";
			$msg .= "To: ".$this->to."\r\n";
			$msg .= "Subject: ".$this->subject."\r\n";
			$msg .= $this->header;
			$msg .= 'Message-ID: <'.gmdate('YmdHs').'.'.substr(md5($this->subject.microtime()), 0, 6).rand(100000, 999999).'@'.$_SERVER['HTTP_HOST'].">{$this->lim}\r\n";
			$msg .= "\r\n\r\n";
			$msg .= "$this->content\r\n.\r\n";
			fputs($this->fp, $msg);
			$server_remsg = fgets($this->fp, 512);
			fputs($this->fp, "QUIT\r\n");	
			$this->close();
			if(substr($server_remsg, 0, 3) != 250) {
				return false;
			}else {
				return true;
			}
		}elseif ($this->mode == 3){
			
			
		}
	}
	function error($info){
		$this->close();
		$errorout =  "<b>ERROR!</b><br />\n<b>info:</b>".$info."<br />\n";
		exit($errorout);
	}
}
function basecode($str, $operation = 'EN') {
	$str = $operation == 'DE' ? base64_decode($str) : base64_encode($str);
	return $str;
}
function charsfilter($string,$operation='S'){

	if(preg_match ("/T/", $operation)){
		$string = trim($string);
	}
	if(preg_match ("/L/", $operation)){
		$string = strtolower($string);
	}
	if(preg_match ("/S/", $operation)){
		$string = htmlspecialchars($string);
	}
	return $string;
}
?>