[Back to Index](README.md)

# Users and permissions

## User identifiers and credentials

User names in Numbat must be unique, and match the following regular
expression: `^[0-9A-Za-z.\-_]+$`. This means that a user name can only
include the numerals 0-9, the letters A-Z (upper or lower case),
periods, hyphens and underscores. The `username` field in the `users`
table restricts the length of a user name to 30 characters or fewer.

User email addresses must be unique and match the following regular
expression: `^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$`, and
the `email` field in the `users` table restricts the length of a user
email to 300 characters. There's probably valid email addresses that
this excludes, to be honest, but it was what I came up with in the
moment. If you need me to update the regular expression so that your
email doesn't get rejected, happy to do so, open an Issue and I'll
look at it.

There aren't many restrictions on passwords, because they are stored
as the 64-character digest of the `sha256` hashing function (as
implemented by PHP) applied to the concatenation of a three-character
[salt](https://en.wikipedia.org/wiki/Salt_(cryptography)) and the
provided password.

A user password can be reset in two ways:

1. After a failed sign-in attempt, click the "Forgot your password?"
   link. Numbat will send an email with a password reset link in it.
2. A user with admin privileges can manually generate a link to reset
   any other user's password. This will not show the admin user the
   other user's password; it will only allow the user to change
   it. This option exists in the case that password reset emails are
   not arriving, so that the admin can send the link to the user
   through other means.

## Privileges

There are three levels of privileges a Numbat user can have, "admin,"
"extractor" and "none."

"Admin" privileges correspond to 4 in the database back-end. This is
the default level of privileges for the first user entered in to the
database by the installer script. An admin user can assign
extractions, edit reference sets and forms, grant user privileges, do
extractions, reconcile extractions with other users and export
data. There must always be at least one user with admin privileges.

"Extractor" priveleges correspond to 2 in the database back-end. A
user with this level of privileges can do extractions and reconcile
extractions with other users, but not assign extractions, edit
reference sets and forms, grant user privileges or export data.

"None" privileges correspond to 0 in the database back-end. This is
the default level for users who sign up, so that in the unlikely event
that a URL for an ongoing project's Numbat instance escapes
containment and spammers or pranksters make accounts on it, that they
will not be able to do extractions uninvited or reconcile other users'
finished extractions.
   
Astute readers may wonder why the database back-end encodes these
privilege levels as 0, 2 and 4, skipping over 1 and 3. This is because
I thought at one point that I might have intermediate levels of
privileges between them. I never implemented that. Maybe someday.

## Activating a user account

A user account is not active until the email associated with it has
been verified. Until the email is verified, the account's credentials
will not be accepted by Numbat.

There are two ways to mark an email as verified:

1. Click the link in the email sent to that address on sign-up.
2. A user with admin privileges can manually mark any other account's
   email as being verified via the user administration page (Numbat
   menu > User administration).
   
---

[Previous: Installing and upgrading Numbat](install-and-upgrade.md)

[Next: References and reference sets](references.md)
