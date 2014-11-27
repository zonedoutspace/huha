<?php

require_once('../model/model.php');
require_once('../includes/helper.php');

if (isset($_REQUEST['param']))
{
	$sold = sell_shares($_SESSION['userid'], $_REQUEST['param'], $error);
	if (!$sold)
	{
		render('template', array('view' => 'portfolio', 'title' => 'Portfolio', 
			'header' => 'Portfolio', 'error' => $error));
	}
	header("Location: /portfolio");
}
else
{
	render('template', array('view' => 'portfolio', 'title' => 'Portfolio', 'header' => 'Portfolio'));
}
