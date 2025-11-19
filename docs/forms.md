[Back to Index](README.md)

# Forms and extraction elements

An extraction form in Numbat is a set of questions defined by a user
with admin privileges to be applied to a single reference all together
as a single unit.

Of course, multiple users can extract use the same extraction form for
the same reference, and a user can apply the same form to multiple
references, but an extraction form can only be applied once per user
per reference.

E.g. imagine a Numbat instance for a systematic review project called
"Inaprovaline vs cordrazine for emergency resuscitation", in which
there is a reference set of clinical trial journal articles that was
obtained via Medline and another obtained via Embase. This project has
2 users who will be extracting data from trial reports. An extraction
form called "Title and abstract level screening" could be applied to
every reference in the two reference sets by both of the 2 extractors,
and then subsequently, an extraction form called "Full text data
extraction" could be applied to every reference that is marked as
"Include" in the title and abstract level screening.

## Structure of a Numbat extraction form in the database back-end

An extraction form is an entry in the `forms` table in the database
back-end that includes a unique and unchangeable numeric identifier
(its form ID), the type of form (`screening` or `extraction`) and
several pieces of metadata, including the form's name (up to 500
characters), description (up to 1000 characters), version number,
author(s) (up to 2500 characters), affiliation(s) (up to 2500
characters), the project that this form is associated with (up to 2500
characters), protocol details (up to 2500 characters) and a date to
associate with the form for reference.

Elements (individual questions or other non-question user-facing parts
of an extraction form) are rows in the `formelements` table, which
contains all the elements for the all forms in a Numbat instance,
indexed by their form ID.

The responses to an extraction form are stored in a table in the
database back-end named `extractions_1`, `extractions_2`, etc. where
the numeral at the end of the name corresponds to the form ID.

There are two types of forms in Numbat: "screening" and
"extraction". (Apologies for the ambiguity in the terminology
"extraction form" that now arises. Maybe later I'll change the name.)
The type of form is indicated in brackets after the date on the forms
page, or above the name of the form in bold when opening the editor
for an individual form. A form of one type cannot be converted into a
form of the other type.

## Screening forms

A "screening" form is much simpler than the "extraction" form. In this
case, an extractor presented with a "screening" type form will be
presented with a grid containing one row per reference that has been
assigned, and they will answer two questions: 1. Include or exclude
the reference in question?, and 2. If it is to be excluded, choose one
of the reasons why. The admin for the form can define an arbitrary
number of reasons for exclusion for the extractor to choose one from.

The extractions table for a "screening" form is also very simple. It
contains columns for a unique extraction ID, timestamp, and the ID's
for the reference, reference set and the user doing the
extraction. The `status` column is always 2, indicating that it is
"completed" (in an "extraction" type form, it is possible for an
extraction's `status` to have a different value, described
below). Finally, each row contains an `include` column, whose values
are either 0 (exclude) or 1 (include), an `exclusion_reason` column,
which can contain up to 200 characters of text, and `extractor_notes`,
which can contain an arbitrary length of text.

## Extraction forms

An "extraction" form is much more flexible than the "screening"
form. In this case, an extractor is presented with a page of questions
for each reference to be extracted, rather than a grid containing all
assigned references. There are no mandatory questions in an
"extraction" form like in a "screening" form.

### Deprecated element types

* Citations as an extraction element have been deprecated, as the
advent of openly available citation data from academic services such
as OpenAlex and other proprietary databases makes collecting this data
manually unnecessary.
* Numbat used to distinguish between "table data" elements
(`table_data` in the database back-end), which allowed only 200
characters per cell and "large table data" elements (`ltable_data` in
the database back-end), which allowed an arbitrary number of
characters per cell. The `ltable_data` element has been deprecated,
and now `table_data` elements allow an arbitrary number of characters
per cell.

### Conditional display logic

By default, all elements in an extraction form are visible, however it
is possible to hide an element and have it appear depending on
responses to other elements of the same form.

To use conditional display logic, go to "When the form is first
opened, this item should be" at the bottom of the form element, and
change it from "Visible" to "Hidden." This will enable the conditional
display logic editor. You can add zero, one or many conditions under
which the element in question will appear. If there is more than one,
these conditions can be joined with a conjunction or a disjunction by
choosing "all" or "any" from the drop-down menu in the sentence "Show
this element when any/all of the following conditions are met."

Add a condition by clicking the "Add condition" button. This will
provide an editor with a drop-down selector for the "trigger" element,
a drop-down selector for how to evaluate the trigger element (matching
a particular response, matching anything but a particular response,
having a response, and having no response), and in the case that
matching is required, a drop-down for selecting the option within that
element to be matched. Conditions can be removed by clicking "Remove"
and "Confirm remove" at the right.

By default, when an element has a response, but then it is hidden, the
response in the now-hidden element is cleared. This behaviour can be
changed by selecting "Preserve response" from the drop-down in the
sentence that says, "In the case that this element is hidden by a
conditional display event after a response has been entered: Clear
response".

### Codebook

Every element in an extraction form contains a "codebook" area. This
allows a user to clarify how the question posed by the extraction form
element is intended to be answered. Text entered here will be
interpreted as HTML, to allow for links or special formatting.

## Extraction form element types

### Section headings

A section heading is an element in an extraction form, but it is not a
column in the exported database, as it is added for purely cosmetic
reasons. It is used to group parts of a form together to make it more
interpretable to extractors.

Note that conditional display logic applied to a section heading also
applies to all the elements that follow it until the next section
heading element.

### Open text

An open text element allows an extractor to enter arbitrary text,
which is stored in the database back-end in a MySQL `TEXT` column
(maximum size 64 kB). The user defines the name of the column in the
database back-end using the "Column name" field in the form
editor. This column name is also the one used at the time of data
export.

The "Display name" field allows the user to define the name for the
open text field that will be displayed at the time of data
extraction. This name is not included at the time of data export.

This field allows for optional regex (regular expression)
validation. If you enter a regex into the field here, an extractor's
entry will not be saved unless it matches the regex specified. For
example, if you want to make sure that an extractor only enters a
whole-number Arabic numeral, use the regex `[0-9]+`.  Leave this field
blank for no regex validation.

At the point of extraction, the user will be presented with a
single-line text field labelled with the user-defined "display name"
above it. Text entered in the field is saved when the field loses
focus. The field will flash green for 500 ms if it is saved
successfully, and it will flash red for 500 ms on a failed attempt to
save (including a failed regex match).

### Text area

A text area element is similar to an open text element, in that it
also allows arbitrary text entry, stored as a `TEXT` column with the
column name in the database back-end and in the data export file
defined by the user in the same way.

Text areas differ from open text fields in two ways: First, a text
area field is presented to the user as a rectangle 150 px tall, rather
than as a single-line height text field. This allows for greater ease
in editing longer passages of text. Second, text area fields do not
allow for regex verification.

### Date selector

A date selector element allows an extractor to enter arbitrary text,
which is then parsed by the `strtotime()` PHP function and then
formatted to an ISO-8601 compliant date (YYYY-MM-DD) for display to
the extractor, and saved to the database back-end as a MySQL `DATE`
column with the column name as specified by the user.

Parsing the text with `strtotime()` in PHP allows the user to enter
many natural language expressions to specify a date, such as "now",
"tomorrow", "September 3, 1985", "next Wed", "+3 year", etc. In the
case of a parsing failure, the field will flash red for 500 ms with
the words "Bad date format", and then be cleared.

During extraction, the user will be presented with a single-line text
field labelled with the user-defined "display name" above it. Text
entered in the field will be parsed as described above when the field
loses focus and if successfully converted to a date, the field will
flash green for 500 ms and the date will appear in ISO-8601 format.

### Single select

A single select element allows the extractor to choose one from a set
of predefined options.

An option consists of a "display name" which is paired with a "DB
value", as specified in the "Options" table. "DB value" here means the
text that will be stored in the database back-end when the option in
question is selected by the extractor, and eventually exported in a
data export file. The DB value can only be 200 characters or fewer in
length. The "display name" for an option in a single select is only
ever seen by the extractor at the time of extraction.

At the point of extraction, the extractor is presented with a list of
all the predefined options as greyed-out text with a grey
underline. Mousing over an unselected option turns the text black and
the underline purple, to aid in legibility, and to make it clear what
text goes together as a single option in the case that options contain
multiple words. Clicking an option saves that choice to the database
back-end. If it is successfully saved, the selected option retains its
black text and purple underline even when the cursor is no longer over
it. Clicking another option deselects the first one chosen, and
clicking the selected option again de-selects it.

### Multi select

A multi select element allows the extractor to choose one or many from
a set of predefined options.

A multi select is nearly identical to a single except in two ways:
First, a user is able to choose more than one option, unlike a single
select that only allows a user to choose one. Second, the way that
this is implemented in the database back-end is necessarily different.

Instead of a single column in the database back-end, a multi-select
generates one column per option. Each column is a MySQL `INT`, and
takes a value of 1 or 0, indicating selected or non-selected. Columns
are named based on the "column prefix" field in the form editor
concatenated with an underscore and the "DB column" for each option.

For example, a user could specify a multi select element for
"Location", where the "column prefix" field contains `location`, and
define two options, display name "Canada", with `ca` as its DB column
and display name "Germany" with `de` as its DB column. This would
generate two columns in the database: `location_ca` and `location_de`
(note that the underscore is added automatically).

## Editing an extraction form

An extraction form can be edited at any time, even after extractions
have already been completed. Go to "Edit extraction forms" under the
Numbat menu and click the "Edit" link in the table row corresponding
to the form of interest. This will open the form editor.

Be careful editing your form, as changes are saved automatically as
they are made.

You can change the order of the elements in your form by dragging them
up or down in the form editor by the ![Three horizontal grey
bars](../images/draggy.svg "Dragging area") area and dropping them in
a new position marked by a bold white outline. For ease of re-ordering
elements, you can vertically collapse an element's editor by clicking
on the "collapse/expand" button at the top right. Changing the order
of elements within a form will *not* cause any data loss.

You can delete an element by clicking "Delete" in the top right corner
of any element in the form editor. (You will be prompted with "Yes,
delete" and "Do not delete" before this change is saved to the
database.) Deleting an element *will result in potential data loss*,
as this will also delete the column containing any data that has been
extracted for that element already. Of course, if no extractions have
been done yet with this form, no data loss is possible.

Changing an extraction element after extractions are already complete
may result in data loss, depending on the nature of the changes
applied.

Changing an element's Display Name will not result in data
loss. Changing an element's database name will not result in data
loss.

Removing options from a Single Select element *will not* result in
data loss, because the data are stored as text in a single
column. Removing options from a Multi Select element *will* result in
data loss, because the data are stored as TRUE/FALSE responses to each
option, each in a column of its own.

## Documenting an extraction form

Numbat provides two methods for documenting an extraction form:

1. JSON-formatted export (`.json`), and
2. Markdown-formatted description (`.md`) export.

These are both available in the Forms table visible after choosing
"Extraction forms" from the Numbat menu.

The `.json` export provides a machine-readable file that contains the
exact specification of your Numbat form in a format that can be read
back into Numbat to produce exactly the same form that was
exported. This file can be used as a backup of an in-progress project,
sent to other researchers to be used in other projects or for
reproducibility, or to document the exact extraction form used in a
previously completed project. This file is not meant to be read by a
human.

The `.md` export provides a human-readable file that contains an exact
description of your Numbat form in a format that can be converted into
a Word document (via pandoc or R Studio, for example) and inserted
into a research protocol or the appendix of a publication. This file
is not meant to be machine-readable, and Numbat is not designed to
interpret an `.md` file to construct a form that matches the
description provided in one.

---

[Previous: References and reference sets](references.md)

[Next: Assignments](assignments.md)
