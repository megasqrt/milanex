<?php
require_once ('system/csrfmagic/csrf-magic.php');
$id = $loggedInUser->user_id;
$account = $loggedInUser->display_username;
if(!isUserLoggedIn()){
echo '<meta http-equiv="refresh" content="0; URL=access_denied.php">';
}
if(isUserAdmin($id) === true)
{
echo "<h2>Welcome Admin</h2>";
$sql = mysql_query("SELECT * FROM Tickets WHERE `opened`='1'");
}
if(isUserMod($id) === true)
{
echo "<h2>Welcome Moderator</h2>";
$sql = mysql_query("SELECT * FROM Tickets WHERE `opened`='1'");
}
if(isUserNormal($id)){
	echo "<h2>How may I help you today, <b>".$account."</b> ?</h2>";
	echo "
	<ul class='nobullets'>
		<li style='width: 200px !important; lineheight: 35px;' class='blues'><h3><a href='index.php?page=newticket'>Get Support</a></h3></li>
		<li style='width: 200px !important; lineheight: 35px;' class='blues'><h3><a href='index.php?page=fchk'>Missing Deposit</a></h3></li>
	</ul>
	</br>";
	$sql = mysql_query("SELECT * FROM Tickets WHERE `user_id`='$id'");
}

$num = mysql_num_rows($sql);
?>

			<div id="page">
				<form action="">
				<table id="page">
				<tr>
					<th><a id="toggle-all" ></a> </th>
					<th><a href="">Ticket Subject</a>	</th>
					<th><a href="">Posted</a></th>			
					<th><a href="">Answers</a></th>
					<th><a href="">Status</a>
				</tr>
<?php
for($i = 0;$i<$num;$i++)
{
$subject = mysql_result($sql,$i,"subject");
$posted = mysql_result($sql,$i,"posted");
$id = mysql_result($sql,$i,"id");
$answers = GetPosts($id);
$opened = mysql_result($sql,$i,"opened");
if($opened == 1)
{
$open = "<b>Open</b>";
}
else
{
$open = "<b>Closed</b>";
}
?>
				<tr>
					<td><input  type="checkbox"/></td>
					<td><a href="index.php?page=viewticket&id=<?php echo $id; ?>"><?php echo $subject;?></a></td>
					<td><?php echo $posted;?></td>
<td><?php echo $answers;?></td>
<td><?php echo $open; ?></td>
				</tr>
<?php
}
?>

				</table>
				</form>
			</div>
