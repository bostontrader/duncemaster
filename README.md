# Welcome to DunceMaster!

The purpose of this app is to provide some basic management of a school.  A school has various objects such 
as Teachers, Students, Semesters, Sections, Classes and more. How do these things fit together? Can we organize
these things in some sensible manner or are we perpetually doomed to flounder in random chaos?  That's what we 
try to figure out here.

Some parts of this app are more generally useful for all schools than other parts which are more
specific to the one particular school that this app was built for.  It would have been nice to abstract
out the general bits, more fully, from the specific bit.  Unfortunately, that was not very high on the
priority list, but we did the best we could, given the constraints.

## Installation

This app was developed using CakePHP 3.* served by LEMP on Ubuntu.  The first step in installation would 
be to replicate our server installation.  That's fairly easily done by examining http://github.com/bostontrader/lemp.

Although that serves as the foundation, there is a minor customization required, in order to 
accommodate this app.  More specifically:

When we build PHP we need to add the following configuration options:

--with-pdo-mysql=$STACK_ROOT/mysql
--enable-intl

These options are required by CakePHP.

--with-openssl

Needed by composer. Which means you'll have to have OpenSSL installed on your server.

--enable-mbstring

Needed by CakePHP

--with-curl

Recall that STACK_ROOT is defined by the LEMP stack installation.

cd $STACK_ROOT/html

git clone https://github.com/bostontrader/duncemaster.git

Setup the proper nginx config file.

Install composer.

$STACK_ROOT/php/bin/php -r "readfile('https://getcomposer.org/installer');" > composer-setup.php
$STACK_ROOT/php/bin/php -r "if (hash('SHA384', file_get_contents('composer-setup.php')) === 'fd26ce67e3b237fffd5e5544b45b0d92c41a4afe3e3f778e942e43ce6be197b9cdc7c251dcde6e2a52297ea269370680') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); }"
$STACK_ROOT/php/bin/php composer-setup.php
php -r "unlink('composer-setup.php');"




$STACK_ROOT/php/bin/php composer.phar update
Update composer.

Configure config/app.php. The existing edition is set for a very low-security testing
environment.  You'll need to tighten screws and adapt for production use.
  
./configure --prefix=/home/wukong/lemp/php --enable-fpm --with-mysql=/home/wukong/lemp/mysql --with-pdo-mysql=/home/wukong/lemp/mysql --enable-intl --with-openssl --enable-mbstring


XDEBUG_SESSION_START

sudo $STACK_ROOT/mysql/bin/mysqld_safe --user=batman --log-error=$STACK_ROOT/mysql.err --datadir=$STACK_ROOT/mysql/data --port=3307 &
$STACK_ROOT/php/sbin/php-fpm
$STACK_ROOT/nginx/sbin/nginx &
$STACK_ROOT/nginx/sbin/nginx -s reload


