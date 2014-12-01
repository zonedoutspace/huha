<?php

require_once('../includes/helper.php');

if (isset($_SESSION['userid']))
	render('template', array('view' => 'home', 'title' => 'Home', 'header' => 'Home'));
else
	render('template', array('view' => 'login', 'title' => 'Log in', 'header' => 'Log in'));
?>
