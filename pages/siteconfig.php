<?php
/**~2014 MilanEx.pw Developers. All Rights Reserved.~*
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
require_once("models/config.php");
if(!isUserAdmin($id) || !isUserLoggedIn())
{
echo '<meta http-equiv="refresh" content="0; URL=access_denied.php">';
die();
}
?>
<h1>Site Configuration</h1>
<hr class="five">
<hr class="five">
<hr class="five">
<style>
table, tr, td {
	padding: 0;
}
table {
	border: 1px solid #000;
}
table tr {
	border-bottom: 1px solid #000;
}
table tr:last-child{
	border: none;
}
table td {
	border-right: 1px solid #000;
}
table td:last-child {
	border: none;
}
</style>
<?php
$siteconfig = mysql_query("SELECT * FROM config");
echo'<table style="width: 50%; table-border: 1px solid #dadada; ">';
while($row = mysql_fetch_assoc($siteconfig)){
	
	if($row['setting'] == 1){
		$status = "<font color='red'>Disabled</font>";
	}else{
		$status = "<font color='green'>Enabled</font>";
	}
echo'

	<tr>
		<td><h2>'.strtoupper($row["name"]).'</h2></td>
		<td><h3>Status: '.$status.'</h3></td>
		<td><a class="blues stdsize" href="index.php?page=siteconfig&s_id='.$row["id"].'" name="s_id" />Change Setting</a></td>
	</tr>
';
}
echo'</table>';


if(isset($_GET["s_id"])) {
	echo'working...';
	$confid = mysql_real_escape_string($_GET["s_id"]);
	$action1 = mysql_query("SELECT * FROM config WHERE `id`='$confid'");
	if(!$action1) {
	
		echo "error : ".mysql_error($action1);
	}
	$setting = mysql_result($action1,0, "setting");
	if($setting == 1) {
		$outputted = 0;
	}else{
		$outputted = 1;
	}
	$action2 = mysql_query("UPDATE `config` SET  `setting` = '$outputted' WHERE `id` = '$confid' ");
	echo '<meta http-equiv="refresh" content="0; URL=index.php?page=siteconfig">';
	
	
}

?>