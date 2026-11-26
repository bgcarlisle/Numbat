[Back to Index](README.md)

# Installing and upgrading Numbat

## Installing a LAMP server for Numbat via Docker

You can skip this step if you are using a hosting provider like
Dreamhost or similar.

Create a file named `Dockerfile` in the root directory of your
project with the following contents:

```
FROM php:8.5-apache

# Install required PHP extensions
RUN docker-php-ext-install pdo pdo_mysql
```

Create a file named `timezone.ini` in the same directory with the
following contents:

```
date.timezone = America/Toronto
```

(Of course, you can update the time zone to whatever is appropriate
for your location. Just make sure to also change it in the file
below.)

Create a file named `docker-compose.yml` in the same directory with
the following contents:

```
services:
  # Numbat Web App (Apache & PHP)
  numbat-web:
    build:
      context: .
      dockerfile: Dockerfile
    container_name: numbat-web
    user: "1000:1000" # For if you want to do dev work
    restart: unless-stopped
    ports:
      - "8080:80" # Remove this if using a reverse proxy
    environment:
      - TZ=America/Toronto
    volumes:
      - ./www:/var/www/html
      - ./timezone.ini:/usr/local/etc/php/conf.d/timezone.ini
    networks:
      - numbat-network
    depends_on:
      - numbat-db

  # MySQL Database
  numbat-db:
    image: mysql:8.0
    container_name: numbat-db
    restart: unless-stopped
    environment:
      - TZ=America/Toronto
      - MYSQL_ROOT_PASSWORD=change_me_root_password
      - MYSQL_DATABASE=numbat
      - MYSQL_USER=numbat_user
      - MYSQL_PASSWORD=change_me_db_password
    volumes:
      - numbat_db_data:/var/lib/mysql
    networks:
      - numbat-network

  # phpMyAdmin (Optional - Database Management)
  numbat-phpmyadmin:
    image: phpmyadmin:latest
    container_name: numbat-phpmyadmin
    restart: unless-stopped
    ports:
      - "8081:80" # Remove this if using a reverse proxy
    environment:
      - TZ=America/Toronto
      - PMA_HOST=numbat-db
      - UPLOAD_LIMIT=128M
      # Uncomment the lines below if running behind an HTTPS reverse proxy:
      # - PMA_ABSOLUTE_URI=https://yourdomain.com/phpmyadmin/
      # - SECURE_PROXY=1
      # - PMA_HTTPS=true
    volumes:
      - ./timezone.ini:/usr/local/etc/php/conf.d/timezone.ini
    networks:
      - numbat-network
    depends_on:
      - numbat-db

networks:
  numbat-network:
    driver: bridge

volumes:
  numbat_db_data: {}
```

Make sure to update the passwords. **For the love of god, change
passwords.**

This will expose port 8080 for Numbat and 8081 for phpMyAdmin (in case
you need to work on the backend). If you are using a reverse proxy,
you can remove the "ports" section and attach the containers to your
proxy's network instead.

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
address. You can install that via Docker using the step above if you
are self-hosting, or you can get the basic shared Linux hosting from a
service provider like Dreamhost or similar.

Connect to your server through the command line via SSH. (Your hosting
provider will have your SSH login credentials, which will allow you to
send commands to your server, or if you set up a homeserver, use those
credentials.)

Once you have successfully connected to your server, navigate to the
directory into which you want to install Numbat (if you used the
Docker setup above, make a new folder in the `www` folder ... or you
can use the `www` folder in the root of your LAMP stack project, I'm
not your dad, you do you, but you might need to `chown` the folder to
your current user to do so), delete or move all the files that are
already there, if any, and run the following command:

```
$ git clone https://github.com/bgcarlisle/Numbat.git ./
```

This will copy the Numbat software from the `git` repo onto your
server, and it will also provide an easy means for upgrading to
subsequent versions of Numbat.

Take note of the full path to the Numbat folder root and the
public-facing URL that corresponds to the Numbat folder.

Before you can open Numbat and run the installer, you must have an
empty MySQL database with read and write permissions ready to
connect. Either create one using your hosting service's tools, or if
you are self-hosting using Docker, log in using the root MySQL
credentials you used in `docker-compose.yml` and create a new user
there specific to your Numbat instance.

Numbat *is* designed to co-exist with other instances of Numbat on the
same domain or subdomain (you could have one Numbat at
`yourdomain.com/project1` and another at `yourdomain.com/project2`),
however it is *not* recommended to share a single MySQL database among
multiple Numbat instances.

When you have set the MySQL database backend up, take note of the
MySQL username, password, database name and hostname, as you will need
to enter this in the installer.

To run the Numbat installer, open a browser and visit the URL that
corresponds to the folder into which you cloned the Numbat `git` repo.

The installer will ask you to enter the following:

(In brackets are the values to enter if you used the Docker setup
above)

* Full path to Numbat folder root (`/var/www/html/`)
* Public-facing URL for Numbat folder (`http://localhost:8080/`)
* MySQL username for an empty MySQL database (`numbat_user`)
* MySQL password (`change_me_db_password`, seriously change this)
* MySQL database name (`numbat`)
* MySQL hostname (`numbat-db`)

Click the "Test database connexion" button when you have entered
these. If Numbat is able to connect to your database, you'll get a
green box confirming this. If you get a red box, you'll have to
trouble-shoot your database connexion.

The absolute path and site URL fields should be detected and filled in
by Numbat automatically. Look them over to see if they look wildly
wrong or are left blank or something.

The installer will also ask you whether you want to enable outgoing
email. This allows a user to reset their own password in case they
forget it. Even if email is disabled, any admin user can generate a
password reset link for any user and send it to them through their own
means, but this is convenient to allow users to do it themselves, and
to provide an extra layer of backup in the case the admins also forget
their passwords.

To enable outgoing email, you will need an email address with SMTP
access that authenticates via STARTTLS on port 587. You will need to
enter:

* The outgoing email "from" address
* SMTP host (will probably look like "smtp.emailprovider.com")
* SMTP user name (in many cases, this is just the email address again)
* SMTP password (many email providers allow you to create app-specific
  passwords for your email accout; this is recommended)
  
Click "Test email settings" and it will try sending an email to the
email provided using the credentials provided. For troubleshooting
purposes, the email includes the SMTP host and user name, the time it
was sent, but (for security reasons) not the password used.

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
and (2) run the PHP script named `db-migrations.php` as an admin user.

If you used `git` to install Numbat in the first place, replacing all
the software files that have changed is a simple matter of pulling the
most recent changes from the Numbat `git` repo. First, SSH into your
server, then navigate to the Numbat root folder. From there, pull the
most recent version of Numbat using the following command:

```
$ git pull
```

If you copied the files over manually and unzipped them, I recommend
deleting all the files in your Numbat root folder except `config.php`,
the contents of `./uploads/files/`, the `./backup/dumpfiles/` folder
and the `./attach/files/` folder, and then unzipping the latest
version of Numbat and copying the new files over manually.

Once the software files have been replaced, there is a script that
detects changes that need to be made to your MySQL database
configuration and applies them. This PHP script can be called by
opening a web browser, visiting your Numbat instance, logging in as an
admin user, and then typing your public-facing URL into the browser
window with `db-migrations.php` at the end.

E.g. If your Numbat instance was installed at
https://domain-name.muffin/project-name/ then you should visit: https://domain-name.muffin/project-name/db-migrations.php

---

[Previous: Quick start guide](quick-start.md)

[Next: Users and permissions](users.md)
