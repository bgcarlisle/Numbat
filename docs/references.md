[Back to Index](README.md)

# References and reference sets

A reference refers to a single unit of extraction. Numbat was designed
for use in the context of medical systematic reviews, so it was
originally meant for a single reference to correspond to a single
academic journal article, but a set of references could refer to
anything that the extractor wishes to apply an extraction form to,
e.g. clinical trial registry entries, clinical practice guidelines,
investigator brochures, etc.

Every reference corresponds to a row in a table called a reference
set. Reference set tables are named `referenceset_1`,
`referenceset_2`, etc. in the database back-end, with Numbat
automatically assigning a reference set ID as an incrementing integer
value. Reference set ID's cannot be changed after they are
assigned. Except for the first column in a reference set, which is
always named `id` in the database back-end, columns in a reference set
are defined by the user when the reference set is uploaded. The
reference `id` column is also automatically incremented starting
at 1. Reference set column names must be unique.

Reference sets have a user-definable name as well as 5 pieces of
metadata defined by columns in the reference set table: title,
authors, year, journal, abstract. Numbat will try to guess based on
column names which columns correspond to which metadata, but these can
also be set manually by going to "Manage reference sets" in the Numbat
menu and clicking "View" on the row of the reference set in
question. These metadata tell Numbat which columns to use to populate
the fields in question when displaying identifiers for the references
in the assignment editor or list of assigned or completed extractions
to be done or reconciled.

## Uploading a new reference set

To make a new reference set, you must prepare a tab-separated value
`.tsv` file. The first line must contain the column names for your
reference data, separated by tabs. Subsequent lines must contain one
reference per line, with data corresponding to the column names, also
separated by tabs.

Microsoft Excel technically can save a spreadsheet as a `.tsv`,
however I recommend that you uninstall Microsoft Office from your
computer and never open Excel ever again if this is at all possible
for you.

LibreOffice Calc can also save a spreadsheet as a `.tsv`. Choose
"File", then "Save As...", then from the file format drop-down menu
choose "Text CSV (.csv)" and click Save. This will open another dialog
entitled "Export Text File", from which you could choose `{Tab}` as
the Field delimiter. It's a little unwieldy, but it works.

The best software I have found for preparing `.tsv` files is called
Modern CSV, which saves delimited file data natively.

Choose "Manage reference sets" from the Numbat menu, and click the
"Add new reference set" button. You will be prompted to select a file
on your local computer (the `.tsv` file that you prepared
earlier). Choose this file and click "Upload new reference set".

The next page will tell you how many rows Numbat was able to read from
the provided file (not including the first row, which contains column
names), and the number of rows that Numbat was able to read. Check
this against the file you just uploaded to be sure that the provided
`.tsv` was formatted properly. Enter a name for your reference set
(this can be changed later if necessary). I recommend making it
descriptive enough that you can distinguish it from other sets of
extractions within the same project, and including a date in the name.

Numbat will try to automatically choose the correct columns for
reference metadata (title, author, etc.) based on the column names
(exact matches will be picked up along with a few other cases). In the
case that Numbat can't guess, you will be prompted to choose a
column. You may leave any of these blank, however an extractor will
not be able to figure out what reference they are being asked to
extract if you do not

## Adding more references to an existing reference set



[Previous: Users and permissions](users.md)

[Next: Forms and extraction elements](forms.md)
