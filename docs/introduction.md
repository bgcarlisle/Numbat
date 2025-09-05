[Back to Index](index.md)

# Introduction to Numbat Systematic Review Manager

Numbat is a tool for multi-user collaborative data extraction,
originally intended for use in medical systematic reviews. Numbat was
designed to require as few resources as possible and to be deployable
on the most basic web hosting packages, so it is a web application
written in PHP with a MySQL back-end.

Numbat was named after the endangered animal of the same name, which
uses its long tongue to extract insects to eat.

Numbat was written by [Benjamin Gregory Carlisle
PhD](https://bgcarlisle.com) and is provided and free and open-source
software under AGPL 3.

# Numbat-specific concepts and definitions

A Numbat instance is a folder of Numbat software that has been linked
to a MySQL database back-end that it manages, and provides a web app
as a front-end for user access.

A user is a person known to the Numbat instance by their username,
which grants access to that person to perform actions with Numbat
software based on the level of privileges they have. In some cases the
term "extractor" will be used more-or-less interchangeably, although
there may be cases where a user may not perform any extractions at
all.

A reference set is a table in a Numbat instance's database, in which
every row represents a single reference and columns contain metadata
for each reference. Usually one reference corresponds to one academic
journal article or one clinical trial, however this can be flexible
depending on the needs of the project at hand, as long as extractors
are clear about the task assigned to them.

An extraction form is a set of questions to be posed to an extractor
regarding a single reference all together as a single unit. An
extraction form consists of some metadata regarding the form itself,
and an ordered set of extraction form elements (individual questions)
to be shown to an extractor.

An assignment is a request that an extractor apply a particular
extraction form to a particular reference within a particular
reference set. An extractor can only extract a given reference once
with the same extraction form.

An extraction is a partially completed or fully completed assignment
by a single user. Numbat allows an arbitrary number of users to be
assigned, and to extract any reference with any extraction form.

# Basic structure of a Numbat instance

The instance's `config.php` file, which is written to disk at the
point of installation, is found in the root folder of the Numbat
instance. This file contains the instance's MySQL credentials, the
absolute path to the Numbat instance on the server's file system, and
the full URL to access the Numbat instance. If there is no
`config.php` file present, Numbat will provide a visitor to the
instance with an installer that will write this file, if successful.

The bulk of the data associated with a Numbat instance is contained
within The MySQL database specified in `config.php`. This database
contains all the user data, reference sets, forms, assignments,
extracted data and reconciled final copies. This database is not
designed to be viewed or altered directly, but may be accessed using
the credentials stored in `config.php` in case of troubleshooting.

The contents of the `uploads/files/` directory are arbitrary files
that a user may have uploaded to the server for their own reference,
or the reference of an extractor. This folder is created only in the
case that a user uploads a file via the "Upload files" tool in the
Numbat menu, and may not exist on your Numbat instance.

The remaining directories and files in a Numbat instance are Numbat
software, and should be identical between Numbat instances that are
running the same version of Numbat.

[Next: Quick start guide](quick-start.md)
