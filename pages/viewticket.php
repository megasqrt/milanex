<?phprequire_once ('system/csrfmagic/csrf-magic.php');if(!isUserLoggedIn()) {	echo '<meta http-equiv="refresh" content="0; URL=access_denied.php">';	die(); }
$id    = @mysql_real_escape_string($_GET["id"]);
$sql   = @mysql_query("SELECT * FROM Tickets WHERE `id`='$id'");
$owner = @mysql_result($sql, 0, "user_id");
if ($loggedInUser->user_id == $owner OR isUserAdmin($loggedInUser->user_id) OR isUserMod($loggedInUser->user_id)) {
    if (isset($_GET["action"])) {
        if ($_GET["action"] == "closev") {
            echo "<h3>Are you sure?</h3><br \><a href=\"index.php?page=viewticket&action=closey&id=" . $id . "\"><input type=\"submit\" class=\"blues\" value=\"Yes\"/></a><br /><a href=\"index.php?page=viewticket&id=$id\"><input type=\"submit\" class=\"blues\" value=\"No\"/></a><br />";
        }
        if ($_GET["action"] == "closey") {
            mysql_query("UPDATE Tickets SET opened=0 WHERE `id`='$id'");
            echo "Your ticket has been closed.";			echo '<meta http-equiv="refresh" content="0; URL=index.php?page=support">';
        }
if ($_GET["action"] == "open")
{
            mysql_query("UPDATE Tickets SET opened=1 WHERE `id`='$id'");
            echo "Your ticket has been reopened.";			echo '<meta http-equiv="refresh" content="0; URL=index.php?page=support">';
}
    } else {
        $subject = mysql_result($sql, 0, "subject");
        if (isset($_POST["post"])) {
            $post   = mysql_real_escape_string(strip_tags($_POST["post"]));
            $uid    = $loggedInUser->user_id;
            $posted = date("F j, Y, g:i a");
            @mysql_query("INSERT INTO `TicketReplies` (`ticket_id` ,`user_id` ,`body` ,`posted`) VALUES ('$id', '$uid', '$post', '$posted');");
        }
        $subject = mysql_result($sql, 0, "subject");
        $post    = mysql_result($sql, 0, "body");
        $posted  = mysql_result($sql, 0, "posted");
        $opened  = mysql_result($sql, 0, "opened");
if ($opened == 0) { ?><h2>THIS TICKET IS CLOSED</h2>
<a href="index.php?page=viewticket&id=<?php echo $id;?>&action=open"><input type="button" class="blues" value="Open" /></a>
<?php } else { ?><h2>THIS TICKET IS OPEN</h2>
<a href="index.php?page=viewticket&id=<?php echo $id;?>&action=closev"><input type="button" class="blues"value="Close" /></a>
<?php  } ?>
<div id="support-thread"><pre><h3><b>Subject:</b> <?php echo $subject;?></h3><form  action=""><div class="balloon left"><div class="innertube">
<b>Started By:</b><?php echo GetUser($owner);?> <b>On:</b> <?php echo $posted;?></br><b>Message:</b><div class="mbody"><?php echo nl2br($post);?></div></div></div>
</form>
<?php
$replies = @mysql_query("SELECT * FROM TicketReplies WHERE `ticket_id`='$id' ORDER BY `id` ASC");
$num2    = @mysql_num_rows($replies);
for ($i = 0; $i < $num2; $i++) {
$post   = mysql_result($replies, $i, "body");
$owner  = mysql_result($replies, $i, "user_id");
$posted = mysql_result($replies, $i, "posted");
?><form action="">
<div class="balloon right"><div class="innertube"><b>Reply From:</b><?php echo GetUser($owner);?> <b>On:</b> <?php echo $posted;?></br><b>Message:</b><div class="mbody"><?php echo nl2br($post);?></div></div></div>
</form>
<?php } ?><form action="index.php?page=viewticket&id=<?php echo $id; ?>" method="POST"><h3>Reply</h3><div class="balloon bottom"><div class="innertube"><center>
<textarea name="post" class="shadowfield message-reply"></textarea>
<input type="submit" class="blues" /></center></div></div></form></pre></div>
<?php
}
} else {
echo "This is not a valid ticket.";
}
?> 
