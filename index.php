<?php
require_once("models/config.php");
$account = $loggedInUser->display_username;
$id = $loggedInUser->user_id;
if(isTORnode()){
	die("Due to legal restrictions users using TOR Browser are not allowed to access this website.");
}
if(isIPbanned()){
	die("ip address is banned. You can appeal this decision by contacting the administrator at admin@openex.pw");
}
if(isBant($id)){
	echo '<meta http-equiv="refresh" content="0; URL=index.php?page=logout">';
}
if(isMaintenanceDisabled()) {
}else{
	if(!isUserAdmin($id)) {
	echo '<meta http-equiv="refresh" content="0; URL='.$maint_url.'">';
	die();
	}
}
?>
<html>
<head>
	<title><?php echo $title ?></title>
	<meta name="keywords" 
	content="cryptocurrency, bitcoin, trading, altcoin, OpenEx, openex.pw, 
	litecoin, feathercoin, opensourcecoin, gldcoin, protoshares, 
	memorycoin, radioactivecoin, 42coin, primecoin, unobtanium, novacoin, 
	nanotokens, skeincoin, blakecoin, mincoin, megacoin, scrypt, 
	sha-256, open source, crypto exchange">
	<meta name="description" content="OpenEx.pw, the one and only open source cryptocurrency exchange for all your trading needs.">
	<link rel="shortcut icon" href="assets/img/favicon.ico" type="image/x-icon" />
	<link rel="stylesheet" type="text/css" href="assets/css/style.css">
	<link rel="stylesheet" type="text/css" href="assets/css/trade.css">
	<link rel="stylesheet" type="text/css" href="assets/css/tables.css">
	<link rel="stylesheet" href="assets/font-awesome/css/font-awesome.min.css" />
	<script src="assets/js/jquery.min.js" type="text/javascript"></script>
	<script type="text/javascript">
	
	$('document').ready(function() {
		$('#chat_toggle').click(function() {
			$('#chatbox').slideToggle();
		});
	});
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

	//page transitions
    $(document).ready(function() {
			
        $("#contentloader").slideDown(500, function() {
            $('.spinner').fadeOut();
        });
		
		
        $("a").click(function(event){

            event.preventDefault();

            linkLocation = this.href;
            
            $("#contentloader").slideUp(500, function() {
                $('.spinner').fadeIn(0, redirectPage);
            });    
        });
         
        function redirectPage() {
            window.location = linkLocation;
        }
    });
	$(document).ready(function() {

		var refreshChat = function() 
		{ 
			setTimeout( 
				function() 
		  		{
		  			var moveDown = false;

		  			if($('#messages').scrollTop() == ($('#messages')[0].scrollHeight - $('#messages')[0].offsetHeight)) {
		  				moveDown = true;
		  			}

		   			$('#messages').load('ajaxLOAD.php', 
						function() 
						{
							if(moveDown) {
								$('#messages').animate({scrollTop: $('#messages')[0].scrollHeight}, 1000);
							}
						}
		   			);
		   			refreshChat();
		  		}, 
		  2000);
		};
		refreshChat();

		<?php 

			// Start Matt Smiths Code
			if(isset($_GET['page'])) {
				if($_GET['page'] == 'trade') {
					?>

					var MarketId = <?php echo($_GET['market']); ?>;
					var refreshOrderbooks = function() {
						setTimeout(function() {
							$("#sellorders").load('./pages/open_orders_from.php?market=' + MarketId,
								function() 
								{
									// Add animation here if you'd like
								});

							$("#buyorders").load('./pages/open_orders_to.php?market=' + MarketId,
								function() 
								{
									// Add animation here if you'd like
								});

							refreshOrderbooks();
						}, 
						10000);
					}
					refreshOrderbooks();

					<?php
				}
			}

			// End Matt Smiths Code
		?>
		$('#ajaxPOST').submit(function() {
			$.post('ajaxPOST.php', $('#ajaxPOST').serialize(), function(data){
						
                            $('#message').val('');
                            $('#messages').load('ajaxLOAD.php', function() {
                                $('#messages').animate({
									
                                    scrollTop: $('#messages')[0].scrollHeight
                                  }, 1000);
                            }); 
			});
			return false; 
		});

    });
	

	
	$('#message').keypress(function(event){
    var char = String.fromCharCode(event.which)
    var txt = $(this).val()
    if (! txt.match(/^[^A-Za-z0-9+#\-\.]+$/)){
        $(this).val(txt.replace(char, ''));
    }
	});
	

	</script>
</head>

<body>
	<div id="header" class="semi-translucent color">
		<div id="logo">
		<img src="assets/img/OpenEx.png" height="30" alt="OpenEx.pw"/>
		</div>
		<div id="menu_nest" class="menu_nest">
			<ul class="nobullets mainmenu">
				<li><a href="index.php?page=home" title="home"><i class="fa fa-home"></i></a></li>
				<li><a href="index.php?page=about" title="about"><i class="fa fa-info"></i></a></li>
				<?php
				if(!isUserLoggedIn()){ 
				echo
				'
				<li><a href="index.php?page=login" title="login"><i class="fa fa-power-off"></i></a></li>
				<li><a href="index.php?page=register" title="register"><i class="fa fa-edit"></i></a></li>
				';
				}else{
				echo
				'
				<li><a href="index.php?page=logout" title="logout"><i class="fa fa-power-off"></i></a></li>
				';
				}
				?>
				<!--<li><a href="https://openex.mobi" title="mobile sit"><i class="fa fa-mobile"></i></a></li>
				<li><a href="https://openex.info" target="_blank" title="forums"><i class="fa fa-comments-o"></i></a></li>-->
				<li><a class="blank" href="https://twitter.com/_OpenEx_" target="_blank" title="Follow us on Twitter"><i class="fa fa-twitter"></i></a></li>
				<li><a class="blank" href="https://github.com/r3wt/openex.git" target="_blank" title="View source on Github"><i class="fa fa-github"></i></a></li>
				<?php
				if(isUserLoggedIn()){ 
				echo 
				'
				<li><a href="index.php?page=account" title="account balances"><i class="fa fa-suitcase"></i></a></li>
				<li><a href="index.php?page=account_history" title="account history"><i class="fa fa-book"></i></a></li>
				<li><a href="index.php?page=support" title="support system"><i class="fa fa-warning"></i></a></li>
				<li><a href="index.php?page=preferences" title="account preferences"><i class="fa fa-cogs"></i></a></li>
				<li><a href="index.php?page=api" title="API"><i class="fa fa-code"></i></a></li>
				';
				} else {
				
				}

				if(isUserAdmin($id)){ 
				echo
				'
				<li><a href="index.php?page=admin" title="admin area"><i class="fa fa-terminal"></i></a></li>
				<li><a href="index.php?page=withdrawalqueue" title="withdrawal queue"><i class="fa fa-clock-o"></i></a></li>
				<li><a href="index.php?page=withdrawalhist" title="withdrawal history"><i class="fa fa-check-square"></i></a></li>
				<li><a href="index.php?page=site_monitor" title="site monitor"><i class="fa fa-tachometer"></i></a></li>
				<li><a href="index.php?page=moderate" title="moderator area"><i class="fa fa-gavel"></i></a></li>
				<li><a href="index.php?page=access_violations" title="access violations"><i class="fa fa-flag-o"></i></a></li>
				<li><a href="index.php?page=siteconfig" title="Enable/Disable Features"><i class="fa fa-lock"></i></a></li>
				<li><a href="index.php?page=addnewcoin" title="Add a coin"><i class="fa fa-plus"></i></a></li>
				';
				} else {
				
				}

				if(isUserMod($id)){
				echo'
				<li><a href="index.php?page=moderate" title="moderator area"><i class="fa fa-gavel"></i></a></li>
				';
				} else {
				
				}
				?>
			</ul>
		</div>
	</div>
	
	<div id="markets" class="semi-translucent color2">
		<center><i class="fa fa-bar-chart-o fa-2x" style="color: #fff;">Markets</i></center>
		<ul class="nobullets">
		<?php
			$sqlx = mysql_query("SELECT * FROM Wallets ORDER BY `Acronymn` ASC");

			while ($row = @mysql_fetch_assoc($sqlx)) {
			if($row["Market_Id"] == 0)
			{
				
			}
			else
			{
				$sqls_2 = @mysql_query("SELECT * FROM Trade_History WHERE `Market_Id`='". $row["Id"] . "' ORDER BY Timestamp DESC");
				
				$last_trade = @mysql_result($sqls_2,0,"Price");
				
			?>
			<li class='left' ><p title="Trade <?php echo $row["Name"] ?>" onclick="window.location = 'index.php?page=trade&market=<?php echo $row["Id"]; ?>';"><?php echo $row["Acronymn"];?> / BTC<?php echo "<font class='price'>".sprintf("%2.8f",$last_trade)."</font>"; ?></p></li>
			<?php
			
			}
			}
		?>
		</ul>
	</div>
	
	<div id="main_content">
		<center>
		<div id="contentloader">
			<div id="contentchild">
			<center>
				<div class="spinner"><i class="fa fa-spinner"></i><i class="fa fa-spinner"></i><i class="fa fa-spinner"></i><i class="fa fa-spinner"></i><i class="fa fa-spinner"></i><i class="fa fa-spinner"></i><i class="fa fa-spinner"></i><i class="fa fa-spinner"></i><i class="fa fa-spinner"></i><i class="fa fa-spinner"></i><i class="fa fa-spinner"></i><i class="fa fa-spinner"></i></div>
				<?php
					if($_GET["page"] == "")
					{
						if(isUserLoggedIn())
						{
							include("pages/account.php");
						} 
						else 
						{
							include("pages/home.php");
						}
					} 
					else
					{
						$p="pages/".basename($_GET['page']).".php";
			            if(file_exists($p)){
							include($p);  
			            }else{
							exit('Page Not Found');
			            }
					}
				?>
			</center>
			</div>
		</div>
		</center>
	</div>
	
	<div id="bar" class="semi-translucent color">
		<button id="chat_toggle" class="semi-translucent color toggle"><img src="assets/img/chat_icon.png" height="24" alt="Chat"></button>
	</div>
	
	<div id="chatbox" class="color3 border-no-bottom glow">
			<hr class="five" />
			<div id="messages"></div>
			<?php
			if (isUserLoggedIn()){ 
			
				echo'
				<hr class="five" />
				<div id="message-wrap">
				<form id="ajaxPOST" history="off" autocomplete="off">
					<div class="fields">
						<input type="text" id="message" maxlength="255" name="message" />
					</div>
					<div class="actions">
						<input type="submit" id="chat-submit" value="Post Message" />
					</div>
				</form>
				</div>
				';
			
			} else {
			
				echo'
				<hr class="five" />
				<div id="LoggedOut"><b><center>You must be logged in to chat</center></b></div>
				';
			
			} 
			
			?>
		</div>
</body>

</html>
