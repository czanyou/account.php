<?php
!defined('IN_VISION') && exit('Access Denied');

class ImageUtils {

	function resize($filename, $size_w, $size_h, $out = null, $type = null, $keep = false) {

		list($width, $height) = getimagesize($filename);

		if ($width <= 0 || $size_w <= 0) {
			return;
		}

		$top = 0;
		$left = 0;

		// 正方形
		if ($size_w == $size_h) {
			if ($width > $height) {
				$left = ($width - $height) / 2;
				$width = $height;
			} else {
				$top  = ($height - $width) / 2;
				$height = $width;
			}

		} else if ($keep) {
			$p1 = $size_h / $size_w;
			$p2 = $height / $width;
			if ($p1 > $p2) {
				$newWidth = $height / $p1;
				$left = ($width - $newWidth) / 2;
				$width = $newWidth;
				
			} else {
				$newHeight = $width * $p1;
				$top  = ($height - $newHeight) / 2;
				$height = $newHeight;
			}
		}

		// Load
		$thumb  = imagecreatetruecolor($size_w, $size_h);
		if ($type == "image/gif"){
			$source = imagecreatefromgif($filename);

		} else if ($type == "image/png" || $type == "image/x-png"){
			$source = imagecreatefrompng($filename);	

		}else{
			$source = imagecreatefromjpeg($filename);

		}
		
		// Resize
		imagecopyresized($thumb, $source, 0, 0, $left, $top, $size_w, $size_h, $width, $height);

		// Output
		if ($out == null) {
			imagejpeg($thumb);
		} else {
			imagejpeg($thumb, $out);
		}
		imagedestroy($thumb);
	}

	/**
	 * 生成验证码图片
	 * @param width 图片宽度
	 * @param height 图片高度
	 * @param how 验证码位数 
	 * @param fontsize 字体大小
	 */
	function createCheckImage($width = 15, $height = 20, $count = 5, $fontsize = 6) {

		$w = $count * $width; // 图片宽度
		$alpha = "abcdefghijkmnopqrstuvwxyz023456789"; //验证码内容1:字母
		$randcode = ""; // 验证码字符串初始化

		srand((double)microtime() * 1000000); //初始化随机数种子

		$image = ImageCreate($w, $height); //创建验证图片

		ImageFill($image, 0, 0, ImageColorAllocate($image, 255, 255, 255)); //填充背景色

		for ($i = 0; $i < 3; $i++) {
			$which = mt_rand(0, strlen($alpha) - 1); // 取哪个字符
			$code = substr($alpha, $which, 1); // 取字符
			$pos = $i * ($w / 3) + 8 + mt_rand(-5, 0); // 绘字符位置

			$color = ImageColorAllocate($image, mt_rand(160, 200), mt_rand(160, 200), mt_rand(160, 200)); // 字符随机颜色
			ImageChar($image, 3, $pos, mt_rand(0, 6), $code, $color); // 绘字符
		}

		/** 逐位产生随机字符 */
		for ($i = 0; $i < $count; $i++) {
			$which = mt_rand(0, strlen($alpha) - 1); // 取哪个字符
			$code = substr($alpha, $which, 1); // 取字符
			$pos = $i * $width + 8 + mt_rand(-5, 0); // 绘字符位置

			$color = ImageColorAllocate($image, mt_rand(0, 100), mt_rand(0, 100), mt_rand(0, 100)); // 字符随机颜色
			ImageChar($image, $fontsize, $pos, mt_rand(0, 6), $code, $color); // 绘字符
			$randcode .= $code; // 逐位加入验证码字符串
		}

		/** 添加干扰 */
		for ($i = 0; $i < 5; $i++) { // 绘背景干扰线
			$color = ImageColorAllocate($image, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255)); // 干扰线颜色
			ImageArc($image, mt_rand(-5, $w), mt_rand(-5, $height), mt_rand(20, 300), mt_rand(20, 200), 55, 44, $color); // 干扰线
		} 
		
		for ($i = 0; $i < $count * 4; $i++)	{ //绘背景干扰点
			$color = ImageColorAllocate($image, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255)); //干扰点颜色 
			ImageSetPixel($image, mt_rand(0, $w), mt_rand(0, $height), $color); // 干扰点
		}

		// 把验证码字符串写入 session
		session_start();
		$_SESSION['randcode'] = $randcode;

		/* 绘图结束 */
		Imagegif($image);
		ImageDestroy($image);
	}
}


$GLOBALS['emoji_maps'] = array(
			"\xe2\x98\x80"=>"<span class=\x22emoji emoji2600\x22></span>", "\xe2\x98\x81"=>"<span class=\x22emoji emoji2601\x22></span>", "\xe2\x98\x94"=>"<span class=\x22emoji emoji2614\x22></span>", "\xe2\x9b\x84"=>"<span class=\x22emoji emoji26c4\x22></span>", 
			"\xe2\x9a\xa1"=>"<span class=\x22emoji emoji26a1\x22></span>", "\xf0\x9f\x8c\x80"=>"<span class=\x22emoji emoji1f300\x22></span>", "\xf0\x9f\x8c\x81"=>"<span class=\x22emoji emoji1f301\x22></span>", "\xf0\x9f\x8c\x82"=>"<span class=\x22emoji emoji1f302\x22></span>", "\xf0\x9f\x8c\x83"=>"<span class=\x22emoji emoji1f303\x22></span>", 
			"\xf0\x9f\x8c\x84"=>"<span class=\x22emoji emoji1f304\x22></span>", "\xf0\x9f\x8c\x85"=>"<span class=\x22emoji emoji1f305\x22></span>", "\xf0\x9f\x8c\x86"=>"<span class=\x22emoji emoji1f306\x22></span>", "\xf0\x9f\x8c\x87"=>"<span class=\x22emoji emoji1f307\x22></span>", "\xf0\x9f\x8c\x88"=>"<span class=\x22emoji emoji1f308\x22></span>", 
			"\xe2\x9d\x84"=>"<span class=\x22emoji emoji2744\x22></span>", "\xe2\x9b\x85"=>"<span class=\x22emoji emoji26c5\x22></span>", "\xf0\x9f\x8c\x89"=>"<span class=\x22emoji emoji1f309\x22></span>", "\xf0\x9f\x8c\x8a"=>"<span class=\x22emoji emoji1f30a\x22></span>", "\xf0\x9f\x8c\x8b"=>"<span class=\x22emoji emoji1f30b\x22></span>", 
			"\xf0\x9f\x8c\x8c"=>"<span class=\x22emoji emoji1f30c\x22></span>", "\xf0\x9f\x8c\x8f"=>"<span class=\x22emoji emoji1f30f\x22></span>", "\xf0\x9f\x8c\x91"=>"<span class=\x22emoji emoji1f311\x22></span>", "\xf0\x9f\x8c\x94"=>"<span class=\x22emoji emoji1f314\x22></span>", "\xf0\x9f\x8c\x93"=>"<span class=\x22emoji emoji1f313\x22></span>", 
			"\xf0\x9f\x8c\x99"=>"<span class=\x22emoji emoji1f319\x22></span>", "\xf0\x9f\x8c\x95"=>"<span class=\x22emoji emoji1f315\x22></span>", "\xf0\x9f\x8c\x9b"=>"<span class=\x22emoji emoji1f31b\x22></span>", "\xf0\x9f\x8c\x9f"=>"<span class=\x22emoji emoji1f31f\x22></span>", "\xf0\x9f\x8c\xa0"=>"<span class=\x22emoji emoji1f320\x22></span>", 
			"\xf0\x9f\x95\x90"=>"<span class=\x22emoji emoji1f550\x22></span>", "\xf0\x9f\x95\x91"=>"<span class=\x22emoji emoji1f551\x22></span>", "\xf0\x9f\x95\x92"=>"<span class=\x22emoji emoji1f552\x22></span>", "\xf0\x9f\x95\x93"=>"<span class=\x22emoji emoji1f553\x22></span>", "\xf0\x9f\x95\x94"=>"<span class=\x22emoji emoji1f554\x22></span>", 
			"\xf0\x9f\x95\x95"=>"<span class=\x22emoji emoji1f555\x22></span>", "\xf0\x9f\x95\x96"=>"<span class=\x22emoji emoji1f556\x22></span>", "\xf0\x9f\x95\x97"=>"<span class=\x22emoji emoji1f557\x22></span>", "\xf0\x9f\x95\x98"=>"<span class=\x22emoji emoji1f558\x22></span>", "\xf0\x9f\x95\x99"=>"<span class=\x22emoji emoji1f559\x22></span>", 
			"\xf0\x9f\x95\x9a"=>"<span class=\x22emoji emoji1f55a\x22></span>", "\xf0\x9f\x95\x9b"=>"<span class=\x22emoji emoji1f55b\x22></span>", "\xe2\x8c\x9a"=>"<span class=\x22emoji emoji231a\x22></span>", "\xe2\x8c\x9b"=>"<span class=\x22emoji emoji231b\x22></span>", "\xe2\x8f\xb0"=>"<span class=\x22emoji emoji23f0\x22></span>", 
			"\xe2\x8f\xb3"=>"<span class=\x22emoji emoji23f3\x22></span>", "\xe2\x99\x88"=>"<span class=\x22emoji emoji2648\x22></span>", "\xe2\x99\x89"=>"<span class=\x22emoji emoji2649\x22></span>", "\xe2\x99\x8a"=>"<span class=\x22emoji emoji264a\x22></span>", "\xe2\x99\x8b"=>"<span class=\x22emoji emoji264b\x22></span>", 
			"\xe2\x99\x8c"=>"<span class=\x22emoji emoji264c\x22></span>", "\xe2\x99\x8d"=>"<span class=\x22emoji emoji264d\x22></span>", "\xe2\x99\x8e"=>"<span class=\x22emoji emoji264e\x22></span>", "\xe2\x99\x8f"=>"<span class=\x22emoji emoji264f\x22></span>", "\xe2\x99\x90"=>"<span class=\x22emoji emoji2650\x22></span>", 
			"\xe2\x99\x91"=>"<span class=\x22emoji emoji2651\x22></span>", "\xe2\x99\x92"=>"<span class=\x22emoji emoji2652\x22></span>", "\xe2\x99\x93"=>"<span class=\x22emoji emoji2653\x22></span>", "\xe2\x9b\x8e"=>"<span class=\x22emoji emoji26ce\x22></span>", "\xf0\x9f\x8d\x80"=>"<span class=\x22emoji emoji1f340\x22></span>", 
			"\xf0\x9f\x8c\xb7"=>"<span class=\x22emoji emoji1f337\x22></span>", "\xf0\x9f\x8c\xb1"=>"<span class=\x22emoji emoji1f331\x22></span>", "\xf0\x9f\x8d\x81"=>"<span class=\x22emoji emoji1f341\x22></span>", "\xf0\x9f\x8c\xb8"=>"<span class=\x22emoji emoji1f338\x22></span>", "\xf0\x9f\x8c\xb9"=>"<span class=\x22emoji emoji1f339\x22></span>", 
			"\xf0\x9f\x8d\x82"=>"<span class=\x22emoji emoji1f342\x22></span>", "\xf0\x9f\x8d\x83"=>"<span class=\x22emoji emoji1f343\x22></span>", "\xf0\x9f\x8c\xba"=>"<span class=\x22emoji emoji1f33a\x22></span>", "\xf0\x9f\x8c\xbb"=>"<span class=\x22emoji emoji1f33b\x22></span>", "\xf0\x9f\x8c\xb4"=>"<span class=\x22emoji emoji1f334\x22></span>", 
			"\xf0\x9f\x8c\xb5"=>"<span class=\x22emoji emoji1f335\x22></span>", "\xf0\x9f\x8c\xbe"=>"<span class=\x22emoji emoji1f33e\x22></span>", "\xf0\x9f\x8c\xbd"=>"<span class=\x22emoji emoji1f33d\x22></span>", "\xf0\x9f\x8d\x84"=>"<span class=\x22emoji emoji1f344\x22></span>", "\xf0\x9f\x8c\xb0"=>"<span class=\x22emoji emoji1f330\x22></span>", 
			"\xf0\x9f\x8c\xbc"=>"<span class=\x22emoji emoji1f33c\x22></span>", "\xf0\x9f\x8c\xbf"=>"<span class=\x22emoji emoji1f33f\x22></span>", "\xf0\x9f\x8d\x92"=>"<span class=\x22emoji emoji1f352\x22></span>", "\xf0\x9f\x8d\x8c"=>"<span class=\x22emoji emoji1f34c\x22></span>", "\xf0\x9f\x8d\x8e"=>"<span class=\x22emoji emoji1f34e\x22></span>", 
			"\xf0\x9f\x8d\x8a"=>"<span class=\x22emoji emoji1f34a\x22></span>", "\xf0\x9f\x8d\x93"=>"<span class=\x22emoji emoji1f353\x22></span>", "\xf0\x9f\x8d\x89"=>"<span class=\x22emoji emoji1f349\x22></span>", "\xf0\x9f\x8d\x85"=>"<span class=\x22emoji emoji1f345\x22></span>", "\xf0\x9f\x8d\x86"=>"<span class=\x22emoji emoji1f346\x22></span>", 
			"\xf0\x9f\x8d\x88"=>"<span class=\x22emoji emoji1f348\x22></span>", "\xf0\x9f\x8d\x8d"=>"<span class=\x22emoji emoji1f34d\x22></span>", "\xf0\x9f\x8d\x87"=>"<span class=\x22emoji emoji1f347\x22></span>", "\xf0\x9f\x8d\x91"=>"<span class=\x22emoji emoji1f351\x22></span>", "\xf0\x9f\x8d\x8f"=>"<span class=\x22emoji emoji1f34f\x22></span>", 
			"\xf0\x9f\x91\x80"=>"<span class=\x22emoji emoji1f440\x22></span>", "\xf0\x9f\x91\x82"=>"<span class=\x22emoji emoji1f442\x22></span>", "\xf0\x9f\x91\x83"=>"<span class=\x22emoji emoji1f443\x22></span>", "\xf0\x9f\x91\x84"=>"<span class=\x22emoji emoji1f444\x22></span>", "\xf0\x9f\x91\x85"=>"<span class=\x22emoji emoji1f445\x22></span>", 
			"\xf0\x9f\x92\x84"=>"<span class=\x22emoji emoji1f484\x22></span>", "\xf0\x9f\x92\x85"=>"<span class=\x22emoji emoji1f485\x22></span>", "\xf0\x9f\x92\x86"=>"<span class=\x22emoji emoji1f486\x22></span>", "\xf0\x9f\x92\x87"=>"<span class=\x22emoji emoji1f487\x22></span>", "\xf0\x9f\x92\x88"=>"<span class=\x22emoji emoji1f488\x22></span>", 
			"\xf0\x9f\x91\xa4"=>"<span class=\x22emoji emoji1f464\x22></span>", "\xf0\x9f\x91\xa6"=>"<span class=\x22emoji emoji1f466\x22></span>", "\xf0\x9f\x91\xa7"=>"<span class=\x22emoji emoji1f467\x22></span>", "\xf0\x9f\x91\xa8"=>"<span class=\x22emoji emoji1f468\x22></span>", "\xf0\x9f\x91\xa9"=>"<span class=\x22emoji emoji1f469\x22></span>", 
			"\xf0\x9f\x91\xaa"=>"<span class=\x22emoji emoji1f46a\x22></span>", "\xf0\x9f\x91\xab"=>"<span class=\x22emoji emoji1f46b\x22></span>", "\xf0\x9f\x91\xae"=>"<span class=\x22emoji emoji1f46e\x22></span>", "\xf0\x9f\x91\xaf"=>"<span class=\x22emoji emoji1f46f\x22></span>", "\xf0\x9f\x91\xb0"=>"<span class=\x22emoji emoji1f470\x22></span>", 
			"\xf0\x9f\x91\xb1"=>"<span class=\x22emoji emoji1f471\x22></span>", "\xf0\x9f\x91\xb2"=>"<span class=\x22emoji emoji1f472\x22></span>", "\xf0\x9f\x91\xb3"=>"<span class=\x22emoji emoji1f473\x22></span>", "\xf0\x9f\x91\xb4"=>"<span class=\x22emoji emoji1f474\x22></span>", "\xf0\x9f\x91\xb5"=>"<span class=\x22emoji emoji1f475\x22></span>", 
			"\xf0\x9f\x91\xb6"=>"<span class=\x22emoji emoji1f476\x22></span>", "\xf0\x9f\x91\xb7"=>"<span class=\x22emoji emoji1f477\x22></span>", "\xf0\x9f\x91\xb8"=>"<span class=\x22emoji emoji1f478\x22></span>", "\xf0\x9f\x91\xb9"=>"<span class=\x22emoji emoji1f479\x22></span>", "\xf0\x9f\x91\xba"=>"<span class=\x22emoji emoji1f47a\x22></span>", 
			"\xf0\x9f\x91\xbb"=>"<span class=\x22emoji emoji1f47b\x22></span>", "\xf0\x9f\x91\xbc"=>"<span class=\x22emoji emoji1f47c\x22></span>", "\xf0\x9f\x91\xbd"=>"<span class=\x22emoji emoji1f47d\x22></span>", "\xf0\x9f\x91\xbe"=>"<span class=\x22emoji emoji1f47e\x22></span>", "\xf0\x9f\x91\xbf"=>"<span class=\x22emoji emoji1f47f\x22></span>", 
			"\xf0\x9f\x92\x80"=>"<span class=\x22emoji emoji1f480\x22></span>", "\xf0\x9f\x92\x81"=>"<span class=\x22emoji emoji1f481\x22></span>", "\xf0\x9f\x92\x82"=>"<span class=\x22emoji emoji1f482\x22></span>", "\xf0\x9f\x92\x83"=>"<span class=\x22emoji emoji1f483\x22></span>", "\xf0\x9f\x90\x8c"=>"<span class=\x22emoji emoji1f40c\x22></span>", 
			"\xf0\x9f\x90\x8d"=>"<span class=\x22emoji emoji1f40d\x22></span>", "\xf0\x9f\x90\x8e"=>"<span class=\x22emoji emoji1f40e\x22></span>", "\xf0\x9f\x90\x94"=>"<span class=\x22emoji emoji1f414\x22></span>", "\xf0\x9f\x90\x97"=>"<span class=\x22emoji emoji1f417\x22></span>", "\xf0\x9f\x90\xab"=>"<span class=\x22emoji emoji1f42b\x22></span>", 
			"\xf0\x9f\x90\x98"=>"<span class=\x22emoji emoji1f418\x22></span>", "\xf0\x9f\x90\xa8"=>"<span class=\x22emoji emoji1f428\x22></span>", "\xf0\x9f\x90\x92"=>"<span class=\x22emoji emoji1f412\x22></span>", "\xf0\x9f\x90\x91"=>"<span class=\x22emoji emoji1f411\x22></span>", "\xf0\x9f\x90\x99"=>"<span class=\x22emoji emoji1f419\x22></span>", 
			"\xf0\x9f\x90\x9a"=>"<span class=\x22emoji emoji1f41a\x22></span>", "\xf0\x9f\x90\x9b"=>"<span class=\x22emoji emoji1f41b\x22></span>", "\xf0\x9f\x90\x9c"=>"<span class=\x22emoji emoji1f41c\x22></span>", "\xf0\x9f\x90\x9d"=>"<span class=\x22emoji emoji1f41d\x22></span>", "\xf0\x9f\x90\x9e"=>"<span class=\x22emoji emoji1f41e\x22></span>", 
			"\xf0\x9f\x90\xa0"=>"<span class=\x22emoji emoji1f420\x22></span>", "\xf0\x9f\x90\xa1"=>"<span class=\x22emoji emoji1f421\x22></span>", "\xf0\x9f\x90\xa2"=>"<span class=\x22emoji emoji1f422\x22></span>", "\xf0\x9f\x90\xa4"=>"<span class=\x22emoji emoji1f424\x22></span>", "\xf0\x9f\x90\xa5"=>"<span class=\x22emoji emoji1f425\x22></span>", 
			"\xf0\x9f\x90\xa6"=>"<span class=\x22emoji emoji1f426\x22></span>", "\xf0\x9f\x90\xa3"=>"<span class=\x22emoji emoji1f423\x22></span>", "\xf0\x9f\x90\xa7"=>"<span class=\x22emoji emoji1f427\x22></span>", "\xf0\x9f\x90\xa9"=>"<span class=\x22emoji emoji1f429\x22></span>", "\xf0\x9f\x90\x9f"=>"<span class=\x22emoji emoji1f41f\x22></span>", 
			"\xf0\x9f\x90\xac"=>"<span class=\x22emoji emoji1f42c\x22></span>", "\xf0\x9f\x90\xad"=>"<span class=\x22emoji emoji1f42d\x22></span>", "\xf0\x9f\x90\xaf"=>"<span class=\x22emoji emoji1f42f\x22></span>", "\xf0\x9f\x90\xb1"=>"<span class=\x22emoji emoji1f431\x22></span>", "\xf0\x9f\x90\xb3"=>"<span class=\x22emoji emoji1f433\x22></span>", 
			"\xf0\x9f\x90\xb4"=>"<span class=\x22emoji emoji1f434\x22></span>", "\xf0\x9f\x90\xb5"=>"<span class=\x22emoji emoji1f435\x22></span>", "\xf0\x9f\x90\xb6"=>"<span class=\x22emoji emoji1f436\x22></span>", "\xf0\x9f\x90\xb7"=>"<span class=\x22emoji emoji1f437\x22></span>", "\xf0\x9f\x90\xbb"=>"<span class=\x22emoji emoji1f43b\x22></span>", 
			"\xf0\x9f\x90\xb9"=>"<span class=\x22emoji emoji1f439\x22></span>", "\xf0\x9f\x90\xba"=>"<span class=\x22emoji emoji1f43a\x22></span>", "\xf0\x9f\x90\xae"=>"<span class=\x22emoji emoji1f42e\x22></span>", "\xf0\x9f\x90\xb0"=>"<span class=\x22emoji emoji1f430\x22></span>", "\xf0\x9f\x90\xb8"=>"<span class=\x22emoji emoji1f438\x22></span>", 
			"\xf0\x9f\x90\xbe"=>"<span class=\x22emoji emoji1f43e\x22></span>", "\xf0\x9f\x90\xb2"=>"<span class=\x22emoji emoji1f432\x22></span>", "\xf0\x9f\x90\xbc"=>"<span class=\x22emoji emoji1f43c\x22></span>", "\xf0\x9f\x90\xbd"=>"<span class=\x22emoji emoji1f43d\x22></span>", "\xf0\x9f\x98\xa0"=>"<span class=\x22emoji emoji1f620\x22></span>", 
			"\xf0\x9f\x98\xa9"=>"<span class=\x22emoji emoji1f629\x22></span>", "\xf0\x9f\x98\xb2"=>"<span class=\x22emoji emoji1f632\x22></span>", "\xf0\x9f\x98\x9e"=>"<span class=\x22emoji emoji1f61e\x22></span>", "\xf0\x9f\x98\xb5"=>"<span class=\x22emoji emoji1f635\x22></span>", "\xf0\x9f\x98\xb0"=>"<span class=\x22emoji emoji1f630\x22></span>", 
			"\xf0\x9f\x98\x92"=>"<span class=\x22emoji emoji1f612\x22></span>", "\xf0\x9f\x98\x8d"=>"<span class=\x22emoji emoji1f60d\x22></span>", "\xf0\x9f\x98\xa4"=>"<span class=\x22emoji emoji1f624\x22></span>", "\xf0\x9f\x98\x9c"=>"<span class=\x22emoji emoji1f61c\x22></span>", "\xf0\x9f\x98\x9d"=>"<span class=\x22emoji emoji1f61d\x22></span>", 
			"\xf0\x9f\x98\x8b"=>"<span class=\x22emoji emoji1f60b\x22></span>", "\xf0\x9f\x98\x98"=>"<span class=\x22emoji emoji1f618\x22></span>", "\xf0\x9f\x98\x9a"=>"<span class=\x22emoji emoji1f61a\x22></span>", "\xf0\x9f\x98\xb7"=>"<span class=\x22emoji emoji1f637\x22></span>", "\xf0\x9f\x98\xb3"=>"<span class=\x22emoji emoji1f633\x22></span>", 
			"\xf0\x9f\x98\x83"=>"<span class=\x22emoji emoji1f603\x22></span>", "\xf0\x9f\x98\x85"=>"<span class=\x22emoji emoji1f605\x22></span>", "\xf0\x9f\x98\x86"=>"<span class=\x22emoji emoji1f606\x22></span>", "\xf0\x9f\x98\x81"=>"<span class=\x22emoji emoji1f601\x22></span>", "\xf0\x9f\x98\x82"=>"<span class=\x22emoji emoji1f602\x22></span>", 
			"\xf0\x9f\x98\x8a"=>"<span class=\x22emoji emoji1f60a\x22></span>", "\xe2\x98\xba"=>"<span class=\x22emoji emoji263a\x22></span>", "\xf0\x9f\x98\x84"=>"<span class=\x22emoji emoji1f604\x22></span>", "\xf0\x9f\x98\xa2"=>"<span class=\x22emoji emoji1f622\x22></span>", "\xf0\x9f\x98\xad"=>"<span class=\x22emoji emoji1f62d\x22></span>", 
			"\xf0\x9f\x98\xa8"=>"<span class=\x22emoji emoji1f628\x22></span>", "\xf0\x9f\x98\xa3"=>"<span class=\x22emoji emoji1f623\x22></span>", "\xf0\x9f\x98\xa1"=>"<span class=\x22emoji emoji1f621\x22></span>", "\xf0\x9f\x98\x8c"=>"<span class=\x22emoji emoji1f60c\x22></span>", "\xf0\x9f\x98\x96"=>"<span class=\x22emoji emoji1f616\x22></span>", 
			"\xf0\x9f\x98\x94"=>"<span class=\x22emoji emoji1f614\x22></span>", "\xf0\x9f\x98\xb1"=>"<span class=\x22emoji emoji1f631\x22></span>", "\xf0\x9f\x98\xaa"=>"<span class=\x22emoji emoji1f62a\x22></span>", "\xf0\x9f\x98\x8f"=>"<span class=\x22emoji emoji1f60f\x22></span>", "\xf0\x9f\x98\x93"=>"<span class=\x22emoji emoji1f613\x22></span>", 
			"\xf0\x9f\x98\xa5"=>"<span class=\x22emoji emoji1f625\x22></span>", "\xf0\x9f\x98\xab"=>"<span class=\x22emoji emoji1f62b\x22></span>", "\xf0\x9f\x98\x89"=>"<span class=\x22emoji emoji1f609\x22></span>", "\xf0\x9f\x98\xba"=>"<span class=\x22emoji emoji1f63a\x22></span>", "\xf0\x9f\x98\xb8"=>"<span class=\x22emoji emoji1f638\x22></span>", 
			"\xf0\x9f\x98\xb9"=>"<span class=\x22emoji emoji1f639\x22></span>", "\xf0\x9f\x98\xbd"=>"<span class=\x22emoji emoji1f63d\x22></span>", "\xf0\x9f\x98\xbb"=>"<span class=\x22emoji emoji1f63b\x22></span>", "\xf0\x9f\x98\xbf"=>"<span class=\x22emoji emoji1f63f\x22></span>", "\xf0\x9f\x98\xbe"=>"<span class=\x22emoji emoji1f63e\x22></span>", 
			"\xf0\x9f\x98\xbc"=>"<span class=\x22emoji emoji1f63c\x22></span>", "\xf0\x9f\x99\x80"=>"<span class=\x22emoji emoji1f640\x22></span>", "\xf0\x9f\x99\x85"=>"<span class=\x22emoji emoji1f645\x22></span>", "\xf0\x9f\x99\x86"=>"<span class=\x22emoji emoji1f646\x22></span>", "\xf0\x9f\x99\x87"=>"<span class=\x22emoji emoji1f647\x22></span>", 
			"\xf0\x9f\x99\x88"=>"<span class=\x22emoji emoji1f648\x22></span>", "\xf0\x9f\x99\x8a"=>"<span class=\x22emoji emoji1f64a\x22></span>", "\xf0\x9f\x99\x89"=>"<span class=\x22emoji emoji1f649\x22></span>", "\xf0\x9f\x99\x8b"=>"<span class=\x22emoji emoji1f64b\x22></span>", "\xf0\x9f\x99\x8c"=>"<span class=\x22emoji emoji1f64c\x22></span>", 
			"\xf0\x9f\x99\x8d"=>"<span class=\x22emoji emoji1f64d\x22></span>", "\xf0\x9f\x99\x8e"=>"<span class=\x22emoji emoji1f64e\x22></span>", "\xf0\x9f\x99\x8f"=>"<span class=\x22emoji emoji1f64f\x22></span>", "\xf0\x9f\x8f\xa0"=>"<span class=\x22emoji emoji1f3e0\x22></span>", "\xf0\x9f\x8f\xa1"=>"<span class=\x22emoji emoji1f3e1\x22></span>", 
			"\xf0\x9f\x8f\xa2"=>"<span class=\x22emoji emoji1f3e2\x22></span>", "\xf0\x9f\x8f\xa3"=>"<span class=\x22emoji emoji1f3e3\x22></span>", "\xf0\x9f\x8f\xa5"=>"<span class=\x22emoji emoji1f3e5\x22></span>", "\xf0\x9f\x8f\xa6"=>"<span class=\x22emoji emoji1f3e6\x22></span>", "\xf0\x9f\x8f\xa7"=>"<span class=\x22emoji emoji1f3e7\x22></span>", 
			"\xf0\x9f\x8f\xa8"=>"<span class=\x22emoji emoji1f3e8\x22></span>", "\xf0\x9f\x8f\xa9"=>"<span class=\x22emoji emoji1f3e9\x22></span>", "\xf0\x9f\x8f\xaa"=>"<span class=\x22emoji emoji1f3ea\x22></span>", "\xf0\x9f\x8f\xab"=>"<span class=\x22emoji emoji1f3eb\x22></span>", "\xe2\x9b\xaa"=>"<span class=\x22emoji emoji26ea\x22></span>", 
			"\xe2\x9b\xb2"=>"<span class=\x22emoji emoji26f2\x22></span>", "\xf0\x9f\x8f\xac"=>"<span class=\x22emoji emoji1f3ec\x22></span>", "\xf0\x9f\x8f\xaf"=>"<span class=\x22emoji emoji1f3ef\x22></span>", "\xf0\x9f\x8f\xb0"=>"<span class=\x22emoji emoji1f3f0\x22></span>", "\xf0\x9f\x8f\xad"=>"<span class=\x22emoji emoji1f3ed\x22></span>", 
			"\xe2\x9a\x93"=>"<span class=\x22emoji emoji2693\x22></span>", "\xf0\x9f\x8f\xae"=>"<span class=\x22emoji emoji1f3ee\x22></span>", "\xf0\x9f\x97\xbb"=>"<span class=\x22emoji emoji1f5fb\x22></span>", "\xf0\x9f\x97\xbc"=>"<span class=\x22emoji emoji1f5fc\x22></span>", "\xf0\x9f\x97\xbd"=>"<span class=\x22emoji emoji1f5fd\x22></span>", 
			"\xf0\x9f\x97\xbe"=>"<span class=\x22emoji emoji1f5fe\x22></span>", "\xf0\x9f\x97\xbf"=>"<span class=\x22emoji emoji1f5ff\x22></span>", "\xf0\x9f\x91\x9e"=>"<span class=\x22emoji emoji1f45e\x22></span>", "\xf0\x9f\x91\x9f"=>"<span class=\x22emoji emoji1f45f\x22></span>", "\xf0\x9f\x91\xa0"=>"<span class=\x22emoji emoji1f460\x22></span>", 
			"\xf0\x9f\x91\xa1"=>"<span class=\x22emoji emoji1f461\x22></span>", "\xf0\x9f\x91\xa2"=>"<span class=\x22emoji emoji1f462\x22></span>", "\xf0\x9f\x91\xa3"=>"<span class=\x22emoji emoji1f463\x22></span>", "\xf0\x9f\x91\x93"=>"<span class=\x22emoji emoji1f453\x22></span>", "\xf0\x9f\x91\x95"=>"<span class=\x22emoji emoji1f455\x22></span>", 
			"\xf0\x9f\x91\x96"=>"<span class=\x22emoji emoji1f456\x22></span>", "\xf0\x9f\x91\x91"=>"<span class=\x22emoji emoji1f451\x22></span>", "\xf0\x9f\x91\x94"=>"<span class=\x22emoji emoji1f454\x22></span>", "\xf0\x9f\x91\x92"=>"<span class=\x22emoji emoji1f452\x22></span>", "\xf0\x9f\x91\x97"=>"<span class=\x22emoji emoji1f457\x22></span>", 
			"\xf0\x9f\x91\x98"=>"<span class=\x22emoji emoji1f458\x22></span>", "\xf0\x9f\x91\x99"=>"<span class=\x22emoji emoji1f459\x22></span>", "\xf0\x9f\x91\x9a"=>"<span class=\x22emoji emoji1f45a\x22></span>", "\xf0\x9f\x91\x9b"=>"<span class=\x22emoji emoji1f45b\x22></span>", "\xf0\x9f\x91\x9c"=>"<span class=\x22emoji emoji1f45c\x22></span>", 
			"\xf0\x9f\x91\x9d"=>"<span class=\x22emoji emoji1f45d\x22></span>", "\xf0\x9f\x92\xb0"=>"<span class=\x22emoji emoji1f4b0\x22></span>", "\xf0\x9f\x92\xb1"=>"<span class=\x22emoji emoji1f4b1\x22></span>", "\xf0\x9f\x92\xb9"=>"<span class=\x22emoji emoji1f4b9\x22></span>", "\xf0\x9f\x92\xb2"=>"<span class=\x22emoji emoji1f4b2\x22></span>", 
			"\xf0\x9f\x92\xb3"=>"<span class=\x22emoji emoji1f4b3\x22></span>", "\xf0\x9f\x92\xb4"=>"<span class=\x22emoji emoji1f4b4\x22></span>", "\xf0\x9f\x92\xb5"=>"<span class=\x22emoji emoji1f4b5\x22></span>", "\xf0\x9f\x92\xb8"=>"<span class=\x22emoji emoji1f4b8\x22></span>", "\xf0\x9f\x87\xa8\xf0\x9f\x87\xb3"=>"<span class=\x22emoji emoji1f1e81f1f3\x22></span>", 
			"\xf0\x9f\x87\xa9\xf0\x9f\x87\xaa"=>"<span class=\x22emoji emoji1f1e91f1ea\x22></span>", "\xf0\x9f\x87\xaa\xf0\x9f\x87\xb8"=>"<span class=\x22emoji emoji1f1ea1f1f8\x22></span>", "\xf0\x9f\x87\xab\xf0\x9f\x87\xb7"=>"<span class=\x22emoji emoji1f1eb1f1f7\x22></span>", "\xf0\x9f\x87\xac\xf0\x9f\x87\xa7"=>"<span class=\x22emoji emoji1f1ec1f1e7\x22></span>", "\xf0\x9f\x87\xae\xf0\x9f\x87\xb9"=>"<span class=\x22emoji emoji1f1ee1f1f9\x22></span>", 
			"\xf0\x9f\x87\xaf\xf0\x9f\x87\xb5"=>"<span class=\x22emoji emoji1f1ef1f1f5\x22></span>", "\xf0\x9f\x87\xb0\xf0\x9f\x87\xb7"=>"<span class=\x22emoji emoji1f1f01f1f7\x22></span>", "\xf0\x9f\x87\xb7\xf0\x9f\x87\xba"=>"<span class=\x22emoji emoji1f1f71f1fa\x22></span>", "\xf0\x9f\x87\xba\xf0\x9f\x87\xb8"=>"<span class=\x22emoji emoji1f1fa1f1f8\x22></span>", "\xf0\x9f\x94\xa5"=>"<span class=\x22emoji emoji1f525\x22></span>", 
			"\xf0\x9f\x94\xa6"=>"<span class=\x22emoji emoji1f526\x22></span>", "\xf0\x9f\x94\xa7"=>"<span class=\x22emoji emoji1f527\x22></span>", "\xf0\x9f\x94\xa8"=>"<span class=\x22emoji emoji1f528\x22></span>", "\xf0\x9f\x94\xa9"=>"<span class=\x22emoji emoji1f529\x22></span>", "\xf0\x9f\x94\xaa"=>"<span class=\x22emoji emoji1f52a\x22></span>", 
			"\xf0\x9f\x94\xab"=>"<span class=\x22emoji emoji1f52b\x22></span>", "\xf0\x9f\x94\xae"=>"<span class=\x22emoji emoji1f52e\x22></span>", "\xf0\x9f\x94\xaf"=>"<span class=\x22emoji emoji1f52f\x22></span>", "\xf0\x9f\x94\xb0"=>"<span class=\x22emoji emoji1f530\x22></span>", "\xf0\x9f\x94\xb1"=>"<span class=\x22emoji emoji1f531\x22></span>", 
			"\xf0\x9f\x92\x89"=>"<span class=\x22emoji emoji1f489\x22></span>", "\xf0\x9f\x92\x8a"=>"<span class=\x22emoji emoji1f48a\x22></span>", "\xf0\x9f\x85\xb0"=>"<span class=\x22emoji emoji1f170\x22></span>", "\xf0\x9f\x85\xb1"=>"<span class=\x22emoji emoji1f171\x22></span>", "\xf0\x9f\x86\x8e"=>"<span class=\x22emoji emoji1f18e\x22></span>", 
			"\xf0\x9f\x85\xbe"=>"<span class=\x22emoji emoji1f17e\x22></span>", "\xf0\x9f\x8e\x80"=>"<span class=\x22emoji emoji1f380\x22></span>", "\xf0\x9f\x8e\x81"=>"<span class=\x22emoji emoji1f381\x22></span>", "\xf0\x9f\x8e\x82"=>"<span class=\x22emoji emoji1f382\x22></span>", "\xf0\x9f\x8e\x84"=>"<span class=\x22emoji emoji1f384\x22></span>", 
			"\xf0\x9f\x8e\x85"=>"<span class=\x22emoji emoji1f385\x22></span>", "\xf0\x9f\x8e\x8c"=>"<span class=\x22emoji emoji1f38c\x22></span>", "\xf0\x9f\x8e\x86"=>"<span class=\x22emoji emoji1f386\x22></span>", "\xf0\x9f\x8e\x88"=>"<span class=\x22emoji emoji1f388\x22></span>", "\xf0\x9f\x8e\x89"=>"<span class=\x22emoji emoji1f389\x22></span>", 
			"\xf0\x9f\x8e\x8d"=>"<span class=\x22emoji emoji1f38d\x22></span>", "\xf0\x9f\x8e\x8e"=>"<span class=\x22emoji emoji1f38e\x22></span>", "\xf0\x9f\x8e\x93"=>"<span class=\x22emoji emoji1f393\x22></span>", "\xf0\x9f\x8e\x92"=>"<span class=\x22emoji emoji1f392\x22></span>", "\xf0\x9f\x8e\x8f"=>"<span class=\x22emoji emoji1f38f\x22></span>", 
			"\xf0\x9f\x8e\x87"=>"<span class=\x22emoji emoji1f387\x22></span>", "\xf0\x9f\x8e\x90"=>"<span class=\x22emoji emoji1f390\x22></span>", "\xf0\x9f\x8e\x83"=>"<span class=\x22emoji emoji1f383\x22></span>", "\xf0\x9f\x8e\x8a"=>"<span class=\x22emoji emoji1f38a\x22></span>", "\xf0\x9f\x8e\x8b"=>"<span class=\x22emoji emoji1f38b\x22></span>", 
			"\xf0\x9f\x8e\x91"=>"<span class=\x22emoji emoji1f391\x22></span>", "\xf0\x9f\x93\x9f"=>"<span class=\x22emoji emoji1f4df\x22></span>", "\xe2\x98\x8e"=>"<span class=\x22emoji emoji260e\x22></span>", "\xf0\x9f\x93\x9e"=>"<span class=\x22emoji emoji1f4de\x22></span>", "\xf0\x9f\x93\xb1"=>"<span class=\x22emoji emoji1f4f1\x22></span>", 
			"\xf0\x9f\x93\xb2"=>"<span class=\x22emoji emoji1f4f2\x22></span>", "\xf0\x9f\x93\x9d"=>"<span class=\x22emoji emoji1f4dd\x22></span>", "\xf0\x9f\x93\xa0"=>"<span class=\x22emoji emoji1f4e0\x22></span>", "\xe2\x9c\x89"=>"<span class=\x22emoji emoji2709\x22></span>", "\xf0\x9f\x93\xa8"=>"<span class=\x22emoji emoji1f4e8\x22></span>", 
			"\xf0\x9f\x93\xa9"=>"<span class=\x22emoji emoji1f4e9\x22></span>", "\xf0\x9f\x93\xaa"=>"<span class=\x22emoji emoji1f4ea\x22></span>", "\xf0\x9f\x93\xab"=>"<span class=\x22emoji emoji1f4eb\x22></span>", "\xf0\x9f\x93\xae"=>"<span class=\x22emoji emoji1f4ee\x22></span>", "\xf0\x9f\x93\xb0"=>"<span class=\x22emoji emoji1f4f0\x22></span>", 
			"\xf0\x9f\x93\xa2"=>"<span class=\x22emoji emoji1f4e2\x22></span>", "\xf0\x9f\x93\xa3"=>"<span class=\x22emoji emoji1f4e3\x22></span>", "\xf0\x9f\x93\xa1"=>"<span class=\x22emoji emoji1f4e1\x22></span>", "\xf0\x9f\x93\xa4"=>"<span class=\x22emoji emoji1f4e4\x22></span>", "\xf0\x9f\x93\xa5"=>"<span class=\x22emoji emoji1f4e5\x22></span>", 
			"\xf0\x9f\x93\xa6"=>"<span class=\x22emoji emoji1f4e6\x22></span>", "\xf0\x9f\x93\xa7"=>"<span class=\x22emoji emoji1f4e7\x22></span>", "\xf0\x9f\x94\xa0"=>"<span class=\x22emoji emoji1f520\x22></span>", "\xf0\x9f\x94\xa1"=>"<span class=\x22emoji emoji1f521\x22></span>", "\xf0\x9f\x94\xa2"=>"<span class=\x22emoji emoji1f522\x22></span>", 
			"\xf0\x9f\x94\xa3"=>"<span class=\x22emoji emoji1f523\x22></span>", "\xf0\x9f\x94\xa4"=>"<span class=\x22emoji emoji1f524\x22></span>", "\xe2\x9c\x92"=>"<span class=\x22emoji emoji2712\x22></span>", "\xf0\x9f\x92\xba"=>"<span class=\x22emoji emoji1f4ba\x22></span>", "\xf0\x9f\x92\xbb"=>"<span class=\x22emoji emoji1f4bb\x22></span>", 
			"\xe2\x9c\x8f"=>"<span class=\x22emoji emoji270f\x22></span>", "\xf0\x9f\x93\x8e"=>"<span class=\x22emoji emoji1f4ce\x22></span>", "\xf0\x9f\x92\xbc"=>"<span class=\x22emoji emoji1f4bc\x22></span>", "\xf0\x9f\x92\xbd"=>"<span class=\x22emoji emoji1f4bd\x22></span>", "\xf0\x9f\x92\xbe"=>"<span class=\x22emoji emoji1f4be\x22></span>", 
			"\xf0\x9f\x92\xbf"=>"<span class=\x22emoji emoji1f4bf\x22></span>", "\xf0\x9f\x93\x80"=>"<span class=\x22emoji emoji1f4c0\x22></span>", "\xe2\x9c\x82"=>"<span class=\x22emoji emoji2702\x22></span>", "\xf0\x9f\x93\x8d"=>"<span class=\x22emoji emoji1f4cd\x22></span>", "\xf0\x9f\x93\x83"=>"<span class=\x22emoji emoji1f4c3\x22></span>", 
			"\xf0\x9f\x93\x84"=>"<span class=\x22emoji emoji1f4c4\x22></span>", "\xf0\x9f\x93\x85"=>"<span class=\x22emoji emoji1f4c5\x22></span>", "\xf0\x9f\x93\x81"=>"<span class=\x22emoji emoji1f4c1\x22></span>", "\xf0\x9f\x93\x82"=>"<span class=\x22emoji emoji1f4c2\x22></span>", "\xf0\x9f\x93\x93"=>"<span class=\x22emoji emoji1f4d3\x22></span>", 
			"\xf0\x9f\x93\x96"=>"<span class=\x22emoji emoji1f4d6\x22></span>", "\xf0\x9f\x93\x94"=>"<span class=\x22emoji emoji1f4d4\x22></span>", "\xf0\x9f\x93\x95"=>"<span class=\x22emoji emoji1f4d5\x22></span>", "\xf0\x9f\x93\x97"=>"<span class=\x22emoji emoji1f4d7\x22></span>", "\xf0\x9f\x93\x98"=>"<span class=\x22emoji emoji1f4d8\x22></span>", 
			"\xf0\x9f\x93\x99"=>"<span class=\x22emoji emoji1f4d9\x22></span>", "\xf0\x9f\x93\x9a"=>"<span class=\x22emoji emoji1f4da\x22></span>", "\xf0\x9f\x93\x9b"=>"<span class=\x22emoji emoji1f4db\x22></span>", "\xf0\x9f\x93\x9c"=>"<span class=\x22emoji emoji1f4dc\x22></span>", "\xf0\x9f\x93\x8b"=>"<span class=\x22emoji emoji1f4cb\x22></span>", 
			"\xf0\x9f\x93\x86"=>"<span class=\x22emoji emoji1f4c6\x22></span>", "\xf0\x9f\x93\x8a"=>"<span class=\x22emoji emoji1f4ca\x22></span>", "\xf0\x9f\x93\x88"=>"<span class=\x22emoji emoji1f4c8\x22></span>", "\xf0\x9f\x93\x89"=>"<span class=\x22emoji emoji1f4c9\x22></span>", "\xf0\x9f\x93\x87"=>"<span class=\x22emoji emoji1f4c7\x22></span>", 
			"\xf0\x9f\x93\x8c"=>"<span class=\x22emoji emoji1f4cc\x22></span>", "\xf0\x9f\x93\x92"=>"<span class=\x22emoji emoji1f4d2\x22></span>", "\xf0\x9f\x93\x8f"=>"<span class=\x22emoji emoji1f4cf\x22></span>", "\xf0\x9f\x93\x90"=>"<span class=\x22emoji emoji1f4d0\x22></span>", "\xf0\x9f\x93\x91"=>"<span class=\x22emoji emoji1f4d1\x22></span>", 
			"\xf0\x9f\x8e\xbd"=>"<span class=\x22emoji emoji1f3bd\x22></span>", "\xe2\x9a\xbe"=>"<span class=\x22emoji emoji26be\x22></span>", "\xe2\x9b\xb3"=>"<span class=\x22emoji emoji26f3\x22></span>", "\xf0\x9f\x8e\xbe"=>"<span class=\x22emoji emoji1f3be\x22></span>", "\xe2\x9a\xbd"=>"<span class=\x22emoji emoji26bd\x22></span>", 
			"\xf0\x9f\x8e\xbf"=>"<span class=\x22emoji emoji1f3bf\x22></span>", "\xf0\x9f\x8f\x80"=>"<span class=\x22emoji emoji1f3c0\x22></span>", "\xf0\x9f\x8f\x81"=>"<span class=\x22emoji emoji1f3c1\x22></span>", "\xf0\x9f\x8f\x82"=>"<span class=\x22emoji emoji1f3c2\x22></span>", "\xf0\x9f\x8f\x83"=>"<span class=\x22emoji emoji1f3c3\x22></span>", 
			"\xf0\x9f\x8f\x84"=>"<span class=\x22emoji emoji1f3c4\x22></span>", "\xf0\x9f\x8f\x86"=>"<span class=\x22emoji emoji1f3c6\x22></span>", "\xf0\x9f\x8f\x88"=>"<span class=\x22emoji emoji1f3c8\x22></span>", "\xf0\x9f\x8f\x8a"=>"<span class=\x22emoji emoji1f3ca\x22></span>", "\xf0\x9f\x9a\x83"=>"<span class=\x22emoji emoji1f683\x22></span>", 
			"\xf0\x9f\x9a\x87"=>"<span class=\x22emoji emoji1f687\x22></span>", "\xe2\x93\x82"=>"<span class=\x22emoji emoji24c2\x22></span>", "\xf0\x9f\x9a\x84"=>"<span class=\x22emoji emoji1f684\x22></span>", "\xf0\x9f\x9a\x85"=>"<span class=\x22emoji emoji1f685\x22></span>", "\xf0\x9f\x9a\x97"=>"<span class=\x22emoji emoji1f697\x22></span>", 
			"\xf0\x9f\x9a\x99"=>"<span class=\x22emoji emoji1f699\x22></span>", "\xf0\x9f\x9a\x8c"=>"<span class=\x22emoji emoji1f68c\x22></span>", "\xf0\x9f\x9a\x8f"=>"<span class=\x22emoji emoji1f68f\x22></span>", "\xf0\x9f\x9a\xa2"=>"<span class=\x22emoji emoji1f6a2\x22></span>", "\xe2\x9c\x88"=>"<span class=\x22emoji emoji2708\x22></span>", 
			"\xe2\x9b\xb5"=>"<span class=\x22emoji emoji26f5\x22></span>", "\xf0\x9f\x9a\x89"=>"<span class=\x22emoji emoji1f689\x22></span>", "\xf0\x9f\x9a\x80"=>"<span class=\x22emoji emoji1f680\x22></span>", "\xf0\x9f\x9a\xa4"=>"<span class=\x22emoji emoji1f6a4\x22></span>", "\xf0\x9f\x9a\x95"=>"<span class=\x22emoji emoji1f695\x22></span>", 
			"\xf0\x9f\x9a\x9a"=>"<span class=\x22emoji emoji1f69a\x22></span>", "\xf0\x9f\x9a\x92"=>"<span class=\x22emoji emoji1f692\x22></span>", "\xf0\x9f\x9a\x91"=>"<span class=\x22emoji emoji1f691\x22></span>", "\xf0\x9f\x9a\x93"=>"<span class=\x22emoji emoji1f693\x22></span>", "\xe2\x9b\xbd"=>"<span class=\x22emoji emoji26fd\x22></span>", 
			"\xf0\x9f\x85\xbf"=>"<span class=\x22emoji emoji1f17f\x22></span>", "\xf0\x9f\x9a\xa5"=>"<span class=\x22emoji emoji1f6a5\x22></span>", "\xf0\x9f\x9a\xa7"=>"<span class=\x22emoji emoji1f6a7\x22></span>", "\xf0\x9f\x9a\xa8"=>"<span class=\x22emoji emoji1f6a8\x22></span>", "\xe2\x99\xa8"=>"<span class=\x22emoji emoji2668\x22></span>", 
			"\xe2\x9b\xba"=>"<span class=\x22emoji emoji26fa\x22></span>", "\xf0\x9f\x8e\xa0"=>"<span class=\x22emoji emoji1f3a0\x22></span>", "\xf0\x9f\x8e\xa1"=>"<span class=\x22emoji emoji1f3a1\x22></span>", "\xf0\x9f\x8e\xa2"=>"<span class=\x22emoji emoji1f3a2\x22></span>", "\xf0\x9f\x8e\xa3"=>"<span class=\x22emoji emoji1f3a3\x22></span>", 
			"\xf0\x9f\x8e\xa4"=>"<span class=\x22emoji emoji1f3a4\x22></span>", "\xf0\x9f\x8e\xa5"=>"<span class=\x22emoji emoji1f3a5\x22></span>", "\xf0\x9f\x8e\xa6"=>"<span class=\x22emoji emoji1f3a6\x22></span>", "\xf0\x9f\x8e\xa7"=>"<span class=\x22emoji emoji1f3a7\x22></span>", "\xf0\x9f\x8e\xa8"=>"<span class=\x22emoji emoji1f3a8\x22></span>", 
			"\xf0\x9f\x8e\xa9"=>"<span class=\x22emoji emoji1f3a9\x22></span>", "\xf0\x9f\x8e\xaa"=>"<span class=\x22emoji emoji1f3aa\x22></span>", "\xf0\x9f\x8e\xab"=>"<span class=\x22emoji emoji1f3ab\x22></span>", "\xf0\x9f\x8e\xac"=>"<span class=\x22emoji emoji1f3ac\x22></span>", "\xf0\x9f\x8e\xad"=>"<span class=\x22emoji emoji1f3ad\x22></span>", 
			"\xf0\x9f\x8e\xae"=>"<span class=\x22emoji emoji1f3ae\x22></span>", "\xf0\x9f\x80\x84"=>"<span class=\x22emoji emoji1f004\x22></span>", "\xf0\x9f\x8e\xaf"=>"<span class=\x22emoji emoji1f3af\x22></span>", "\xf0\x9f\x8e\xb0"=>"<span class=\x22emoji emoji1f3b0\x22></span>", "\xf0\x9f\x8e\xb1"=>"<span class=\x22emoji emoji1f3b1\x22></span>", 
			"\xf0\x9f\x8e\xb2"=>"<span class=\x22emoji emoji1f3b2\x22></span>", "\xf0\x9f\x8e\xb3"=>"<span class=\x22emoji emoji1f3b3\x22></span>", "\xf0\x9f\x8e\xb4"=>"<span class=\x22emoji emoji1f3b4\x22></span>", "\xf0\x9f\x83\x8f"=>"<span class=\x22emoji emoji1f0cf\x22></span>", "\xf0\x9f\x8e\xb5"=>"<span class=\x22emoji emoji1f3b5\x22></span>", 
			"\xf0\x9f\x8e\xb6"=>"<span class=\x22emoji emoji1f3b6\x22></span>", "\xf0\x9f\x8e\xb7"=>"<span class=\x22emoji emoji1f3b7\x22></span>", "\xf0\x9f\x8e\xb8"=>"<span class=\x22emoji emoji1f3b8\x22></span>", "\xf0\x9f\x8e\xb9"=>"<span class=\x22emoji emoji1f3b9\x22></span>", "\xf0\x9f\x8e\xba"=>"<span class=\x22emoji emoji1f3ba\x22></span>", 
			"\xf0\x9f\x8e\xbb"=>"<span class=\x22emoji emoji1f3bb\x22></span>", "\xf0\x9f\x8e\xbc"=>"<span class=\x22emoji emoji1f3bc\x22></span>", "\xe3\x80\xbd"=>"<span class=\x22emoji emoji303d\x22></span>", "\xf0\x9f\x93\xb7"=>"<span class=\x22emoji emoji1f4f7\x22></span>", "\xf0\x9f\x93\xb9"=>"<span class=\x22emoji emoji1f4f9\x22></span>", 
			"\xf0\x9f\x93\xba"=>"<span class=\x22emoji emoji1f4fa\x22></span>", "\xf0\x9f\x93\xbb"=>"<span class=\x22emoji emoji1f4fb\x22></span>", "\xf0\x9f\x93\xbc"=>"<span class=\x22emoji emoji1f4fc\x22></span>", "\xf0\x9f\x92\x8b"=>"<span class=\x22emoji emoji1f48b\x22></span>", "\xf0\x9f\x92\x8c"=>"<span class=\x22emoji emoji1f48c\x22></span>", 
			"\xf0\x9f\x92\x8d"=>"<span class=\x22emoji emoji1f48d\x22></span>", "\xf0\x9f\x92\x8e"=>"<span class=\x22emoji emoji1f48e\x22></span>", "\xf0\x9f\x92\x8f"=>"<span class=\x22emoji emoji1f48f\x22></span>", "\xf0\x9f\x92\x90"=>"<span class=\x22emoji emoji1f490\x22></span>", "\xf0\x9f\x92\x91"=>"<span class=\x22emoji emoji1f491\x22></span>", 
			"\xf0\x9f\x92\x92"=>"<span class=\x22emoji emoji1f492\x22></span>", "\xf0\x9f\x94\x9e"=>"<span class=\x22emoji emoji1f51e\x22></span>", "\xc2\xa9"=>"<span class=\x22emoji emojia9\x22></span>", "\xc2\xae"=>"<span class=\x22emoji emojiae\x22></span>", "\xe2\x84\xa2"=>"<span class=\x22emoji emoji2122\x22></span>", 
			"\xe2\x84\xb9"=>"<span class=\x22emoji emoji2139\x22></span>", "#\xe2\x83\xa3"=>"<span class=\x22emoji emoji2320e3\x22></span>", "1\xe2\x83\xa3"=>"<span class=\x22emoji emoji3120e3\x22></span>", "2\xe2\x83\xa3"=>"<span class=\x22emoji emoji3220e3\x22></span>", "3\xe2\x83\xa3"=>"<span class=\x22emoji emoji3320e3\x22></span>", 
			"4\xe2\x83\xa3"=>"<span class=\x22emoji emoji3420e3\x22></span>", "5\xe2\x83\xa3"=>"<span class=\x22emoji emoji3520e3\x22></span>", "6\xe2\x83\xa3"=>"<span class=\x22emoji emoji3620e3\x22></span>", "7\xe2\x83\xa3"=>"<span class=\x22emoji emoji3720e3\x22></span>", "8\xe2\x83\xa3"=>"<span class=\x22emoji emoji3820e3\x22></span>", 
			"9\xe2\x83\xa3"=>"<span class=\x22emoji emoji3920e3\x22></span>", "0\xe2\x83\xa3"=>"<span class=\x22emoji emoji3020e3\x22></span>", "\xf0\x9f\x94\x9f"=>"<span class=\x22emoji emoji1f51f\x22></span>", "\xf0\x9f\x93\xb6"=>"<span class=\x22emoji emoji1f4f6\x22></span>", "\xf0\x9f\x93\xb3"=>"<span class=\x22emoji emoji1f4f3\x22></span>", 
			"\xf0\x9f\x93\xb4"=>"<span class=\x22emoji emoji1f4f4\x22></span>", "\xf0\x9f\x8d\x94"=>"<span class=\x22emoji emoji1f354\x22></span>", "\xf0\x9f\x8d\x99"=>"<span class=\x22emoji emoji1f359\x22></span>", "\xf0\x9f\x8d\xb0"=>"<span class=\x22emoji emoji1f370\x22></span>", "\xf0\x9f\x8d\x9c"=>"<span class=\x22emoji emoji1f35c\x22></span>", 
			"\xf0\x9f\x8d\x9e"=>"<span class=\x22emoji emoji1f35e\x22></span>", "\xf0\x9f\x8d\xb3"=>"<span class=\x22emoji emoji1f373\x22></span>", "\xf0\x9f\x8d\xa6"=>"<span class=\x22emoji emoji1f366\x22></span>", "\xf0\x9f\x8d\x9f"=>"<span class=\x22emoji emoji1f35f\x22></span>", "\xf0\x9f\x8d\xa1"=>"<span class=\x22emoji emoji1f361\x22></span>", 
			"\xf0\x9f\x8d\x98"=>"<span class=\x22emoji emoji1f358\x22></span>", "\xf0\x9f\x8d\x9a"=>"<span class=\x22emoji emoji1f35a\x22></span>", "\xf0\x9f\x8d\x9d"=>"<span class=\x22emoji emoji1f35d\x22></span>", "\xf0\x9f\x8d\x9b"=>"<span class=\x22emoji emoji1f35b\x22></span>", "\xf0\x9f\x8d\xa2"=>"<span class=\x22emoji emoji1f362\x22></span>", 
			"\xf0\x9f\x8d\xa3"=>"<span class=\x22emoji emoji1f363\x22></span>", "\xf0\x9f\x8d\xb1"=>"<span class=\x22emoji emoji1f371\x22></span>", "\xf0\x9f\x8d\xb2"=>"<span class=\x22emoji emoji1f372\x22></span>", "\xf0\x9f\x8d\xa7"=>"<span class=\x22emoji emoji1f367\x22></span>", "\xf0\x9f\x8d\x96"=>"<span class=\x22emoji emoji1f356\x22></span>", 
			"\xf0\x9f\x8d\xa5"=>"<span class=\x22emoji emoji1f365\x22></span>", "\xf0\x9f\x8d\xa0"=>"<span class=\x22emoji emoji1f360\x22></span>", "\xf0\x9f\x8d\x95"=>"<span class=\x22emoji emoji1f355\x22></span>", "\xf0\x9f\x8d\x97"=>"<span class=\x22emoji emoji1f357\x22></span>", "\xf0\x9f\x8d\xa8"=>"<span class=\x22emoji emoji1f368\x22></span>", 
			"\xf0\x9f\x8d\xa9"=>"<span class=\x22emoji emoji1f369\x22></span>", "\xf0\x9f\x8d\xaa"=>"<span class=\x22emoji emoji1f36a\x22></span>", "\xf0\x9f\x8d\xab"=>"<span class=\x22emoji emoji1f36b\x22></span>", "\xf0\x9f\x8d\xac"=>"<span class=\x22emoji emoji1f36c\x22></span>", "\xf0\x9f\x8d\xad"=>"<span class=\x22emoji emoji1f36d\x22></span>", 
			"\xf0\x9f\x8d\xae"=>"<span class=\x22emoji emoji1f36e\x22></span>", "\xf0\x9f\x8d\xaf"=>"<span class=\x22emoji emoji1f36f\x22></span>", "\xf0\x9f\x8d\xa4"=>"<span class=\x22emoji emoji1f364\x22></span>", "\xf0\x9f\x8d\xb4"=>"<span class=\x22emoji emoji1f374\x22></span>", "\xe2\x98\x95"=>"<span class=\x22emoji emoji2615\x22></span>", 
			"\xf0\x9f\x8d\xb8"=>"<span class=\x22emoji emoji1f378\x22></span>", "\xf0\x9f\x8d\xba"=>"<span class=\x22emoji emoji1f37a\x22></span>", "\xf0\x9f\x8d\xb5"=>"<span class=\x22emoji emoji1f375\x22></span>", "\xf0\x9f\x8d\xb6"=>"<span class=\x22emoji emoji1f376\x22></span>", "\xf0\x9f\x8d\xb7"=>"<span class=\x22emoji emoji1f377\x22></span>", 
			"\xf0\x9f\x8d\xbb"=>"<span class=\x22emoji emoji1f37b\x22></span>", "\xf0\x9f\x8d\xb9"=>"<span class=\x22emoji emoji1f379\x22></span>", "\xe2\x86\x97"=>"<span class=\x22emoji emoji2197\x22></span>", "\xe2\x86\x98"=>"<span class=\x22emoji emoji2198\x22></span>", "\xe2\x86\x96"=>"<span class=\x22emoji emoji2196\x22></span>", 
			"\xe2\x86\x99"=>"<span class=\x22emoji emoji2199\x22></span>", "\xe2\xa4\xb4"=>"<span class=\x22emoji emoji2934\x22></span>", "\xe2\xa4\xb5"=>"<span class=\x22emoji emoji2935\x22></span>", "\xe2\x86\x94"=>"<span class=\x22emoji emoji2194\x22></span>", "\xe2\x86\x95"=>"<span class=\x22emoji emoji2195\x22></span>", 
			"\xe2\xac\x86"=>"<span class=\x22emoji emoji2b06\x22></span>", "\xe2\xac\x87"=>"<span class=\x22emoji emoji2b07\x22></span>", "\xe2\x9e\xa1"=>"<span class=\x22emoji emoji27a1\x22></span>", "\xe2\xac\x85"=>"<span class=\x22emoji emoji2b05\x22></span>", "\xe2\x96\xb6"=>"<span class=\x22emoji emoji25b6\x22></span>", 
			"\xe2\x97\x80"=>"<span class=\x22emoji emoji25c0\x22></span>", "\xe2\x8f\xa9"=>"<span class=\x22emoji emoji23e9\x22></span>", "\xe2\x8f\xaa"=>"<span class=\x22emoji emoji23ea\x22></span>", "\xe2\x8f\xab"=>"<span class=\x22emoji emoji23eb\x22></span>", "\xe2\x8f\xac"=>"<span class=\x22emoji emoji23ec\x22></span>", 
			"\xf0\x9f\x94\xba"=>"<span class=\x22emoji emoji1f53a\x22></span>", "\xf0\x9f\x94\xbb"=>"<span class=\x22emoji emoji1f53b\x22></span>", "\xf0\x9f\x94\xbc"=>"<span class=\x22emoji emoji1f53c\x22></span>", "\xf0\x9f\x94\xbd"=>"<span class=\x22emoji emoji1f53d\x22></span>", "\xe2\xad\x95"=>"<span class=\x22emoji emoji2b55\x22></span>", 
			"\xe2\x9d\x8c"=>"<span class=\x22emoji emoji274c\x22></span>", "\xe2\x9d\x8e"=>"<span class=\x22emoji emoji274e\x22></span>", "\xe2\x9d\x97"=>"<span class=\x22emoji emoji2757\x22></span>", "\xe2\x81\x89"=>"<span class=\x22emoji emoji2049\x22></span>", "\xe2\x80\xbc"=>"<span class=\x22emoji emoji203c\x22></span>", 
			"\xe2\x9d\x93"=>"<span class=\x22emoji emoji2753\x22></span>", "\xe2\x9d\x94"=>"<span class=\x22emoji emoji2754\x22></span>", "\xe2\x9d\x95"=>"<span class=\x22emoji emoji2755\x22></span>", "\xe3\x80\xb0"=>"<span class=\x22emoji emoji3030\x22></span>", "\xe2\x9e\xb0"=>"<span class=\x22emoji emoji27b0\x22></span>", 
			"\xe2\x9e\xbf"=>"<span class=\x22emoji emoji27bf\x22></span>", "\xe2\x9d\xa4"=>"<span class=\x22emoji emoji2764\x22></span>", "\xf0\x9f\x92\x93"=>"<span class=\x22emoji emoji1f493\x22></span>", "\xf0\x9f\x92\x94"=>"<span class=\x22emoji emoji1f494\x22></span>", "\xf0\x9f\x92\x95"=>"<span class=\x22emoji emoji1f495\x22></span>", 
			"\xf0\x9f\x92\x96"=>"<span class=\x22emoji emoji1f496\x22></span>", "\xf0\x9f\x92\x97"=>"<span class=\x22emoji emoji1f497\x22></span>", "\xf0\x9f\x92\x98"=>"<span class=\x22emoji emoji1f498\x22></span>", "\xf0\x9f\x92\x99"=>"<span class=\x22emoji emoji1f499\x22></span>", "\xf0\x9f\x92\x9a"=>"<span class=\x22emoji emoji1f49a\x22></span>", 
			"\xf0\x9f\x92\x9b"=>"<span class=\x22emoji emoji1f49b\x22></span>", "\xf0\x9f\x92\x9c"=>"<span class=\x22emoji emoji1f49c\x22></span>", "\xf0\x9f\x92\x9d"=>"<span class=\x22emoji emoji1f49d\x22></span>", "\xf0\x9f\x92\x9e"=>"<span class=\x22emoji emoji1f49e\x22></span>", "\xf0\x9f\x92\x9f"=>"<span class=\x22emoji emoji1f49f\x22></span>", 
			"\xe2\x99\xa5"=>"<span class=\x22emoji emoji2665\x22></span>", "\xe2\x99\xa0"=>"<span class=\x22emoji emoji2660\x22></span>", "\xe2\x99\xa6"=>"<span class=\x22emoji emoji2666\x22></span>", "\xe2\x99\xa3"=>"<span class=\x22emoji emoji2663\x22></span>", "\xf0\x9f\x9a\xac"=>"<span class=\x22emoji emoji1f6ac\x22></span>", 
			"\xf0\x9f\x9a\xad"=>"<span class=\x22emoji emoji1f6ad\x22></span>", "\xe2\x99\xbf"=>"<span class=\x22emoji emoji267f\x22></span>", "\xf0\x9f\x9a\xa9"=>"<span class=\x22emoji emoji1f6a9\x22></span>", "\xe2\x9a\xa0"=>"<span class=\x22emoji emoji26a0\x22></span>", "\xe2\x9b\x94"=>"<span class=\x22emoji emoji26d4\x22></span>", 
			"\xe2\x99\xbb"=>"<span class=\x22emoji emoji267b\x22></span>", "\xf0\x9f\x9a\xb2"=>"<span class=\x22emoji emoji1f6b2\x22></span>", "\xf0\x9f\x9a\xb6"=>"<span class=\x22emoji emoji1f6b6\x22></span>", "\xf0\x9f\x9a\xb9"=>"<span class=\x22emoji emoji1f6b9\x22></span>", "\xf0\x9f\x9a\xba"=>"<span class=\x22emoji emoji1f6ba\x22></span>", 
			"\xf0\x9f\x9b\x80"=>"<span class=\x22emoji emoji1f6c0\x22></span>", "\xf0\x9f\x9a\xbb"=>"<span class=\x22emoji emoji1f6bb\x22></span>", "\xf0\x9f\x9a\xbd"=>"<span class=\x22emoji emoji1f6bd\x22></span>", "\xf0\x9f\x9a\xbe"=>"<span class=\x22emoji emoji1f6be\x22></span>", "\xf0\x9f\x9a\xbc"=>"<span class=\x22emoji emoji1f6bc\x22></span>", 
			"\xf0\x9f\x9a\xaa"=>"<span class=\x22emoji emoji1f6aa\x22></span>", "\xf0\x9f\x9a\xab"=>"<span class=\x22emoji emoji1f6ab\x22></span>", "\xe2\x9c\x94"=>"<span class=\x22emoji emoji2714\x22></span>", "\xf0\x9f\x86\x91"=>"<span class=\x22emoji emoji1f191\x22></span>", "\xf0\x9f\x86\x92"=>"<span class=\x22emoji emoji1f192\x22></span>", 
			"\xf0\x9f\x86\x93"=>"<span class=\x22emoji emoji1f193\x22></span>", "\xf0\x9f\x86\x94"=>"<span class=\x22emoji emoji1f194\x22></span>", "\xf0\x9f\x86\x95"=>"<span class=\x22emoji emoji1f195\x22></span>", "\xf0\x9f\x86\x96"=>"<span class=\x22emoji emoji1f196\x22></span>", "\xf0\x9f\x86\x97"=>"<span class=\x22emoji emoji1f197\x22></span>", 
			"\xf0\x9f\x86\x98"=>"<span class=\x22emoji emoji1f198\x22></span>", "\xf0\x9f\x86\x99"=>"<span class=\x22emoji emoji1f199\x22></span>", "\xf0\x9f\x86\x9a"=>"<span class=\x22emoji emoji1f19a\x22></span>", "\xf0\x9f\x88\x81"=>"<span class=\x22emoji emoji1f201\x22></span>", "\xf0\x9f\x88\x82"=>"<span class=\x22emoji emoji1f202\x22></span>", 
			"\xf0\x9f\x88\xb2"=>"<span class=\x22emoji emoji1f232\x22></span>", "\xf0\x9f\x88\xb3"=>"<span class=\x22emoji emoji1f233\x22></span>", "\xf0\x9f\x88\xb4"=>"<span class=\x22emoji emoji1f234\x22></span>", "\xf0\x9f\x88\xb5"=>"<span class=\x22emoji emoji1f235\x22></span>", "\xf0\x9f\x88\xb6"=>"<span class=\x22emoji emoji1f236\x22></span>", 
			"\xf0\x9f\x88\x9a"=>"<span class=\x22emoji emoji1f21a\x22></span>", "\xf0\x9f\x88\xb7"=>"<span class=\x22emoji emoji1f237\x22></span>", "\xf0\x9f\x88\xb8"=>"<span class=\x22emoji emoji1f238\x22></span>", "\xf0\x9f\x88\xb9"=>"<span class=\x22emoji emoji1f239\x22></span>", "\xf0\x9f\x88\xaf"=>"<span class=\x22emoji emoji1f22f\x22></span>", 
			"\xf0\x9f\x88\xba"=>"<span class=\x22emoji emoji1f23a\x22></span>", "\xe3\x8a\x99"=>"<span class=\x22emoji emoji3299\x22></span>", "\xe3\x8a\x97"=>"<span class=\x22emoji emoji3297\x22></span>", "\xf0\x9f\x89\x90"=>"<span class=\x22emoji emoji1f250\x22></span>", "\xf0\x9f\x89\x91"=>"<span class=\x22emoji emoji1f251\x22></span>", 
			"\xe2\x9e\x95"=>"<span class=\x22emoji emoji2795\x22></span>", "\xe2\x9e\x96"=>"<span class=\x22emoji emoji2796\x22></span>", "\xe2\x9c\x96"=>"<span class=\x22emoji emoji2716\x22></span>", "\xe2\x9e\x97"=>"<span class=\x22emoji emoji2797\x22></span>", "\xf0\x9f\x92\xa0"=>"<span class=\x22emoji emoji1f4a0\x22></span>", 
			"\xf0\x9f\x92\xa1"=>"<span class=\x22emoji emoji1f4a1\x22></span>", "\xf0\x9f\x92\xa2"=>"<span class=\x22emoji emoji1f4a2\x22></span>", "\xf0\x9f\x92\xa3"=>"<span class=\x22emoji emoji1f4a3\x22></span>", "\xf0\x9f\x92\xa4"=>"<span class=\x22emoji emoji1f4a4\x22></span>", "\xf0\x9f\x92\xa5"=>"<span class=\x22emoji emoji1f4a5\x22></span>", 
			"\xf0\x9f\x92\xa6"=>"<span class=\x22emoji emoji1f4a6\x22></span>", "\xf0\x9f\x92\xa7"=>"<span class=\x22emoji emoji1f4a7\x22></span>", "\xf0\x9f\x92\xa8"=>"<span class=\x22emoji emoji1f4a8\x22></span>", "\xf0\x9f\x92\xa9"=>"<span class=\x22emoji emoji1f4a9\x22></span>", "\xf0\x9f\x92\xaa"=>"<span class=\x22emoji emoji1f4aa\x22></span>", 
			"\xf0\x9f\x92\xab"=>"<span class=\x22emoji emoji1f4ab\x22></span>", "\xf0\x9f\x92\xac"=>"<span class=\x22emoji emoji1f4ac\x22></span>", "\xe2\x9c\xa8"=>"<span class=\x22emoji emoji2728\x22></span>", "\xe2\x9c\xb4"=>"<span class=\x22emoji emoji2734\x22></span>", "\xe2\x9c\xb3"=>"<span class=\x22emoji emoji2733\x22></span>", 
			"\xe2\x9a\xaa"=>"<span class=\x22emoji emoji26aa\x22></span>", "\xe2\x9a\xab"=>"<span class=\x22emoji emoji26ab\x22></span>", "\xf0\x9f\x94\xb4"=>"<span class=\x22emoji emoji1f534\x22></span>", "\xf0\x9f\x94\xb5"=>"<span class=\x22emoji emoji1f535\x22></span>", "\xf0\x9f\x94\xb2"=>"<span class=\x22emoji emoji1f532\x22></span>", 
			"\xf0\x9f\x94\xb3"=>"<span class=\x22emoji emoji1f533\x22></span>", "\xe2\xad\x90"=>"<span class=\x22emoji emoji2b50\x22></span>", "\xe2\xac\x9c"=>"<span class=\x22emoji emoji2b1c\x22></span>", "\xe2\xac\x9b"=>"<span class=\x22emoji emoji2b1b\x22></span>", "\xe2\x96\xab"=>"<span class=\x22emoji emoji25ab\x22></span>", 
			"\xe2\x96\xaa"=>"<span class=\x22emoji emoji25aa\x22></span>", "\xe2\x97\xbd"=>"<span class=\x22emoji emoji25fd\x22></span>", "\xe2\x97\xbe"=>"<span class=\x22emoji emoji25fe\x22></span>", "\xe2\x97\xbb"=>"<span class=\x22emoji emoji25fb\x22></span>", "\xe2\x97\xbc"=>"<span class=\x22emoji emoji25fc\x22></span>", 
			"\xf0\x9f\x94\xb6"=>"<span class=\x22emoji emoji1f536\x22></span>", "\xf0\x9f\x94\xb7"=>"<span class=\x22emoji emoji1f537\x22></span>", "\xf0\x9f\x94\xb8"=>"<span class=\x22emoji emoji1f538\x22></span>", "\xf0\x9f\x94\xb9"=>"<span class=\x22emoji emoji1f539\x22></span>", "\xe2\x9d\x87"=>"<span class=\x22emoji emoji2747\x22></span>", 
			"\xf0\x9f\x92\xae"=>"<span class=\x22emoji emoji1f4ae\x22></span>", "\xf0\x9f\x92\xaf"=>"<span class=\x22emoji emoji1f4af\x22></span>", "\xe2\x86\xa9"=>"<span class=\x22emoji emoji21a9\x22></span>", "\xe2\x86\xaa"=>"<span class=\x22emoji emoji21aa\x22></span>", "\xf0\x9f\x94\x83"=>"<span class=\x22emoji emoji1f503\x22></span>", 
			"\xf0\x9f\x94\x8a"=>"<span class=\x22emoji emoji1f50a\x22></span>", "\xf0\x9f\x94\x8b"=>"<span class=\x22emoji emoji1f50b\x22></span>", "\xf0\x9f\x94\x8c"=>"<span class=\x22emoji emoji1f50c\x22></span>", "\xf0\x9f\x94\x8d"=>"<span class=\x22emoji emoji1f50d\x22></span>", "\xf0\x9f\x94\x8e"=>"<span class=\x22emoji emoji1f50e\x22></span>", 
			"\xf0\x9f\x94\x92"=>"<span class=\x22emoji emoji1f512\x22></span>", "\xf0\x9f\x94\x93"=>"<span class=\x22emoji emoji1f513\x22></span>", "\xf0\x9f\x94\x8f"=>"<span class=\x22emoji emoji1f50f\x22></span>", "\xf0\x9f\x94\x90"=>"<span class=\x22emoji emoji1f510\x22></span>", "\xf0\x9f\x94\x91"=>"<span class=\x22emoji emoji1f511\x22></span>", 
			"\xf0\x9f\x94\x94"=>"<span class=\x22emoji emoji1f514\x22></span>", "\xe2\x98\x91"=>"<span class=\x22emoji emoji2611\x22></span>", "\xf0\x9f\x94\x98"=>"<span class=\x22emoji emoji1f518\x22></span>", "\xf0\x9f\x94\x96"=>"<span class=\x22emoji emoji1f516\x22></span>", "\xf0\x9f\x94\x97"=>"<span class=\x22emoji emoji1f517\x22></span>", 
			"\xf0\x9f\x94\x99"=>"<span class=\x22emoji emoji1f519\x22></span>", "\xf0\x9f\x94\x9a"=>"<span class=\x22emoji emoji1f51a\x22></span>", "\xf0\x9f\x94\x9b"=>"<span class=\x22emoji emoji1f51b\x22></span>", "\xf0\x9f\x94\x9c"=>"<span class=\x22emoji emoji1f51c\x22></span>", "\xf0\x9f\x94\x9d"=>"<span class=\x22emoji emoji1f51d\x22></span>", 
			"\xe2\x9c\x85"=>"<span class=\x22emoji emoji2705\x22></span>", "\xe2\x9c\x8a"=>"<span class=\x22emoji emoji270a\x22></span>", "\xe2\x9c\x8b"=>"<span class=\x22emoji emoji270b\x22></span>", "\xe2\x9c\x8c"=>"<span class=\x22emoji emoji270c\x22></span>", "\xf0\x9f\x91\x8a"=>"<span class=\x22emoji emoji1f44a\x22></span>", 
			"\xf0\x9f\x91\x8d"=>"<span class=\x22emoji emoji1f44d\x22></span>", "\xe2\x98\x9d"=>"<span class=\x22emoji emoji261d\x22></span>", "\xf0\x9f\x91\x86"=>"<span class=\x22emoji emoji1f446\x22></span>", "\xf0\x9f\x91\x87"=>"<span class=\x22emoji emoji1f447\x22></span>", "\xf0\x9f\x91\x88"=>"<span class=\x22emoji emoji1f448\x22></span>", 
			"\xf0\x9f\x91\x89"=>"<span class=\x22emoji emoji1f449\x22></span>", "\xf0\x9f\x91\x8b"=>"<span class=\x22emoji emoji1f44b\x22></span>", "\xf0\x9f\x91\x8f"=>"<span class=\x22emoji emoji1f44f\x22></span>", "\xf0\x9f\x91\x8c"=>"<span class=\x22emoji emoji1f44c\x22></span>", "\xf0\x9f\x91\x8e"=>"<span class=\x22emoji emoji1f44e\x22></span>", 
			"\xf0\x9f\x91\x90"=>"<span class=\x22emoji emoji1f450\x22></span>", 
		);



?>