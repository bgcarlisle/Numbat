[Back to Index](README.md)

# Numbat quick start guide

This guide assumes that you have SSH access to a web server with
Linux, Apache, MySQL and PHP installed. You will also need a MySQL
database available with credentials that allow you full permissions to
read and write. 

Numbat is designed to co-exist with other instances of Numbat on the
same domain or subdomain, however it is not recommended to share a
single MySQL database among multiple Numbat instances.

## Necessary information

Have the following information ready before installation:

* SSH username for your web server
* SSH password
* SSH hostname
* Full path to Numbat folder root
* Public-facing URL for Numbat folder
* MySQL username for an empty MySQL database
* MySQL password
* MySQL database name
* MySQL hostname

## Step 1: Put Numbat software on your server

[Download the latest version of
Numbat](https://github.com/bgcarlisle/Numbat/releases) as a `.zip`
file and expand. Use the file transfer software of your choice
(Filezilla is a good one if you need a suggestion) to transfer the
entire contents of the expanded `.zip` to the root folder on your web
server.

## Step 2: Run Numbat installer

Open your web browser and navigate to the URL of your Numbat
instance. You should see an installer that asks you for your MySQL
credentials.

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

## Step 3: Upload a reference set

Make a table in Modern CSV or using another spreadsheet tool where you
know how to export it as a `.tsv` (tab-separated value)
file. Recommended column names: `title`, `author`, `year`, `journal`.

---

[Previous: Introduction](introduction.md)

[Next: Installing and upgrading Numbat](install-and-upgrade.md)
