[Back to Index](README.md)

# Assignments

Assignments are represented as a row in the `assignments` table in the
database back-end for your Numbat instance. Each row represents a link
between an assigner (a user on your Numbat instance), an extractor (a
user on your Numbat instance, possibly the same one), the form to be
used in this extraction, the reference set and individual reference
within that reference set to extract.

Assignments are added via the page found by clicking "Manage
extraction assignments" from the Numbat menu. From this page, you will
see a table containing all the reference sets in your Numbat instance,
each with a "View" link.

The "View" link leads to a page with a table containing one row per
reference in the reference set in question, and a column for every
form in the Numbat instance. Each cell contains all the names of users
on your Numbat instance with a "✓" or an "✗" beside them, indicating
which references in the reference set in question have been
assigned to which users to extract with which forms.

When an extractor is given an assignment, this appears on the page
found by choosing "Do extractions" from the Numbat menu.
Screening-type extraction forms are presented on a per-reference set
basis, allowing extractors to click to open a grid showing all the
assigned references to screen. Normal extraction forms are presented
on a per-reference basis, allowing extractors to click to open
individual references to be extracted.

---

[Previous: Forms and extraction elements](forms.md)

[Next: Extractions](extractions.md)
