<?php
/**~2014 MilanEx.pw Developers. All Rights Reserved.~*
 *               https://www.milancoin.org/milanex/
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

$account = mysql_real_escape_string(strip_tags($loggedInUser->display_username));
$uagent = mysql_real_escape_string(getuseragent()); //get user agent
$ip = mysql_real_escape_string(getIP()); //get user ip
if(isUserLoggedIn()) {
	if ($account != null) {
		$account = mysql_real_escape_string($loggedInUser->display_username);
	}
	else {
		$account = mysql_real_escape_string("Guest/Not Logged In");
	}
}
$date = mysql_real_escape_string(gettime());
$sql = @mysql_query("INSERT INTO access_violations (username, ip, user_agent, time) VALUES ('$account', '$ip', '$uagent', '$date');");
$getcountip = mysql_query("SELECT ip,COUNT(*) as count FROM access_violations GROUP BY ip ORDER BY count DESC;");
while($row = mysql_fetch_assoc($getcountip)) {
	if($row['count'] > 10) {
		$factors = $row['ip'];
		$sql2 = mysql_query("SELECT ip FROM bantables_ip WHERE ip = '$factors';");
		$number_of_rows = mysql_num_rows($sql2);
		
		if ($number_of_rows > 0) {	
		}else {
			$date2 = mysql_real_escape_string(gettime());
			$ip_address = mysql_real_escape_string($row['ip']);
			$sqlxz = mysql_query("INSERT INTO bantables_ip (ip, date) VALUES ( '$ip_address', '$date2');");	
		}
	}
}
echo "<style>html { width:100%; height:100%; background:url(assets/img/access_denied.gif) center center no-repeat; background-color: #00000 !important;}</style>";
echo '<link rel="icon" type="image/x-icon" href="assets/img/the_eye.ico" />';
?>