<?php
if(isUserLoggedIn){
$loggedInUser->userLogOut();
$getactive = mysql_query("SELECT `users_logged_in` FROM `usersactive` WHERE `id`='1'");
$count = mysql_result($getactive, 0, 'users_logged_in');
$new = $count - 1;
$time = gettime();
$logout = mysql_query("UPDATE usersactive SET `users_logged_in`='$new' WHERE `id`=1 ");
$update = mysql_query("UPDATE usersactive SET `last_update`='$time' WHERE `id`=1 ");
sleep(1);
?>

<meta http-equiv="refresh" content="0; URL=index.php?page=loggedout">

<?php }else{ ?>

<meta http-equiv="refresh" content="0; URL=index.php?page=home">

<?php } ?>