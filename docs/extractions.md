[Back to Index](README.md)

# Extractions

A user can find their assigned extractions by clicking the Numbat menu
and choosing "Do extractions." This will bring up a page separated
into the two types of extractions possible: screening and regular
extractions. I should come up with a better term, I know.

If there is screening assigned, there will be a heading that says "Do
screening" with a table that shows which reference set and form have
been assigned to you. Click the "Screen" link at the right side of
this table to begin screening.

If there are regular extractions assigned, these will be under a
heading that says "Do extractions." There will be one table per
reference set under this heading, with one row on each per extraction
assigned. Columns in the table will indicate which reference is
assigned, with which form and the status of the extraction.

In the database backend, extractions exist as rows in a table named
`extractions_n`, where `n` is a whole number corresponding to the
order in which extraction forms for the Numbat instance in question
were created, increasing and starting with 1. An extraction row is
created whenever a user visits the link to the extraction. The link
will have the form: `extract/?action=extract&form=1&refset=2&ref=3`,
where `form=1` specifies the form number (in this case, 1), `refset=2`
specifies the reference set number (in this case, 2), and `ref=3`
specifies the reference number for the extraction in question. There
is nothing stopping a user from opening and completing an extraction
of a reference that they haven't been assigned, and removing an
assignment does not delete a started or finished
extraction. Assignments and extractions are independent in this sense.

When an extraction is started, it is initialized with status "Not yet
started" and the current timestamp. A user can update the status of
the extraction at any time from the "Status of extraction" selector at
the top of every extraction. The first time that the extraction is
changed to "Completed," the extraction is also updated with a
timestamp for when the extraction was finished. If you need better
control over extraction timing, use the "Extraction timer" form
element, which allows a user to "Restart timer" (set the "started"
timestamp to the current time) and "Clear finished time", which allows
a user to set the completion time by clicking "Completed" again.


[Previous: Assignments](assignments.md)

[Next: Reconciliation](reconciliation.md)
