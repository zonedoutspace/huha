<form method="POST" action="buy" onsubmit="return validateBuyForm();">
  	<div class="field text">
        <label for="symbol">Symbol</label>
        <input type="text" name="param" placeholder="Stock Symbol" id="symbol">
        <input type="text" name="amount" placeholder="Amount" id="amount">
    </div>
    <div class="field text">
        <input type="submit" value="Submit">
    </div>
</form>

<script type='text/javascript'>
// <! [CDATA[

function validateBuyForm()
{
	isValid = true;
	
	// check if the symbol is alphabetic and the amount is numeric
	symbolField = $("input[name=param]");
	amountField = $("input[name=amount]");
	if (!symbolField.val().match(/^([.a-zA-Z]+)$/))
	{
		alert("Symbol can only be Alphabetic.");
		isValid = false;
	}
	elseif (!amountField.val().match(/^([0-9]+)$/) || amountField.val() < 0)
	{
		alert("Amount can only be positive integers.");
		isValid = false;
	}
		
	return isValid;
}

// set the focus to the email field (located by id attribute)
$("input[name=param]").focus();

// ]] >
</script>
