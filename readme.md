OpenEx.pw 0.3

THIS IS BETA SOFTWARE. IT IS UNPROVEN. USE AT YOUR OWN RISK.

build percentage=97%
build status= functional
build name= o-slice1.1
release= openex 0.3.6
build date=1/9/2014

technologies used/required:
nginx
php
mysql
memcached
jquery(included) <-- All rights reserved
fontawesome(included) <-- All rights reserved
userCake(Heavily Modified) <--All Rights reserved
mysqli.class.php <--Jesse boyer, all rights reserved(jream.net)


Demo(live trading site)->https://openex.pw

known issues:

chat scroll doesn't always work.
trading fee's sometimes don't refund on cancelled trades.
ajax resources cause high load. you will need an expensive server if you plan on serving many users at all.
site config page isn't functional. you will have to change the settings in the db.
api key generation can cause extreme load on small servers.
session timeout results in chat still showing the text box, but users can't submit a message
withdrawal system is only semi automated. administrator's must confirm them in the withdrawal queue page


other information:

the trade engine, deposits and table optimization scripts are closed source and not included.

install instructions:
**Step 1**
after your webserver is setup and all packages upgraded upload your files to your webservers html or www directory.

-----------------------------
**Step 2**
install phpmyadmin and browse to the url of your phpmyadmin.
login to phpmyadmin and create a new database and import db.sql into that db.

-----------------------------
**Step 3**
create two users in mysql:

1 for chat
1 for app

open models/chat.config.php and enter the chat db users credentials.
open models/settings.php and enter the main applications db credentials.
you will also need to imput your database name and ip as well. the chat.config page mistakenly list db name as $config['table'], so put db name there. save and upload.
----------------------------
**Step 4**

open your web browser and browse  to the registration page. if you see a "cannot connect to database" message, your main db credentials are wrong. likewise, you will see an "access denied" in the chat window if those credentials are bad.


barring any errors, register a username. then browse to the "userCake_Users" table of the database. select edit next to your username. in the groupid table, put 9 for admin or 5 for moderator. the default is 1, which is a normal user.
----------------------------

**Step 5**

Install your coin clients, then input their details into the "Wallets" table of the database.

to test your connection, browse to the "sitemonitor" page of the site. click on "coin stats". if a bunch of information pops up by a coin name, congrats it works.

----------------------------

**Additional information**

if you need to debug, browse to models/config.php and replace the first two lines where it says:

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

with:

error_reporting(E_ALL);
ini_set('display_errors', 1);

now, debug information will be shown on any pages where you have errors upon reloading.



If you found our software useful, please consider donating to the authors at the following btc address:

1337xmR8JRzwKonFgd17dhVo9NHQiP7875










