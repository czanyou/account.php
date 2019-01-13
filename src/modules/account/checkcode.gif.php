<?php

/**
 * 生成验证码图片
 * @param width 图片宽度
 * @param height 图片高度
 * @param how 验证码位数 
 * @param fontsize 字体大小
 */
function create_check_image($width = 20, $height = 27, $count = 5, $fontsize = 5) {

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

		$color = ImageColorAllocate($image, mt_rand(180, 230), mt_rand(180, 230), mt_rand(180, 230)); // 字符随机颜色
		ImageChar($image, 3, $pos, mt_rand(0, 6), $code, $color); // 绘字符
	}

	/** 添加干扰 */
	for ($i = 0; $i < 5; $i++) { // 绘背景干扰线
		$color = ImageColorAllocate($image, mt_rand(100, 255), mt_rand(100, 255), mt_rand(100, 255)); // 干扰线颜色
		ImageArc($image, mt_rand(-5, $w), mt_rand(-5, $height), mt_rand(20, 300), mt_rand(20, 200), 55, 44, $color); // 干扰线
	} 
	
	for ($i = 0; $i < $count * 8; $i++)	{ //绘背景干扰点
		$color = ImageColorAllocate($image, mt_rand(100, 255), mt_rand(100, 255), mt_rand(100, 255)); //干扰点颜色 
		ImageSetPixel($image, mt_rand(0, $w), mt_rand(0, $height), $color); // 干扰点
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

	// 把验证码字符串写入 session
	session_start();
	$_SESSION['randcode'] = $randcode;
   setcookie("randcode", "$randcode", 0,"/",""); 
	/* 绘图结束 */
	Imagegif($image);
	ImageDestroy($image);
}

header("Content-type: image/gif");
create_check_image();

?> 