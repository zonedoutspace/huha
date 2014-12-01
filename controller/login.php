<?php

require_once('../model/model.php');
require_once('../includes/helper.php');

if (isset($_POST['email']) && isset($_POST['password']))
{
	
	$email = $_POST['email'];
	$password = $_POST['password'];
	
	$userid = 4;
	if ($userid > 0)
	{
		$_SESSION['userid'] = $userid;
		render('template', array('view' => 'home', 'title' => 'Home', 'header' => 'Home'));
	}
	else
	{
		render('template', array('view' => 'login', 'error' => $error, 'header' => 'Log in'));
	}
}
else
{
	render('template', array('view' => 'login', 'header' => 'Log in'));
}
?>
