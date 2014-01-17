function per(num, percentage){
	  return num*percentage/100;
	}
	function calculateFees1(x)
	{
	var total = document.getElementById('Amount').value;
	var earn = document.getElementById('Amount').value * document.getElementById('price1').value;
		$.get("system/calculatefees2.php?P=" + total,function(data,status){
		document.getElementById('fee1').value = data;
		});

		$.get("system/calculatefees.php?P=" + earn,function(data,status){
		  document.getElementById('earn1').value = data;
		});
	}
	function calculateFees2()
	{
	var total = document.getElementById('Amount2').value;
	var earn = document.getElementById('Amount2').value * document.getElementById('price2').value;
		$.get("system/calculatefees.php?P=" + earn,function(data,status){
		  document.getElementById('fee2').value = data;
		});

	}

