<?php		
	require_once('../includes/helper.php');

	render('header', array('title' => isset($title) ? $title : 'C$75 Finance','home_link' => strcmp($view, 'home') ? true : false));
	
	
	echo "<div id='container'>";
	if (!preg_match('(login|register|home)', $view)) {
			echo "<div id='nav_list'>
				<ul>
					<li ><a href='/quote' class='list_item'>Get a quote</a></li>
					<li ><a href='/portfolio' class='list_item'>View Portfolio</a></li>
					<li ><a href='/buy' class='list_item'>Buy a stock</a></li>
					<li ><a href='/logout' class='list_item'>Logout</a></li>
				</ul>
			</div>";
		}
?>
		
<div id='frame'>
	<h2 id='header' style='font-family:sans-serif'><?= $header ?></h2>
	<?php render($view, array('data' => isset($data) ? $data : array())); ?>
</div>
<?php
	if (isset($error))
	echo "<p>{$error}<p/>"; 
	echo "</div>";

render('footer');

?>
