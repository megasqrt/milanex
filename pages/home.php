<?php
/**~2014 MilanCoin Developers. All Rights Reserved.~*
 *               http://www.milancoin.org/milanex/
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
require_once ('system/csrfmagic/csrf-magic.php');
if(!isUserLoggedIn()){

}
?>
<div class="container">
	<div class="innertube">
			<?php
			$getmarkets = mysql_query("SELECT * FROM `Wallets` WHERE `disabled`='0' AND `Id`!='1' ORDER BY `Acronymn` ASC");
					
			echo '<div id="page" >
				<table id="page" style="width: 90%;">
				<hr class="five">
				<h1>Market Overview</h1>
				<hr class="five">
				<tr class="blue">
					<td style="width: 20%;">Market</td>
					<td style="width: 20%; text-align: right; margin-right: 10px;">Current Ask</td>
					<td style="width: 20%; text-align: right; margin-right: 10px;">Current Bid</td>
					<td style="width: 20%; text-align: right; margin-right: 10px;">Last Price</td>
					<td style="width: 20%; text-align: right; margin-right: 10px;">Volume(MLC)</td>
			';
			$rows = mysql_num_rows($getmarkets);
			for($i = 1;$i < $rows;$i++) {
				if ( $i & 1 ) {
					$color = "lightgray";
				} else {
					$color = "darkgray";
				}
				$market_id = mysql_result($getmarkets,$i,"Id");
				$name = mysql_result($getmarkets,$i,"Name");
				$acro = mysql_result($getmarkets,$i,"Acronymn");
				$sqltrades1 = @mysql_query("SELECT * FROM `Trade_History` WHERE `Market_Id`='$market_id' ORDER BY Timestamp DESC");
				$last_trade = @mysql_result($sqltrades1,0,"Price");
				$lastprice = sprintf("%2.8f",$last_trade);
				$sqltrades2 = @mysql_query("SELECT * FROM `trades` WHERE `From`='$acro' ORDER BY `Value` ASC");
				$curask = @mysql_result($sqltrades2, 0, "Value");
				if($curask > 9){ $curask1 = round($curask); }else{ $curask1 = sprintf("%2.8f",$curask); }
				$sqltrades3 = @mysql_query("SELECT * FROM `trades` WHERE `To`='$acro' ORDER BY `Value` DESC");
				$curoffer = @mysql_result($sqltrades3, 0, "Value");
				if($curoffer > 9){
					$curoffer1 = round($curoffer);
				}else{
					$curoffer1 = sprintf("%2.8f",$curoffer);
				}
				$timez = time() - (60*60*24);
				$sql24hour = mysql_query("SELECT * FROM `Trade_History` WHERE `Timestamp` >= '$timez' AND `Market_Id`='$market_id'");
				$vol = array();
				while($row = mysql_fetch_assoc($sql24hour)) {
					$vol[] = $row["Quantity"] * $row["Price"];
				}
				$volume = sprintf("%0.8f",array_sum($vol));
				echo'
					<tr id="'.$market_id.'" style="cursor: pointer;" title="'.$name.' Market" class="'.$color.'">
						<td style="width: 20%;">'.$acro.' / MLC</td>
						<td style="width: 20%; text-align: right; margin-right: 10px;">'.$curask1.' MLC</td>
						<td style="width: 20%; text-align: right; margin-right: 10px;">'.$curoffer1.' MLC</td>
						<td style="width: 20%; text-align: right; margin-right: 10px;">'.$lastprice.' MLC</td>
						<td style="width: 20%; text-align: right; margin-right: 10px;">'.$volume.' MLC</td>
					</tr>
					';
				?>
				<script type="text/javascript">
				$('#<?php echo $market_id; ?>').click(function() {
					window.location = 'index.php?page=trade&market=<?php echo $market_id; ?>';
				});
				</script>
				<?php
			}
			echo'</table></div>';		
		?>
	</div>
</div>
