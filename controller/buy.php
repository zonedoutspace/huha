<?php

require_once('../model/model.php');
require_once('../includes/helper.php');

if (isset($_REQUEST['param']) && isset($_REQUEST['amount']))
{
	$symbol = strtoupper($_REQUEST['param']);
	$amount = $_REQUEST['amount'];
	$userid = $_SESSION['userid'];
	$return = buy_shares($userid, $symbol, $amount, $error);
	if (!$return)
	{
		render('template', array('view' => 'buy', 'title' => 'Buy', 'header' => 'Buy stock', 'error' => $error));
		exit();
	}
	header("Location: /portfolio");
}
else
{
	render('template', array('view' => 'buy', 'title' => 'Buy', 'header' => 'Buy stock'));
}