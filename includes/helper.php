<?php
/****************************************************
 * helper.php
 *
 * CSCI S-75
 * Project 1
 * Chris Gerber
 *
 * Contains a few helpful functions.
 * This should be required by all controllers.
 ****************************************************/
require_once("constants.php");
/*
 * render() - Renders the template
 *
 * @param string $template - The name of the template to render.
 * @param array $data - An array of variables and values to pass to the template.
 */
function render($template, $data = array())
{
    $path = __DIR__ . '/../view/' . $template . '.php';
	if (file_exists($path))
    {
        extract($data);
        require($path);
    }
}
/* connect_to_database() - returns a Database handler, on error returns false.
 */
function connect_to_database()
{
	// attempt connection to Database
	try 
	{
		$dbh = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_DATABASE, DB_USER, DB_PASSWORD);
		$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	} 
	catch(PDOexception $e)
	{
		return false;		
	} 
	return $dbh;
}
/*
 * query() - Prepares a SQL statement and returns a handle to it
 * 		   - returns: on success - a statement_handle ready to be executed, on failure - false.
 *
 * @param PDO object $dbh - Database handler.
 * @param string $query_string - The query that you want to query.
 * @param array $array_values - An array with key=>value pairs where key is the placeholder
 *  and value is the value that gets plugged in to the string using bindValue().
 */
function prepare_query($dbh, $query_string, $array_values)
{
	// prepare the string for database query.
	$stmt = $dbh->prepare($query_string);
	if (!$stmt)
		return false;
	foreach ($array_values as $key => $value) {
		if (strpos($query_string,':' . $key))
		{
			$stmt->bindValue(':' . $key, $value);
		}
	}
	return $stmt;
}


function validate_form($email, $password, $password2, &$error)
{
	if (strcmp($password, $password2))
	{
		$error = "Passwords don't match.";
		return false;
	}
	elseif (preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/', $email) === 0)
	{
		$error = "Email is not valid.";
		return false;
	}
	elseif (strlen($password) < 7 || preg_match('/((^[0-9]+[a-z]+)|(^[a-z]+[0-9]+))+[0-9a-z]+$/i', $password) === 0)
	{
		$error = "Password is not valid.";
		return false;
	}
	else
	{
		return true;
	}

}
/*
 * This piece of code makes sure the user is logged in before he can access any page
 * and if he isn't it redirects him to the login page.
 */
if (isset($_GET["page"]))
{
	$page = $_GET["page"];
	$everyone = array('login', 'register', 'logout');
	if (!in_array($page, $everyone))
	{
		if (empty($_SESSION["userid"]))
		{
			header("Location: http://project1/");
			exit;
		}
	}
	
}


?>

