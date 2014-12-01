<?php

require_once('../includes/helper.php');

unset($_SESSION['userid']);
session_destroy();

render('template', array('view' => 'login', 'title' => 'Login', 'header' => 'Log in'));
?>
