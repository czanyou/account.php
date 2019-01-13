<?php

require_once '../../common.php';
require_once 'common/api_comment.php';

require_once 'test_unit.php';

global $globalViewer;
$globalViewer = (object)array('id'=>100, 'email'=>'test@qq.com');

function test_comment() {
	$uid = 'test1234';

	$pic = array('type'=>'image/jpg', 'tmp_name'=>S_ROOT.'/modules/tests/test.jpg');
	$video = array('type'=>'video/mp4', 'tmp_name'=>S_ROOT.'/modules/tests/test.jpg');
	$files = array('pic'=>$pic, 'video'=>$video);

	// add
	$params = array('uid'=>$uid, 'content'=>'test image');
	//$ret = onCommentUploadPicture($params, $files);
	//print_r($ret);

	$params = array('uid'=>$uid, 'content'=>'test video');
	$ret = onCommentUploadVideo($params, $files);
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

test_comment();
