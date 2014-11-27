<?php
/*********************************
 * model.php
 *
 * CSCI S-75
 * Project 1
 * Chris Gerber
 *
 * Model for users and portfolios
 *********************************/
require_once ("../includes/constants.php");
/*
 * login_user() - Verify account credentials and create session
 *
 * @param string $email
 * @param string $password
 */
function login_user($email, $password, &$error) {
	$dbh = connect_to_database();
	if (!$dbh) {
		$error = "Could not connect to Database.";
		return false;
	}
	$values = array("email" => $email, "password" => hash("SHA1", $password));
	$stmt   = prepare_query($dbh, "SELECT uid FROM users WHERE LOWER(email)=:email AND password=:password", $values);
	if (!$stmt) {
		$dbh   = null;
		$error = "Incorrect SQL statement.";
		return false;
	}
	$stmt->execute();
	$result = $stmt->fetch(PDO::FETCH_ASSOC);
	if (isset($result["uid"])) {
		$dbh = null;
		return $result["uid"];
	} else {
		$error = "Username or password don't match.";
		return false;
	}
}

/*
 * get_user_shares() - Get portfolio for specified userid
 *
 * @param int $userid
 */
function get_user_shares($userid, &$error) {
	// connect to database with PDO
	$dbh = connect_to_database();
	if ($dbh) {
		// get user's portfolio
		$values = array('userid' => $userid);
		$stmt   = prepare_query($dbh, "SELECT symbol, amount FROM portfolio WHERE uid=:userid", $values);
		if (!$stmt) {
			$error = "Error preparing statement.";
			$dbh   = null;
			return false;
		}
		if ($stmt->execute()) {
			$result = array();
			while ($row = $stmt->fetch()) {
				array_push($result, $row);
			}
			if (!count($result)) {
				$error = 'No shares available.';
				$dbh   = null;
				return false;
			}
			$dbh = null;
			return $result;
		}
	}
	$error = 'Could not execute.';
	// close database and return null
	$dbh = null;
	return null;
}

/*
 * get_quote_data() - Get Yahoo quote data for a symbol
 *
 * this function doubles down to work two ways for quote page it can be sent one
 * stock and for portfolio it takes multiple stocks so not all parts of the function
 * are for both cases.
 *
 * @param string $symbol
 */
function get_quote_data($symbol, &$error) {
	if (!empty($symbol)) {
		if (!preg_match('/^([.A-Za-z])+((\+[.A-Za-z]+)*\+?)$/', $symbol)) {
			$error = 'Invalid symbol.';
			return false;
		}
		$result = array();
		$url    = "http://download.finance.yahoo.com/d/quotes.csv?s=".trim($symbol)."&f=sl1n&e=.csv";
		$handle = fopen($url, "r");
		if (!$handle) {
			$error = 'Not a valid URL.';
			return false;
		}
		$result = array();
		while ($row = fgetcsv($handle)) {
			if (isset($row[1])) {
				array_push($result, array("symbol" => $row[0],
						"last_trade"                     => $row[1],
						"name"                           => $row[2]));
			}
		}
		fclose($handle);

		foreach ($result as $stock) {
			if (!isset($stock['last_trade']) || $stock['last_trade'] <= 0.0000) {
				$error = "No valid symbol was provided, or no quote data was found.";
				return false;
			}
		}

		return $result;
	}
}

/*
 * register_user() - Create a new user account
 *
 * @param string $email
 * @param string $password
 *
 * @return string $error
 */
function register_user($email, $password, &$error) {
	$dbh = connect_to_database();
	if (!$dbh) {
		$error = 'could not connect to database';
		$dbh   = null;
		return false;
	}
	$values      = array('email' => $email, 'password' => hash('SHA1', $password));
	$select_stmt = prepare_query($dbh, "SELECT * FROM users WHERE email=:email", $values);
	if (!$select_stmt) {
		$error = 'Not valid SQL statement #SELECT';
		return false;
	}
	$insert_stmt = prepare_query($dbh, "INSERT INTO users (email, password, money) VALUES (:email, :password, 10000)", $values);
	if (!$insert_stmt) {
		$error = 'Not valid SQL statement #INSERT';
		return false;
	}
	$dbh->beginTransaction();
	$select_stmt->execute();
	if ($select_stmt->rowCount() < 1) {
		$insert_stmt->execute();
		$dbh->commit();
		$dbh = null;
		return true;
	} else {
		$dbh->rollback();
		$error = 'Your seem to have already been registered.';
		return false;
	}
}

function get_user_balance($userid, &$error) {
	$dbh = connect_to_database();
	if (!$dbh) {
		$error = 'could not connect to database';
		$dbh   = null;
		return false;
	}
	$values = array('userid' => $userid);
	$stmt   = prepare_query($dbh, "SELECT money FROM users WHERE uid=:userid", $values);
	if (!$stmt) {
		$error = 'Not valid SQL statement #SELECT';
		return false;
	}
	$stmt->execute();
	$result = $stmt->fetch(PDO::FETCH_ASSOC);
	if (isset($result['money'])) {
		$dbh = null;
		return $result['money'];
	}
}

function buy_shares($userid, $symbol, $amount, &$error) {
	if (!(strpos(htmlspecialchars($amount), '.') === false) || $amount < 1) {
		$error = 'Not a valid amount of that share.';
		return false;
	}
	// Here's where ALL the magic happens.
	$balance = get_user_balance($userid, $error);
	if (!$balance) {
		return false;
	}
	$data = get_quote_data($symbol, $error);
	if (!$data) {
		return false;
	}

	extract($data[0]);
	$total = $last_trade*$amount;
	if ($balance < $total) {
		$error = 'Sorry not enough money.(we\'re not a credit card company)';
		return false;
	}

	$dbh = connect_to_database();
	if (!$dbh) {
		$error = 'Could not connect to database.';
		return false;
	}
	try {
		$dbh->beginTransaction();
		$values       = array('uid' => $userid, 'total' => $balance-$total, 'symbol' => $symbol, 'amount' => $amount);
		$update_money = prepare_query($dbh, "UPDATE users SET money=:total WHERE uid=:uid", $values);
		$update_money->execute();
		$select_stock = prepare_query($dbh, "SELECT symbol FROM portfolio WHERE uid=:uid AND symbol=:symbol", $values);
		$select_stock->execute();
		if ($select_stock->rowCount() < 1) {
			$insert_stock = prepare_query($dbh, "INSERT INTO portfolio
				(uid, symbol, amount) VALUES (:uid, :symbol, :amount)", $values);
			$insert_stock->execute();
		} else {
			$update_stock = prepare_query($dbh, "UPDATE portfolio
				SET amount=amount + :amount WHERE uid=:uid AND symbol=:symbol", $values);
			$update_stock->execute();
		}
		$dbh->commit();
		$dbh = null;

	} catch (PDOException $e) {
		$dbh->rollback();
		$dbh   = null;
		$error = "problem with queries.";
		return false;
	}
	return true;

}

function sell_shares($userid, $symbol, &$error) {
	// get the current value
	$price = get_quote_data($symbol, $error);
	$price = $price[0]['last_trade'];

	// affect the database as needed
	$dbh = connect_to_database();
	if (!$dbh) {
		$error = 'Error connecting to Database.';
		return false;
	}
	$values = array('uid' => $userid, 'symbol' => $symbol);
	$stmt   = prepare_query($dbh, "SELECT amount FROM portfolio WHERE uid=:uid AND symbol=:symbol", $values);
	if (!$stmt->execute()) {
		$error = 'Error getting data from Database.';
		$dbh   = null;
		return false;
	}

	$result          = $stmt->fetch(PDO::FETCH_ASSOC);
	$values['total'] = $result['amount']*$price;

	$dbh->beginTransaction();
	$stmt = prepare_query($dbh, "DELETE FROM portfolio WHERE uid=:uid AND symbol=:symbol", $values);
	if (!$stmt->execute()) {
		$error = 'Error getting data from Database.';
		$dbh->rollback();
		$dbh = null;
		return false;
	}
	$stmt = prepare_query($dbh, "UPDATE users SET money=money+:total WHERE uid=:uid", $values);
	if (!$stmt->execute()) {
		$error = 'Error getting data from Database.';
		$dbh->rollback();
		$dbh = null;
		return false;
	}
	$dbh->commit();
	$dbh = null;
	return true;
}

/*
 * get_user_portfolio()
 *
 * Gets all the data -efficiently- needed for the portfolio controller
 *
 */
function get_user_portfolio($uid, &$error) {
	$dbh = connect_to_database();
	if (!$dbh) {
		return false;
	}
	$values = array('uid' => $uid);
	$stmt   = prepare_query($dbh, "SELECT users.money, portfolio.symbol, portfolio.amount
								 FROM users
								 JOIN portfolio ON users.uid=:uid AND portfolio.uid=users.uid
								 ", $values);
	if (!$stmt) {
		echo 'error preparing statement.';
		$dbh = null;
		return false;
	}
	if (!$stmt->execute()) {
		echo 'error getting data from Database.';
		$dbh = null;
		return false;
	}
	$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

	$url            = '';
	$current_prices = array();
	foreach ($data as $row) {
		$url .= $row["symbol"].'+';
		$current_prices[$row['symbol']] = $row['amount'];
	}
	$symbols = get_quote_data($url, $error);
	if (!$symbols) {return false;}

	$total = 0;

	$i = 0;
	while (isset($data[$i])) {
		$stock = $symbols[$i];
		$row   = &$data[$i];
		if ($row['symbol'] === $stock['symbol']) {
			$row['price'] = $stock['last_trade'];
			$row['total'] = $stock['last_trade']*$row['amount'];
			$total += $row['total'];
		} else {
			echo 'Something went wrong.'.$row['symbol'].$stock['symbol'];
			return false;
		}
		$i++;
	}

	$data[] = $total;
	return $data;
}