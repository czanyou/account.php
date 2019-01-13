<?php

global $errorCount;

function testCase($name) {
	echo 'Run Test Case: ', $name, ':', "\r\n";
}

function testPrintStackTrace() {
   $array = debug_backtrace();
   unset($array[0]);
   unset($array[1]);

   $html = "";
   foreach ($array as $row) {
       $html .=$row['file'].':'.$row['line'].'('.$row['function'].")\r\n";
   }
   echo $html;
}

function testReport() {
	global $errorCount;
	if ($errorCount > 0) {
		echo 'Test failed: total ', $errorCount, ' errors.', "\r\n";
	} else {
		echo 'Test: all pass!';
	}
}

function testFailed() {
	testPrintStackTrace();

	global $errorCount;
	$errorCount++;
}

function assertTrue($result, $message = "") {
	if (!$result) {
		testFailed();
		echo 'Expected true! got[', $message, "]\r\n";
	}
}

function assertNotNull($result, $message = "") {
	if ($result == null) {
		testFailed();
		echo 'Expected not null! got[', $message, "]\r\n";
	}
}

function assertNull($result, $message = "") {
	if ($result != null) {
		testFailed();
		echo 'Expected null! got[', $message, "]\r\n";
	}
}

function assertFalse($result, $message = "") {
	if ($result) {
		testFailed();
		echo 'Expected false! got[', $message, "]\r\n";
	}
}

function assertEqual($value1, $result) {
	if ($value1 != $result) {
		testFailed();
		echo 'Equal fails: want [', $value1, '], but got [', $result, "]\r\n";
	}	
}



