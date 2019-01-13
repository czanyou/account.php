<?php
require_once '../../common.php';
require_once 'common/api_comment.php';
require_once 'common/api_device.php';

require_once 'test_unit.php';

global $globalViewer;
$globalViewer = (object)array('id'=>100, 'email'=>'test@qq.com');

function test_comment() {
	$uid = 'test1234';

	//$pic = array('type'=>'image/jpg', 'tmp_name'=>S_ROOT.'/modules/tests/test.jpg');
	//$video = array('type'=>'video/mp4', 'tmp_name'=>S_ROOT.'/modules/tests/test.jpg');
	//$files = array('pic'=>$pic, 'video'=>$video);

	// add
	$params = array('uid'=>$uid, 'content'=>'test image');
	//$ret = onCommentUploadPicture($params, $files);
	//print_r($ret);

	$params = array('uid'=>$uid, 'content'=>'test upload');
	$ret = onCommentUploadPicture($params);
	print_r($ret);	

	// list
	$params = array('uid'=>$uid);
	$ret = onCommentList($params);
	$list = safe_get($ret, 'data', array());
	$comment = $list[0];
	print_r($comment);

	// remove
	foreach ($list as $comment) {
		$params = array('id'=> $comment->id, 'openid'=>100);

        $ret = onCommentRemove($params);
    }
}

function test_device() {
	$uid = 'test1234';

	$openid = 100;
	$params = array('openid'=>$openid, 'name'=>'test', 'uri'=>'pppp://1234', 'uid'=>$uid);
	$ret = onPublicDeviceAdd($params);

	// add
	$params = array('uid'=>$uid, 'content'=>'test image');
	$ret = onDeviceUploadCover($params);
	print_r($ret);	
}

function test_group() {
	$uid = 'test1234';

	$openid = 100;
	$params = array('openid'=>$openid, 'name'=>'test', 'uri'=>'pppp://1234', 'uid'=>$uid);
	$ret = onPublicDeviceAdd($params);

	// add
	$params = array('uid'=>$uid, 'content'=>'test image');
	$ret = onGroupUpdateCover($params);
	print_r($ret);	
}

function test_user() {
	$openid = 1576;

	// add
	$params = array('openid'=>$openid);
	$ret = onUserUpdateCover($params);
	print_r($ret);	
}

$files = $_FILES;
if ($files) {
    print_r($files);

    $pic = safe_get($files, 'pic');
    if ($pic) {
    	test_user();
    }
}

?>

<html>
<head>
</head>
<body>
<form action="test_upload.php" method="post" enctype="multipart/form-data">
	<input type="file" name="pic"/>
	<input type="submit" name="submit" value="Submit"/>
</form>
</body>
</html>