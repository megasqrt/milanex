<?php
if ($_SERVER['SERVER_NAME'] != "milancoin.com" or $_SERVER['SERVER_PORT'] != "443"){
       header('location:https://milancoin.com');
}
/**~2014 milanoin.org Developers. All Rights Reserved.~**->>
 *Licensed Under the MIT License : http://www.opensource.org/licenses/mit-license.php
 *
 *WARRANTY INFORMATION:
 *THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 *AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *THE SOFTWARE. 
 ***************************************/
require_once("models/config.php");
require_once ('system/csrfmagic/csrf-magic.php');
if(isTORnode()){
	die("Due to legal restrictions users using TOR Browser are not allowed to access this website.");
}
if(isIPbanned()){
	die("ip address is banned. You can appeal this decision by contacting an administrator at support@milancoin.com");
}
if(isMaintenanceDisabled()) {
}else{
	if((isset($_GET["BYPASS"])?$_GET["BYPASS"]:"") != "")
	{
		session_start();
		$_SESSION["BYPASS"] = $_GET["BYPASS"];
	}
	//if(isUserAdmin($id)) {}
	//session bypass key for maintenance mode.
	if((isset($_SESSION["BYPASS"])?$_SESSION["BYPASS"]:"") == "") {
	}else{
		echo '<meta http-equiv="refresh" content="0; URL='.$maint_url.'">';
		die();
	}
}
if(isUserLoggedIn()){
	$id       = addslashes(strip_tags($loggedInUser->user_id));
	$account  = addslashes(strip_tags($loggedInUser->display_username));
	$loggedin = mysql_query("UPDATE `userCake_Users` SET `LastTimeSeen` = NOW() WHERE `User_ID` = '$id'");
	if(isBant($id)){
		echo '<meta http-equiv="refresh" content="0; URL=index.php?page=logout">';
	}
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">  
<title><?php echo $title ?></title>
<meta name="keywords" 
content="cryptocurrency, bitcoin, trading, altcoin, MilanEx, milancoin.com, 
litecoin, feathercoin, opensourcecoin, gldcoin, protoshares, 
memorycoin, radioactivecoin, 42coin, primecoin, unobtanium, novacoin, 
nanotokens, skeincoin, blakecoin, mincoin, megacoin, scrypt,a-256, open source, crypto exchange">
<meta name="description" content="MilanCoin, the cryptocurrency exchange for all your trading needs.">
<link rel="shortcut icon" href="assets/img/favicon.ico" type="image/x-icon" />
<link rel="stylesheet" href="assets/font-awesome/css/font-awesome.min.css" />
<link rel="stylesheet" href="assets/css/stylez0r.min.css" />
<link rel="stylesheet" href="assets/css/tabber.css" />
<script src="assets/js/jquery.min.js" type="text/javascript"></script>
<script src="assets/js/jquery.cookie.js" type="text/javascript"></script>
<script type="text/javascript" src="assets/js/tabber.min.js">
</script>
<?php
$_GET["page"] = isset($_GET["page"])?$_GET["page"]:"";
 if($_GET["page"] == "trade")
{
	?>
	<script type="text/javascript" src="/assets/js/canvasXpress.min.js"></script>
	<?php
}
?>
<script type="text/javascript">
<?php if($_GET["page"] == "trade")
{
	echo "
	var head = document.getElementsByTagName('head')[0];
	function loadCharts()
	{ 		
		var script= document.createElement('script');
		script.type= 'text/javascript';
		script.src= '/ajax/getchartjs.lua?id=" . intval($_GET["market"]) . "';
		head.appendChild(script);
	}
	"; 
	}
	?>
	$('document').ready(function() {
		$(function() {  
			var pull        = $('#pull');  
				menu        = $('nav ul'); 
				logo        = $('#logo');
				menuHeight  = menu.height();  
		  
			$(pull).on('click', function(e) {  
				e.preventDefault();  
				menu.slideToggle("fast");  
			});  
		}); 
		var main = $('#main_content');
		var foot = $('#footerl');
		
		$(window).resize(function(){  
			var w = $(window).width();  
			if(w > 1200) {  
				menu.removeAttr('style');  
				logo.removeAttr('style');
			}
			if(w > 800) {  
				menu.removeAttr('style');  
				logo.removeAttr('style');
				foot.removeAttr('style');
				main.removeAttr('style');
			}
			if(w < 1200) {
				main.removeAttr('style');
			}
			if(w < 800) {
				main.removeAttr('style');
			}
		}); 
		// Start Matt Smiths Chat Loading

		var moveChatDown = true;
		var chatMoved = false;
		var chatReloadTime = 240000;

		$('#messages').scroll(function () {
			chatMoved = true;
			if($('#messages')[0].scrollHeight - $('#messages').scrollTop() == $('#messages').outerHeight()) {
				moveChatDown = true;
			} else {
				moveChatDown = false;
			}
		});

		var refreshChat = function() 
		{
			setTimeout( 
				function() 
				{
					if(chatMoved)
					{
						if($('#messages')[0].scrollHeight - $('#messages').scrollTop() == $('#messages').outerHeight()) {
							moveChatDown = true;
						} else {
							moveChatDown = false;
						}
						chatMoved = false;
					}

					$('#messages').load('ajax.php?do=load', 
						function() 
						{
							if(moveChatDown && (chatMoved == false)) {
								$('#messages').animate({scrollTop: $('#messages')[0].scrollHeight}, 60000);
							}
						}
					);
					refreshChat();
				}, 
		 	chatReloadTime);
		};
		refreshChat();
		
		$('#chat_toggle').click(function() {
			$('#chatbox').slideToggle('slow');
			
			if(chatReloadTime == 2000)
			{
				chatReloadTime = 1000;
			} else {
				chatReloadTime = 5000;
			}
			
		});
		
		$('#ajaxPOST').submit(function() {
			$.post('ajax.php?do=post', $('#ajaxPOST').serialize(), function(data){
						
                            $('#message').val('');
                            $('#messages').load('ajax.php?do=load', function() {
                                $('#messages').animate({
									
                                    scrollTop: $('#messages')[0].scrollHeight
                                  }, 120000);
                            }); 
			});
			return false; 
		});
		
		
	});
	
	function per(num, percentage){
	  return num*percentage/100;
	}
	function calculateFees1()
	{
	var total = document.getElementById('Amount').value;
	var earn = document.getElementById('Amount').value * document.getElementById('price1').value;

		$.get("system/calculatefees.php?P=" + earn,function(data,status){
		  document.getElementById('earn1').value = data;
		});
	}
	function calculateFees2()
	{
	var total = document.getElementById('Amount2').value;
	var earn = document.getElementById('Amount2').value / document.getElementById('price2').value;
		$.get("system/calculatefees.php?P=" + earn,function(data,status){
		  document.getElementById('fee2').value = data;
		});
	}
	function calculateFees3()
	{	
	var earn = document.getElementById('fee2').value * document.getElementById('price2').value;

		$.get("system/reversefees.php?P=" + earn,function(data,status){
		  document.getElementById('Amount2').value = data;
		});
	}
	function calculateFees4()
	{	
	var earn = document.getElementById('earn1').value / document.getElementById('price1').value;

		$.get("system/reversefees.php?P=" + earn,function(data,status){
		  document.getElementById('Amount').value = data;
		});
	}



	//page transitions
    $(document).ready(function() {
			
        $("#main_content").fadeIn(100, function() {
        });
		$('a[target=_blank]').click(function(e){
			//do nothing and allow the target=_blank
		});
		
		//redirect the page in a non chalant way
        $("a").click(function(event){
            event.preventDefault();
			event.stopPropagation();
            linkLocation = this.href;
            
			$("#main_content").val('');
            redirectPage();
               
        });
         
        function redirectPage() {
            window.location = linkLocation;
        }
    });
	$(document).ready(function() {

		

		<?php 

			// Start Matt Smiths Code
			if(isset($_GET['page'])) {
				if($_GET['page'] == 'trade') {
					?>
					var MarketId = <?php echo json_encode($_GET['market']); ?>;
					$("#sellorders").html('');
					$("#sellorders").load('./pages/open_orders_from.php?market=' + MarketId +'');
					$("#buyorders").html('');
					$("#buyorders").load('./pages/open_orders_to.php?market=' + MarketId +'');	
					
					var refreshBalances = function() {
						secbal = $("#secondaryBalance").load("./ajax.php?do=trade_balances&coin=" + MarketId);
						btcbal = $("#btcBalance").load("./ajax.php?do=trade_balances&coin=1");
					};
					
					$("#btcBalance").click(function(){
						refreshBalances();
						var updatedbalance1 = $("#btcBalance").text();
						$("#Amount2").val(updatedbalance1);
						calculateFees2()
					});
					
					$("#secondaryBalance").click(function(){
						refreshBalances();
						var updatedbalance2 = $("#secondaryBalance").text();
						$("#Amount").val(updatedbalance2);
						calculateFees1(this);
					});		
					
					function loadCharts()
					{   
						var script= document.createElement('script');
						script.type= 'text/javascript';
						script.src= '/system/getchartjs.php?id=' + MarketId +'';
						head.appendChild(script);
					}
					 
					$("#chartshow").click(function(){
						loadCharts();
					})
					
					
					var refreshOrderbooks = function() {
						setTimeout(function() {
							$("#sellorders").load('./pages/open_orders_from.php?market=' + MarketId,
								function() 
								{
									refreshBalances();
								});
							
							$("#buyorders").load('./pages/open_orders_to.php?market=' + MarketId,
								function() 
								{
									refreshBalances();
								});

							refreshOrderbooks();
						}, 
						5000);
					}
					refreshOrderbooks();
					/*
					$('#buyorder').submit(function() {
						$.post('ajax.php?do=buyorder&coin=' + MarketId, $('#buyorder').serialize(), function(data){
							
						});
						var buy = $('#Buy');
							buy.disabled=1;
							buy.value='Submitting trade...';
							refreshOrderbooks();
							refreshBalances();
							buy.disabled=0;
						return false; 
					});
					
					$('#sellorder').submit(function() {
						$.post('ajax.php?do=sellorder&coin=' + MarketId, $('#sellorder').serialize(), function(data){
							
						});
						var sell = $("#Sell");
							sell.disabled=1;sell.value='Submitting trade...';
							refreshOrderbooks();
							refreshBalances();
							sell.disabled=0;
						return false; 
					});
					*/


					
					
				<?php
				}
			}

			// End Matt Smiths Code
		?>
		
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
	<div id="loading">
		<!--the loading div-->
	</div>
	<!--menu-->
	<nav class="clearfix">  
		<a id="logo" href="https://milancoin.com"><img src="assets/img/OpenEx.png" height="40" /></a>
		<ul class="clearfix">  
			<li><a  href="index.php?page=home" title="home"><i class="fa fa-home"></i></a></li>
			<li><a  href="index.php?page=about" title="about"><i class="fa fa-info"></i></a></li>
			<?php
			if(!isUserLoggedIn()){ 
				echo
				'
				<li><a  href="index.php?page=login" title="login"><i class="fa fa-power-off"></i></a></li>
				<li><a  href="index.php?page=register" title="register"><i class="fa fa-edit"></i></a></li>
				';
			}else{
				echo
				'
				<li><a  href="index.php?page=logout" title="logout"><i class="fa fa-power-off"></i></a></li>
				<li><a  href="index.php?page=account" title="account"><i class="fa fa-suitcase"></i></a></li>
				';
				if(isUserAdmin($id)){ 
					echo
					'
					<li><a  href="index.php?page=moderate" title="moderator area"><i class="fa fa-terminal"></i></a></li>
					<li><a  href="index.php?page=withdrawalqueue" title="withdrawal queue"><i class="fa fa-clock-o"></i></a></li>
					<li><a  href="index.php?page=site_monitor" title="site monitor"><i class="fa fa-tachometer"></i></a></li>
					<li><a  href="index.php?page=siteconfig" title="Enable/Disable Features"><i class="fa fa-lock"></i></a></li>	
					';
				}
				if(isUserMod($id)){
					echo'
					<li><a href="index.php?page=moderate" title="moderator area"><i class="fa fa-gavel"></i></a></li>
					';
				}
			}
			?>   
		</ul>  
		<a href="#" id="pull"><img src="assets/img/OpenEx.png" height="40" /></a>  
	</nav> 	
	<!--user content area-->
	
	<!--market area-->
	<div id="markets">
		<div id="mhead">
		<hr class="five" />
		<center><i class="fa fa-bar-chart-o fa-2x tshadow" style="color: #fff;"><font class="mhead tshadow">Markets</font></i></center>
		<hr class="five" />
		</div>
		<ul class="nobullets markets">
		<?php
			$sqlx = mysql_query("SELECT * FROM Wallets WHERE `disabled`='0' AND `Market_Id`!='0'ORDER BY `Acronymn` ASC");
			$n_rows = mysql_num_rows($sqlx);
			for($nn = 0; $nn < $n_rows; $nn++) {
				$coinid = mysql_result($sqlx,$nn,"Id");
				$coinnm = mysql_result($sqlx,$nn,"Name");
				$coinac = mysql_result($sqlx,$nn,"Acronymn");
				$sqls_2 = @mysql_query("SELECT * FROM Trade_History WHERE `Market_Id`='".$coinid."' ORDER BY Timestamp DESC");
				$last_trade = @mysql_result($sqls_2,0,"Price");
				if($last_trade > 9){
					$p2disp = round($last_trade);
				}else{
					$p2disp = sprintf("%2.8f",$last_trade);
				}
				?>
				<li class='box2'><p title="Trade <?php echo $coinnm; ?>" onclick="window.location = 'index.php?page=trade&market=<?php echo $coinid; ?>';"><?php echo $coinac;?> / MLC<br/><?php echo $p2disp; ?></p></li>
				<?php
			}
		?>
		</ul>
	</div>
	<!--main content area-->
		<div id="main_content">
			<hr class="five">
			<hr class="five">
			<center>
			<?php
				switch($_GET['page']) {

					case 'about':

						include('pages/about.php');

						break;

					case 'access_violations':

						include('pages/access_violations.php');

						break;
					case 'account':

						include('pages/account.php');

						break;
					
					case 'account_history':

						include('pages/account_history.php');

						break;
					case 'deposit':

						include('pages/deposit.php');

						break;
					case 'home':

						include('pages/home.php');

						break;
					
					case 'invalid_market':

						include('pages/invalid_market.php');

						break;
					case 'loggedout':

						include('pages/loggedout.php');

						break;
					case 'logout':

						include('pages/logout.php');

						break;
					
					case 'login':

						include('pages/login.php');

						break;
					case 'moderate':

						include('pages/moderate.php');

						break;
					case 'newticket':

						include('pages/newticket.php');

						break;
					case 'preferences':

						include('pages/preferences.php');

						break;
					case 'register':

						include('pages/register.php');

						break;
					case 'reset':

						include('pages/reset.php');

						break;
					case 'site_monitor':

						include('pages/site_monitor.php');

						break;
					
					case 'siteconfig':

						include('pages/siteconfig.php');

						break;
					case 'support':

						include('pages/support.php');

						break;
					case 'tos':

						include('pages/tos.php');

						break;
					case 'trade':

						include('pages/trade.php');

						break;
					case 'trade_hist_all':

						include('pages/trade_hist_all.php');

						break;
					case 'trade_history':

						include('pages/trade_history.php');

						break;
					case 'viewticket':

						include('pages/viewticket.php');

						break;
					case 'withdraw':

						include('pages/withdraw.php');

						break;
					case 'withdrawalhist':

						include('pages/withdrawalhist.php');

						break;
					case 'withdrawalqueue':

						include('pages/withdrawalqueue.php');

						break;
					case 'open_orders_all':
					
						include('pages/open_orders_all.php');
						
						break;
					case 'depositchecker':
						
						include('pages/depcheck.php');
						
						break;
					default:

						include('pages/home.php');

				}
			
?>
		</center>
	</div>
	
	<div id="filler">
		<!--footer-->
		<ul id="footerl" class="nobullets">
			<li><a  href="https://twitter.com/milancoin" target="_blank" title="Follow us on Twitter"><i class="fa fa-twitter"></i> <font class="mhead">Follow Us</font></a></li>
			<li><a  href="https://github.com/milancoin-project/milanex.git" target="_blank" title="View source on Github"><i class="fa fa-github"></i><font class="mhead">View The Source</font></a></li>
		</ul>
		<!--copyright-->
		<div id="centerfooter" class="mhead">
			<center>
			&copy; 2014 MilanEx LLC. All Rights Reserved.
			</center>
		</div>
		<!--chat toggle-->
		<button id="chat_toggle" class="toggle"><img src="assets/img/chat_icon.png" height="26" alt="Chat" title="Chat"></button>
	</div>
	<!--chat box-->
	<div id="chatbox" class="color3">
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
		}else{
			echo'
			<hr class="five" />
			<div id="LoggedOut"><b><center>You must be logged in to chat</center></b></div>
			';
		} 
?>
	</div>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-48607759-2', 'milancoin.com');
  ga('send', 'pageview');

</script>
</body>
</html>
