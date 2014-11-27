<?php
/*******************
 * quote.php
 *
 * CSCI S-75
 * Project 1
 * Velvel Marasow
 *
 * Register controller
 *******************/
require_once ('../model/model.php');
require_once ('../includes/helper.php');

// validate user input
if (isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["re_password"])) {
	if (!validate_form($_POST['email'], $_POST['password'], $_POST['re_password'], $error)) {
		render('template', array('view' => 'register', 'title' => 'Register',
				'header'                      => 'Register', 'error'                      => $error));
	} else {
		$email    = $_POST["email"];
		$password = $_POST["password"];
		$password = hash("SHA1", $password);
		// function declaration is in model does all the error checking there and returns it in the error var.
		$registered = register_user($email, $password, $error);
		if (!$registered) {
			render('template', array('view' => 'register', 'title' => 'Register',
					'header'                      => 'Register', 'error'                      => $error));
		} else {
			render('template', array('view' => 'login', 'title' => 'Login', 'header' => 'Log in'));
		}
	}
} else {
	render('template', array('view' => 'register', 'title' => 'Register', 'header' => 'Register'));
}