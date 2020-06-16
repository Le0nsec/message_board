<?php
	header("content-type:text/html;charset=utf-8");

	$conn=mysqli_connect("127.0.0.1","message","message");
	mysqli_set_charset($conn,"utf8");
	mysqli_select_db($conn,"message_board");