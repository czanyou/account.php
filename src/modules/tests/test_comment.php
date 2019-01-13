<?php

require_once '../../common.php';
require_once 'common/api_comment.php';

require_once 'test_unit.php';

global $globalViewer;
$globalViewer = (object)array('id'=>100, 'email'=>'test@qq.com');

function test_comment_like() {
	$commentId = 100;
	$userId = 100;

	CommentManager::addLike($commentId , $userId);

	$likes = CommentManager::getLikes($commentId );
	assertEqual(1, $likes);

	$isLike = CommentManager::isLike($commentId , $userId);
	assertTrue($isLike, 'is not like');

	CommentManager::removeLike($commentId , $userId);

	$isLike = CommentManager::isLike($commentId , $userId);
	assertTrue(!$isLike, 'is like');	
}

function test_emoji() {
	$text = "U+1F30x🌀;U+1F91x🤐;U+1F64x🙀;U+1F68x🚀;U+260x;☀;U+27Bx➰;";
	echo(bin2hex($text));

	echo ' text: ', $text;
	$output = CommentManager::encodeContent($text);
	echo ' output: ', $output;

	$text = CommentManager::decodeContent($output);
	echo ' text: ', $text;
}

function test_comment() {
	$uid = 'test1234';

	// add
	$params = array('uid'=>$uid, 'content'=>'test');
	$ret = onCommentAdd($params);
	//print_r($ret);

	// list
	$params = array('uid'=>$uid);
	$ret = onCommentList($params);
	//print_r($ret);

	$list = safe_get($ret, 'data', array());
	$comment = $list[0];

	// like/unlike
	$params = array('id'=> $comment->id, 'openid'=>100);
	$ret = onCommentLike($params);
	//print_r($ret);

	$likes = CommentManager::getLikes($comment->id);
	assertEqual(1, $likes);

	$ret = onCommentUnlike($params);
	//print_r($ret);

    $likes = CommentManager::getLikes($comment->id);
	assertEqual(0, $likes);

	// reply
	$params = array('uid'=>$uid, 'content'=>'test', 'id'=>$comment->id);
	$ret = onCommentAdd($params);

	$params = array('id'=>$comment->id);
	$ret = onCommentList($params);
	print_r($ret);

	// remove
	foreach ($list as $comment) {
		$params = array('id'=> $comment->id, 'openid'=>100);

        $ret = onCommentRemove($params);
        //print_r($ret);
    }
}

function test_all() {
	test_emoji();
	test_comment_like();
}


//test_all();
test_comment();

