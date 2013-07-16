<?php

/**
 * Utils_CommonFunction class
 * */
class Utils_CommonFunction
{
public static function removeUnicode($content){
		$marTViet=array("à","á","ạ","ả","ã","â","ầ","ấ","ậ","ẩ","ẫ","ă",
			"ằ","ắ","ặ","ẳ","ẵ","è","é","ẹ","ẻ","ẽ","ê","ề"
			,"ế","ệ","ể","ễ",
			"ì","í","ị","ỉ","ĩ",
			"ò","ó","ọ","ỏ","õ","ô","ồ","ố","ộ","ổ","ỗ","ơ"
			,"ờ","ớ","ợ","ở","ỡ",
			"ù","ú","ụ","ủ","ũ","ư","ừ","ứ","ự","ử","ữ",
			"ỳ","ý","ỵ","ỷ","ỹ",
			"đ",
			"À","Á","Ạ","Ả","Ã","Â","Ầ","Ấ","Ậ","Ẩ","Ẫ","Ă"
			,"Ằ","Ắ","Ặ","Ẳ","Ẵ",
			"È","É","Ẹ","Ẻ","Ẽ","Ê","Ề","Ế","Ệ","Ể","Ễ",
			"Ì","Í","Ị","Ỉ","Ĩ",
			"Ò","Ó","Ọ","Ỏ","Õ","Ô","Ồ","Ố","Ộ","Ổ","Ỗ","Ơ"
			,"Ờ","Ớ","Ợ","Ở","Ỡ",
			"Ù","Ú","Ụ","Ủ","Ũ","Ư","Ừ","Ứ","Ự","Ử","Ữ",
			"Ỳ","Ý","Ỵ","Ỷ","Ỹ",
			"Đ");
		
		$marKoDau=array("a","a","a","a","a","a","a","a","a","a","a"
			,"a","a","a","a","a","a",
			"e","e","e","e","e","e","e","e","e","e","e",
			"i","i","i","i","i",
			"o","o","o","o","o","o","o","o","o","o","o","o"
			,"o","o","o","o","
				o",
			"u","u","u","u","u","u","u","u","u","u","u",
			"y","y","y","y","y",
			"d",
			"A","A","A","A","A","A","A","A","A","A","A","A"
			,"A","A","A","A","A",
			"E","E","E","E","E","E","E","E","E","E","E",
			"I","I","I","I","I",
			"O","O","O","O","O","O","O","O","O","O","O","O"
			,"O","O","O","O","O",
			"U","U","U","U","U","U","U","U","U","U","U",
			"Y","Y","Y","Y","Y",
			"D");
		return str_replace($marTViet,$marKoDau,$content);
	}
	
	public static function timeFormat($date, $normalDate = false)
	{
			if ($normalDate) {
				$date = date("l, d M, Y h:ia", $date);
				$dayAndMonth = array(
						'Sunday' => 'Chủ Nhật',
						'Monday' => 'Thứ Hai',
						'Tuesday' => 'Thứ Ba',
						'Wednesday' => 'Thứ Tư',
						'Thursday' => 'Thứ Năm',
						'Friday' => 'Thứ Sáu',
						'Saturday' => 'Thứ Bảy',
						'Jan' => 'Tháng Một',
						'Feb' => 'Tháng Hai',
						'Mar' => 'Tháng Ba',
						'Apr' => 'Tháng Tư',
						'May' => 'Tháng Năm',
						'Jun' => 'Tháng Sáu',
						'Jul' => 'Tháng Bảy',
						'Aug' => 'Tháng Tám',
						'Sep' => 'Tháng Chín',
						'Oct' => 'Tháng Mười',
						'Nov' => 'Tháng Mười Một',
						'Dec' => 'Tháng Mười Hai',
				);
				return str_replace(array_keys($dayAndMonth), $dayAndMonth, $date);
			}
			if (empty($date)) {
				return 0;
			}
			$periods = array(
					"giây",
					"phút",
					"tiếng",
					"ngày"
			);
			$lengths = array(
					"60",
					"60",
					"24",
					"7"
			);
			$now = time();
			$unix_date = $date;
			// check validity of date
			if (empty($unix_date)) {
				return "Bad date";
			}
			if ($now == $unix_date) {
				return "mới đây";
			}
			// is it future date or past date
			if ($now > $unix_date) {
				$difference = $now - $unix_date;
				$tense = "trước";
			} else {
				/*$difference = $unix_date - $now;
				 $tense = "từ bây giờ";*/
			}
			for ($j = 0; $difference >= $lengths[$j] && $j < count($lengths) - 1; $j ++) {
				$difference /= $lengths[$j];
			}
			$difference = round($difference);
			if (date('Y') != date('Y', (float)$unix_date)) {
				$year = '-Y';
			}
			$datetime = date("H:i d-m$year", (float)$unix_date);
			if ($j == count($lengths) - 1) {
				if ($difference > 1)
					return $datetime;
				else
					return "hôm qua";
			} else {
				return "$difference $periods[$j] {$tense}";
			}
		}
		
		/*
		 *	transfer seo
		*/
		public static function vn_str_filter ($str){
			$unicode = array(
					'a'=>'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
					'd'=>'đ',
					'e'=>'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
					'i'=>'í|ì|ỉ|ĩ|ị',
					'o'=>'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
					'u'=>'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
					'y'=>'ý|ỳ|ỷ|ỹ|ỵ',
					'A'=>'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
					'D'=>'Đ',
					'E'=>'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
					'I'=>'Í|Ì|Ỉ|Ĩ|Ị',
					'O'=>'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
					'U'=>'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
					'Y'=>'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
					'' => '™|&|®|(|)'
			);
			foreach($unicode as $nonUnicode=>$uni){
				$str = preg_replace("/($uni)/i", $nonUnicode, $str);
			}
			return $str;
		}
		
		public static function getNameSeo($str) {
			$str = self::vn_str_filter(trim($str));
			$str = strtolower(preg_replace("/[^a-zA-Z0-9\s\-]/", "", $str));
			$str = str_replace(' ', '-', trim($str));
			return $str;
		}
}
