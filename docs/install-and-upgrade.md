[Back to Index](README.md)

# Installing and upgrading Numbat

## Installing Numbat

The recommended method for installing Numbat is using `git`, because
this provides version control, however Numbat can also be installed
manually by extracting the Numbat software into the directory on the
web server as described in the [Numbat quick start
guide](quick-start.md) The instructions here assume that the user has
a base level of facility with basic Linux command line.

Numbat requires a [LAMP
stack](https://en.wikipedia.org/wiki/LAMP_(software_bundle)) (Linux,
Apache, MySQL, PHP) with the web server providing a public-facing
address.

Connect to your server through the command line via SSH. Your hosting
provider will have your SSH login credentials, which will allow you to
send commands to your server.

Once you have successfully connected to your server, navigate to the
directory into which you want to install Numbat, delete all the files
that are already there, if any, and run the following command:

```
$ git clone https://github.com/bgcarlisle/Numbat.git ./
```

This will copy the Numbat software from the git repo onto your server,
and it will also provide an easy means for upgrading to subsequent
versions of Numbat.

Take note of the full path to the Numbat folder root and the
public-facing URL that corresponds to the Numbat folder.

Before you can open Numbat and run the installer, you must have an
empty MySQL database with read and write permissions ready to connect.

While Numbat is designed to co-exist with other instances of Numbat on
the same domain or subdomain, however it is *not* recommended to share
a single MySQL database among multiple Numbat instances.

When you have set the MySQL database backend up, take note of the
MySQL username, password, database name and hostname, as you will need
to enter this in the installer.

To run the Numbat installer, open a browser and visit the URL that
corresponds to the folder into which you cloned the Numbat git repo.

The installer will ask you to enter the following:

* Full path to Numbat folder root
* Public-facing URL for Numbat folder
* MySQL username for an empty MySQL database
* MySQL password
* MySQL database name
* MySQL hostname

Click the "Test database connexion" button when you have entered
these. If Numbat is able to connect to your database, you'll get a
green box confirming this. If you get a red box, you'll have to
trouble-shoot your database connexion.

The absolute path and site URL fields should be detected and filled in
by Numbat automatically. Look them over to see if they look wildly
wrong or are left blank or something.

The Numbat project name field is a short amount of text that will help
you and other extractors distinguish this Numbat instance from others
they may use. Make it descriptive and specific.

Numbat will take the admin credentials provided and generate its first
user based on them. This user will have admin privileges.

Upon successful completion, Numbat will write a file named
`config.php` to the installation folder. This file contains the MySQL
credentials, the path on disk to Numbat and the public URL for Numbat.

## Upgrading Numbat

To upgrade Numbat, you must (1) replace all the Numbat software files
that have changed between when it was installed and the newest version
and (2) run the PHP script named `db-migrations.php`.

If you used `git` to install Numbat in the first place, replacing all
the software files that have changed is a simple matter of pulling the
most recent changes from the Numbat `git` repo. First, SSH into your
server, then navigate to the Numbat root folder. From there, pull the
most recent version of Numbat using the following command:

```
$ git pull
```

---

[Previous: Quick start guide](quick-start.md)

[Next: Users and permissions](users.md)
