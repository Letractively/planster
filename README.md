PLANster is a free software which allows you to coordinate virtually anything
(meetings, events, food taste, ...) with your friends.

## Requirements ##

In order to run PLANster, you'll need to have the ADOdb Database Abstraction
Library for PHP installed on your system. That library can be downlaoded from
http://adodb.sourceforge.net/ (tried with version 4.52).

Also, you'll need some kind of database which is supported by ADOdb (tried with
MySQL 4.1 and 5.0) and of course the corresponding PHP extension.

As for the apache web-server, make sure you have mod\_rewrite loaded,
.htaccess files are parsed and MultiViews are disabled. Otherwise PLANster
will not work.


## Installation ##

When ADOdb is installed, change to PLANster's config directory, rename
site.conf.php-dist to site.conf.php and configure your database connection in
there.

Now, open http://your.planster.url/setup/ in your browser. The script will
check whether the smarty template compile directory is writable, whether ADOdb
is installed and whether your database connection works. If everything is OK,
click "Initialize database" to have the db layout created.

In case your PLANster installation is not running locally on the machine you're
working on, you'll have to add your client's IP address to setup/.htaccess in
order to be allowed to access the setup script.

Now, if you'd like to import the demo-events, there's a "Reset demo events"
link in the setup tool. Just click it and they will be created.

That's it.