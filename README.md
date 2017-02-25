# Inquire UK Website

This is the Joomla source for the Inquire UK website. 

## Files to be wary of when upgrading

 When upgrading Joomla, the following files will need to be checked to ensure that custom language overrides haven't been lost:
 
 - administrator/language/en-GB/en-GB.ini
 - language/en-GB/en-GB.com_content.ini
 - language/en-GB/en-GB.com_users.ini
 - language/en-GB/en-GB.ini
 - layouts/joomla/system/message.php
 - media/system/js/core-uncompressed.js

## Local development setup
 
 First, check out the source code from this respository into a local folder.
 
#### Mac OS X

##### Apache config changes

These instructions are based on Mac OS X 10.10 Yosemite.

1. Edit the default location of the built-in apache webserver.

```bash
$ vim /etc/apache2/httpd.conf
```

And enable gthe following modules:

```
 LoadModule deflate_module libexec/apache2/mod_deflate.so
 LoadModule expires_module libexec/apache2/mod_expires.so
 LoadModule php5_module libexec/apache2/libphp5.so
```

Then edit the DocumentRoot to point at the folder where the source is, and enable AllowOverride so that SEO friendly URLs work (this lets the .htaccess rules override the main config):
```
DocumentRoot "/Users/<user>/Sites/inquire"
<Directory "/Users/<user>/Sites/inquire">
AllowOverride All
```

Restart Apache:

```
$ sudo apachectl restart
```

##### MySQL Server

Install MySQL: http://dev.mysql.com/downloads/mysql/

Update your path in .bash_profile to include mysql:

```
export PATH=/usr/local/mysql/bin:$PATH
```

On Mac OS X, MySQL stores it's lock in /tmp, but php expects it to be in /var. Add a symlink:

```
cd /var
sudo mkdir mysql
cd mysql
sudo ln -s /tmp/mysql.sock mysql.sock
```

#### Notes

This page provided the basis for some of these instructions: https://jason.pureconcepts.net/2015/10/install-apache-php-mysql-mac-os-x-el-capitan/