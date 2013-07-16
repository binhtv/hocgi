<?php
class Business_Common_Utils
{
	static function redirect($url)
	{
		header( 'Location: ' . $url ) ;exit;	
		
	}

	static function prepareDay() {
		$return = "";
		for($i=1;$i<=31;$i++) {
			if($i<10) {
				$return .= "<option value='0" . $i . "'>0" . $i . "</option>";
			}
			else {
				$return .= "<option value='" . $i . "'>" . $i . "</option>";
			}		
		}
		return $return;
	}

	static function prepareMonth() {
		$return = "";
		for($i=1;$i<=12;$i++) {
			if($i<10) {
				$return .= "<option value='0" . $i . "'>0" . $i . "</option>";
			}
			else {
				$return .= "<option value='" . $i . "'>" . $i . "</option>";
			}
		}
		return $return;
	}

	static function prepareYear($range = 100) {
		$year = intval(date('Y'));

		$start = $year - $range;

		$return = "";
		for($i=$start;$i<$year;$i++) {
			if($i<10) {
				$return .= "<option value='0" . $i . "'>0" . $i . "</option>";
			}
			else {
				$return .= "<option value='" . $i . "'>" . $i . "</option>";
			}
		}
		return $return;
	}
	
	static function adaptTitleLinkURL($title)
	{
		$title = str_replace("-","",$title);
		$title = str_replace("  "," ",$title);
		$special = array(" ","/","\\","?","&");
		$title = str_replace($special,"_",$title);		
		$title = self::removeTiengViet($title);
		$title = strtolower($title) . '.html';
		return $title;
	}
	static function removeTiengViet($content)
	{
		 $trans = array ('à' => 'a', 'á' => 'a', 'ả' => 'a', 'ã' => 'a', 'ạ' => 'a', 'â' => 'a', 'ấ' => 'a', 'ầ' => 'a', 'ẫ' => 'a', 'ẩ' => 'a', 'ậ' => 'a', 'ú' => 'a', 'ù' => 'u', 'ủ' => 'u', 'ũ' => 'u', 'ụ' => 'u', 'à' => 'a', 'á' => 'a', 'ô' => 'o', 'ố' => 'o', 'ồ' => 'o', 'ổ' => 'o', 'ỗ' => 'o', 'ộ' => 'o', 'ó' => 'o', 'ò' => 'o', 'ỏ' => 'o', 'õ' => 'o', 'ọ' => 'o', 'ê' => 'e', 'ế' => 'e', 'ề' => 'e', 'ể' => 'e', 'ễ' => 'e', 'ệ' => 'e', 'í' => 'i', 'ì' => 'i', 'ỉ' => 'i', 'ĩ' => 'i', 'ị' => 'i', 'ơ' => 'o', 'ớ' => 'o', 'ý' => 'y', 'ỳ' => 'y', 'ỷ' => 'y', 'ỹ' => 'y', 'ỵ' => 'y', 'ờ' => 'o', 'ở' => 'o', 'ỡ' => 'o', 'ợ' => 'o', 'ư' => 'u', 'ừ' => 'u', 'ứ' => 'u', 'ử' => 'u', 'ữ' => 'u', 'ự' => 'u', 'đ' => 'd', 'À' => 'A', 'Á' => 'A', 'Ả' => 'A', 'Ã' => 'A', 'Ạ' => 'A', 'Â' => 'A', 'Ấ' => 'A', 'À' => 'A', 'Ẫ' => 'A', 'Ẩ' => 'A', 'Ậ' => 'A', 'Ú' => 'U', 'Ù' => 'U', 'Ủ' => 'U', 'Ũ' => 'U', 'Ụ' => 'U', 'Ô' => 'O', 'Ố' => 'O', 'Ồ' => 'O', 'Ổ' => 'O', 'Ỗ' => 'O', 'Ộ' => 'O',
        'Ê' => 'E', 'Ế' => 'E', 'Ề' => 'E', 'Ể' => 'E', 'Ễ' => 'E', 'Ệ' => 'E', 'Í' => 'I', 'Ì' => 'I', 'Ỉ' => 'I', 'Ĩ' => 'I', 'Ị' => 'I', 'Ơ' => 'O', 'Ớ' => 'O', 'Ờ' => 'O', 'Ở' => 'O', 'Ỡ' => 'O', 'Ợ' => 'O', 'Ư' => 'U', 'Ừ' => 'U', 'Ứ' => 'U', 'Ử' => 'U', 'Ữ' => 'U', 'Ự' => 'U', 'Đ' => 'D', 'Ý' => 'Y', 'Ỳ' => 'Y', 'Ỷ' => 'Y', 'Ỹ' => 'Y', 'Ỵ' => 'Y', 
        'á' => 'a', 'à' => 'a', 'ả' => 'a', 'ã' => 'a', 'ạ' => 'a', 'ă' => 'a', 'ắ' => 'a', 'ằ' => 'a', 'ẳ' => 'a', 'ẵ' => 'a', 'ặ' => 'a', 'â' => 'a', 'ấ' => 'a', 'ầ' => 'a', 'ẩ' => 'a', 'ẫ' => 'a', 'ậ' => 'a', 'ú' => 'u', 'ù' => 'u', 'ủ' => 'u', 'ũ' => 'u', 'ụ' => 'u', 'ư' => 'u', 'ứ' => 'u', 'ừ' => 'u', 'ử' => 'u', 'ữ' => 'u', 'ự' => 'u', 'í' => 'i', 'ì' => 'i', 'ỉ' => 'i', 'ĩ' => 'i', 'ị' => 'i', 'ó' => 'o', 'ò' => 'o', 'ỏ' => 'o', 'õ' => 'o', 'ọ' => 'o', 'ô' => 'o', 'ố' => 'o', 'ồ' => 'ô', 'ổ' => 'o', 'ỗ' => 'o', 'ộ' => 'o', 'ơ' => 'o', 'ớ' => 'o', 'ờ' => 'o', 'ở' => 'o', 'ỡ' => 'o', 'ợ' => 'o', 'đ' => 'd', 'Đ' => 'D', 'ý' => 'y', 'ỳ' => 'y', 'ỷ' => 'y', 'ỹ' => 'y', 'ỵ' => 'y', 'Á' => 'A', 'À' => 'A', 'Ả' => 'A', 'Ã' => 'A', 'Ạ' => 'A', 'Ă' => 'A', 'Ắ' => 'A', 'Ẳ' => 'A', 'Ẵ' => 'A', 'Ặ' => 'A', 'Â' => 'A', 'Ấ' => 'A', 'Ẩ' => 'A', 'Ẫ' => 'A', 'Ậ' => 'A', 'É' => 'E', 'È' => 'E', 'Ẻ' => 'E', 'Ẽ' => 'E', 'Ẹ' => 'E', 'Ế' => 'E', 'Ề' => 'E', 'Ể' => 'E', 'Ễ' => 'E', 'Ệ' => 'E', 'Ú' => 'U', 'Ù' => 'U', 'Ủ' => 'U', 'Ũ' => 'U', 'Ụ' => 'U', 'Ư' => 'U', 'Ứ' => 'U', 'Ừ' => 'U', 'Ử' => 'U', 'Ữ' => 'U', 'Ự' => 'U', 'Í' => 'I', 'Ì' => 'I', 'Ỉ' => 'I', 'Ĩ' => 'I', 'Ị' => 'I', 'Ó' => 'O', 'Ò' => 'O', 'Ỏ' => 'O', 'Õ' => 'O', 'Ọ' => 'O', 'Ô' => 'O', 'Ố' => 'O', 'Ổ' => 'O', 'Ỗ' => 'O', 'Ộ' => 'O', 'Ơ' => 'O', 'Ớ' => 'O', 'Ờ' => 'O', 'Ở' => 'O', 'Ỡ' => 'O', 'Ợ' => 'O', 'Ý' => 'Y', 'Ỳ' => 'Y', 'Ỷ' => 'Y', 'Ỹ' => 'Y', 'Ỵ' => 'Y')
        ;        
        $content = strtr ( $content, $trans ); // chuoi da duoc bo dau
        return $content;
	}
	
	static function sendRequestByCurl($url, $param = array())
	{		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		$fields_string = http_build_query($param);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);		
		$output = curl_exec($ch);
		curl_close($ch);		
		unset($ch);		
		if($output === false)
		{
			return "";
		}
		return $output;		
	}
	
	static function sendEmailV3($from,$displayname, $replyto, $to, $subject,$body_html, $file_attached = null)
	{
                $mail_config = "smtpout.secureserver.net;25;newsletter@maroads.net;chochet@123";
                
		if($replyto == "") $replyto = $from;

		$arr_config = explode(';',$mail_config);

		//$host = $arr_config[0] . ':' . $arr_config[1];

		$host = $arr_config[0];
		$port = $arr_config[1];

		$username = $arr_config[2];
		$password = $arr_config[3];
		try {
			$config = array(
//				'ssl' => 'tls',
                'auth' 		=> 'login',
                'username' 	=> $username,
                'password' 	=> $password,
				'port'		=> $port
			);

                $transport = new Zend_Mail_Transport_Smtp($host, $config);

                $mail = new Zend_Mail('utf-8');
                $mail->setType(Zend_Mime::MULTIPART_RELATED);
                $mail->setHeaderEncoding(Zend_Mime::ENCODING_BASE64);

                $mail->setReplyTo($replyto);
//			$mail->setBodyText(strip_tags($body_html));

                $mail->setFrom($from,$displayname);
                $mail->addTo($to);
                $mail->setSubject($subject);
	       	$mail->setBodyHtml($body_html);

			//$mail->setBodyHtml($body_html);
			$mail->send($transport);
//		    pre($result);
		}
		catch(Exception $ex)
		{
			return $ex->getMessage();
		}

		return "";

	}
	
	static function generateRandomWord($length=6)
	{	
		$list = 'ABCDEFGHIJKLMNOPQRST123456789';
		$rndWord = "";
		for($i=0; $i<$length; $i++ )
		{
			$index = rand(0, strlen($list) -1 );			
			$rndWord .= $list{$index};
		}
		return $rndWord;
	
	}
}
?>