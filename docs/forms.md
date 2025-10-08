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

## Editing an extraction form

An extraction form can be edited at any time. Go to "Edit extraction
forms" under the Numbat menu and click the "Edit" link in the table
row corresponding to the form of interest. This will open the form
editor.

Be careful editing your form, as changes are saved automatically as
they are made.

You can change the order of the elements in your form by dragging them
up or down in the form editor by the ![Three horizontal grey
bars](../images/draggy.svg "Dragging area") area and dropping them in
a new position marked by a bold white outline. For ease of re-ordering
elements, you can vertically collapse an element's editor by clicking
on the "collapse/expand" button at the top right. Changing the order
of elements within a form will not cause any data loss.

You can delete an element by clicking "Delete" in the top right corner
of any element in the form editor. (You will be prompted with "Yes,
delete" and "Do not delete" before this change is saved to the
database.) Deleting an element will result in potential data loss, as
this will also delete the column containing any data that has been
extracted for that element already. Of course, if no extractions have
been done yet with this form, no data loss is possible.


## Documenting an extraction form

---

[Previous: References and reference sets](references.md)

[Next: Assignments](assignments.md)
