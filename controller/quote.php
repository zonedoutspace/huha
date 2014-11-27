<?php
/*******************
 * quote.php
 *
 * CSCI S-75
 * Project 1
 * Chris Gerber
 *
 * Quote controller
 *******************/

require_once('../model/model.php');
require_once('../includes/helper.php');

if (isset($_REQUEST['param']))
{
	$error;
	$quote_data = get_quote_data(urlencode($_REQUEST['param']), $error);
	if (!$quote_data)
	{
		render('template', array('view' => 'quote', 
	 'header' => 'Get Quote', 'error' => $error));
		exit();
	}
	// extract array from inner array
	$quote_data = $quote_data[0];
	$title = 'Quote for '.htmlspecialchars($quote_data["symbol"]);
	
	render('template', array('view' => 'quote', 'title' => $title,
	 'header' => 'Get Quote', 'data' => $quote_data, 'error' => $error));
}
else
{
	render('template', array('view' => 'quote', 
	 'header' => 'Get Quote', 'error' => $error));
}
?>