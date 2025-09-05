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

[Previous: Introduction](introduction.md)

[Next: Installing and upgrading Numbat](install-and-upgrade.md)
