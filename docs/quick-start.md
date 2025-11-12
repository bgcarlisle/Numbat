[Back to Index](README.md)

# Numbat quick start guide

Numbat is web application, designed to be installed on a web
server. Numbat is not a laptop/desktop/mobile app. An administrator
needs to install Numbat on a web server, but users only need a web
browser to access it.

This guide assumes that you have SSH access to a web server with
Linux, Apache, MySQL and PHP installed. You will also need a MySQL
database available with credentials that allow you full permissions to
read and write.

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

The quickest way to install Numbat is to [download the latest version
of Numbat](https://github.com/bgcarlisle/Numbat/releases) as a `.zip`
file and expand. Use the file transfer software of your choice
(Filezilla is a good one if you need a suggestion) to transfer the
entire contents of the expanded `.zip` to the root folder on your web
server. (Cloning Numbat on to your web server using `git` for version
control is the recommended method, described in the [documentation for installation and upgrading](install-and-upgrade.md).)

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

Upload this file to Numbat by selecting "Manage reference sets" from
the Numbat menu, then "Add new reference set".

## Step 4: Define an extraction form

Choose the Numbat menu, then "Edit extraction forms" and choose the
"Add new extraction form" button. A new empty form will be added to
the list. Click "edit" to the right of the name for the new extraction
form. (You will be prompted to provide optional metadata for your
form, which you can skip if you like, but you will be better-served in
the long run if you provide good documentation for yourself.)

Add elements to your form by clicking the "Add new element" button at
the bottom of the form editor. A detailed description of the available
form elements can be found in the [documentation for forms and
extraction elements](forms.md).

## Step 5: Sign up other extractors

Tell other extractors to visit the URL of your Numbat instance and to
click where it says "New here? Sign up." When they sign up, if Numbat
is configured to send out emails, they will receive an email with a
link prompting them to verify that their email is accurate.

If Numbat is not configured to send out emails, or if the email is not
received for whatever reason, an admin account can manually change the
account so that Numbat takes that email address to be verified anyway.

To do so, a user with admin privileges must choose "User
administration" from the Numbat menu. Then, for every user on the
instance, there is a drop-down menu that allows an admin user to
update that account's email verification status.

Before a user can start doing extractions, that user must have "User"
level privileges. (By default, at signup, a user has "None"
privileges.) User privileges can be assigned by an admin user in the
same way as marking an account's email as being verified.

## Step 6: Assign and do extractions

A user's list of extractions to do is managed via the assignments
page, which can be accessed under "Manage extraction assignments" in
the Numbat menu.

## Step 7: Reconcile finished extractions

## Step 8: Export final data sets for analysis

---

[Previous: Introduction](introduction.md)

[Next: Installing and upgrading Numbat](install-and-upgrade.md)
