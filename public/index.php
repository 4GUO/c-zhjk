<?php
	include __DIR__ . '/Mobile_Detect.php';
	$Mobile_Detect = new Mobile_Detect();
	if (!$Mobile_Detect->isMobile()) {
		header('location:admin.php');
	} else {
		header('location:front.php');
	}