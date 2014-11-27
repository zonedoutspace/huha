<?php
/*********************
 * portfolio.php
 *
 * CSCI S-75
 * Project 1
 * Chris Gerber
 *
 * Portfolio controller
 *********************/

require_once('../model/model.php');
require_once('../includes/helper.php');

if (isset($_SESSION['userid']))
{
	$userid = (int)$_SESSION['userid'];

	$portfolio = get_user_portfolio($userid, $error);
	if (!$portfolio)
	{
		render('template', array('view' => 'portfolio', 'title' => 'Portfolio', 
			'header' => 'Portfolio', 'error' => $error));
		exit();
	}
	
	// send all the data to the template
	render('template', array('view' => 'portfolio', 'title' => 'Portfolio', 'header' => 'Portfolio', 
		'data' => $portfolio, 'error' => $error));
}
else
{
	render('template', array('view' => 'login', 'title' => 'Login', 'header' => 'Log in'));
}
?>
