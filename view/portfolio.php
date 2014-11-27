<table id="table_view">
    <tr>
        <th>Symbol</th>
        <th>Shares</th>
        <th>Price</th>
        <th>Total</th>
    </tr>
<?php
setlocale(LC_MONETARY, 'en_US.UTF-8');
//$prices = isset($data['prices']) ? $data['prices'] : null;
if (isset($data))
{
	$total = array_pop($data);
	foreach ($data as $row)
	{
	    print "<tr>";
	    print "<td>" . htmlspecialchars($row["symbol"]) . "</td>";
	    print "<td>" . htmlspecialchars($row["amount"]) . "</td>";
	    print "<td>" . htmlspecialchars(money_format('%i', $row['price'])) . "</td>";
	    print "<td>" . htmlspecialchars(money_format('%i', $row['total'])) . "</td>";
	    print "<td><a href='/sell/" . $row['symbol'] . "' class='list_item'>Sell</a></td>";
	    print "</tr>";
	}
	echo	"<table id='table_view'>
			<th>Total: </th>
			<td>" . htmlspecialchars(money_format('%i', $total)) . "</td>
		</table>

		<table id='table_view'>
			<th>Balance: </th>
			<td>" . htmlspecialchars(money_format('%i', $data[0]['money'])) . "</td>
		</table>
	</table>";
}
