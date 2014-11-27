<!DOCTYPE html>
<html>
    <head>
        <title><?= htmlspecialchars($title) ?></title>
		<script type='text/javascript' src='https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js'></script>
		<link type="text/css" href="http://project1/css/styles.css" rel="stylesheet"/>
    </head>
    <body>
    <?php if ($home_link) : ?>
		<a href="/">Home page</a> <br />
	<?php endif; ?>