<?php
!defined('IN_VISION') && exit('Access Denied');

/* 
 * 邮件发送类
 */
class EmailUtils {

	 var $smtp = ""; //SMTP 服务器供应商
	 
	 var $check = 1;  //SMTP需要要身份验证设值为 1 不需要身份验证值为 0
	 
	 var $username = "";//您的email帐号名称
	
	 var $password = ""; //您的email密码
	 
	 var $s_from = "";//此email 必需是发信服务器上的email
 
	 /* 
	  * 功能：发信初始化设置
	  * $from      你的发信服务器上的邮箱
	  * $password  你的邮箱密码
	  * $smtp      您的SMTP 服务器供应商，可以是域名或IP地址
	  * $check     SMTP需要要身份验证设值为 1 不需要身份验证值为 0，现在大多数的SMTP服务商都要验证
	  */ 
	 function EmailUtils ( $from = '', $password = '', $smtp = 'smtp.sina.com', $check = 1 ) { 
		if( preg_match("/^[^\d\-_][\w\-]*[^\-_]@[^\-][a-zA-Z\d\-]+[^\-](\.[^\-][a-zA-Z\d\-]*[^\-])*\.[a-zA-Z]{2,3}/", $from ) ) {
			$this->username = substr( $from, 0, strpos( $from , "@" ) );
			$this->password = $password;
			$this->smtp = $smtp ? $smtp : $this->smtp;
			$this->check = $check;
			$this->s_from = $from;
		}
	 }
 
	 /* 
	  * 功能：发送邮件
	  * $to   目标邮箱
	  * $from 来源邮箱
	  * $subject 邮件标题
	  * $message 邮件内容
	  */
	 function Send( $to, $from, $subject, $message ) { 
	 	//echo "\r\n发送邮件...\r\n";
		 		   
		$subject = "=?UTF-8?B?".base64_encode($subject)."?="; //对标题进行编码
		//$subject = 'hello jj';

		//连接服务器 
		$fp = fsockopen( $this->smtp, 25, $errno, $errstr, 60); 
		if (!$fp ) {
			return "联接服务器失败".__LINE__;
		}

		set_socket_blocking($fp, true); 
		$lastmessage = fgets($fp, 512);
		//test echo $lastmessage;
		if ( substr($lastmessage,0,3) != 220 ) {
			return "error1:$lastmessage".__LINE__; 
		}


		//HELO
		$yourname = "chinav";
		if ($this->check == "1") {
			$lastact="EHLO ".$yourname."\r\n";
		} else {
			$lastact="HELO ".$yourname."\r\n";
		}

		fputs($fp, $lastact);
		//test echo $lastact;

		$lastmessage == fgets($fp, 512);
		//test echo $lastmessage;
		if (substr($lastmessage,0,3) != 220 ) {
			return "error2:$lastmessage".__LINE__; 
		}

		while (true) {
			$lastmessage = fgets($fp, 512);
			//test echo $lastmessage;
			if ( (substr($lastmessage,3,1) != "-")  or  (empty($lastmessage)) ) {
				break;
			}
		}

		//身份验证
		if ($this->check=="1") {
			//echo "\r\n身份验证...\r\n";

			//验证开始
			$lastact="AUTH LOGIN"."\r\n";
			fputs( $fp, $lastact);
			//test echo $lastact;

			$lastmessage = fgets ($fp,512);
			//test echo $lastmessage;
			if (substr($lastmessage,0,3) != 334) {
				return "error3:$lastmessage".__LINE__; 
			}

			//用户姓名
			$lastact=base64_encode($this->username)."\r\n";
			fputs( $fp, $lastact);
			//test echo $lastact;

			$lastmessage = fgets ($fp,512);
			//test echo $lastmessage;
			if (substr($lastmessage,0,3) != 334) {
				return "error4:$lastmessage".__LINE__;
			}

			//用户密码
			$lastact=base64_encode($this->password)."\r\n";
			fputs( $fp, $lastact);
			//test echo $lastact;

			$lastmessage = fgets ($fp,512);
			//test echo $lastmessage;
			if (substr($lastmessage,0,3) != "235") {
				return "error5:$lastmessage".__LINE__;
			}
		}

		//echo "\r\nFROM...\r\n";

		//FROM:
		$lastact="MAIL FROM: <". $this->s_from . ">\r\n"; 
		//test echo $lastact;

		fputs( $fp, $lastact);
		$lastmessage = fgets ($fp,512);
		//test echo $lastmessage;
		if (substr($lastmessage,0,3) != 250) {
			return "error6:$lastmessage".__LINE__;
		}

		//TO:
		$lastact="RCPT TO: <". $to ."> \r\n"; 
		//test echo $lastact;

		fputs( $fp, $lastact);
		$lastmessage = fgets ($fp,512);
		//test echo $lastmessage;
		if (substr($lastmessage,0,3) != 250) {
			return "error7:$lastmessage".__LINE__;
		}

		//DATA
		$lastact="DATA\r\n";
		fputs($fp, $lastact);
		//test echo $lastact;

		$lastmessage = fgets($fp,512);
		//test echo $lastmessage;
		if (substr($lastmessage,0,3) != 354) {
			return "error8:$lastmessage".__LINE__;
		}

		// $message = "hello mm!";

		//echo "\r\n消息内容...\r\n";
		$headers  = "MIME-Version: 1.0\r\n";
	    $headers .= "Content-type: text/html; charset=UTF-8\r\n"; //UTF-8
	    $headers .= "From: $from\r\n"; 
	    $headers .= "To: $to\r\n";
	    $headers .= "Subject: $subject\r\n";
	    $headers .= "\r\n";

		//处理To头 
		$message = $headers.$message;

		//加上结束串 
		$message .= "\r\n.\r\n";

		//发送信息 
		fputs($fp, $message); 
		//test echo $message;

		$lastmessage = fgets($fp, 512);
		//test echo $lastmessage;		

		$lastact="QUIT\r\n"; 
		fputs($fp, $lastace);
		//test echo $lastact;

		fclose($fp); 
		return 1;
	} 
}

//发送示例
/*$sm = new EmailUtils( "server@chinavtech.cn", "888888", "202.104.149.232" );
$end = $sm->Send( "ldm@chinavtech.cn", "ldm@chinavtech.cn", "这是标题", "这是内容" );
if( $end ) //test echo $end;
else echo "发送成功";
*/
?>