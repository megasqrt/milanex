<?php

/**~2014 OpenEx.pw Developers. All Rights Reserved.~*
 *https://openex.pw/
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
 
require_once('models/config.php');
$id2 = addslashes(strip_tags($loggedInUser->user_id));
$username = addslashes(strip_tags($loggedInUser->display_username));
$do = addslashes(strip_tags($_GET['do']));
$m_id = addslashes(strip_tags($_GET['market']));
if($do === 'sellorders') {



}
elseif($do === 'buyorders') {



}
elseif($do === 'openorders') {



}
elseif($do === 'getbal') {
 
	$getbal = mysql_query("SELECT `Amount`  FROM balances WHERE User_ID='$id2' AND `Wallet_ID` = '$m_id'");
	$curbal = mysql_result($getbal, 0, "Amount");
	if($curbal == null){
		$curbal = "0.00000000";
	}
	echo $curbal;
}
elseif($do === 'getbtc') {

	$getbtc = mysql_query("SELECT `Amount`  FROM balances WHERE User_ID='$id2' AND `Wallet_ID` = '1'");
	$curbtc = mysql_result($getbtc, 0, "Amount");
	if($curbtc == null){
		$curbtc = "0.00000000";
	}
	echo $curbtc;

}else{
	die("invalid operation.");
}
