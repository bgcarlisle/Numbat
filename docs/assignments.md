[Back to Index](README.md)

# Assignments

Assignments are represented as a row in the `assignments` table in the
database back-end for your Numbat instance. Each row represents a link
between an assigner (a user on your Numbat instance), an extractor (a
user on your Numbat instance, possibly the same one), the form to be
used in this extraction, the reference set and individual reference
within that reference set to extract.

Assignments are added via the page found by clicking "Manage user
assignments" from the Numbat menu. From this page, you will see a
table containing all the reference sets in your Numbat instance, each
with a "View" link.

## The assignment grid

The "View" link leads to a page with a table containing one row per
reference in the reference set in question, and a column for every
form in the Numbat instance. Each cell contains all the names of users
on your Numbat instance with a "✓" and a green outline or an "✗" and a
red outline beside them, indicating which references in the reference
set in question have been assigned to which users to extract with
which forms.

When an extractor is given an assignment, this appears on the page
found by choosing "Do extractions" from the Numbat menu.
Screening-type extraction forms are presented on a per-reference set
basis, allowing extractors to click to open a grid showing all the
assigned references to screen. Normal extraction forms are presented
on a per-reference basis, allowing extractors to click to open
individual references to be extracted.

Note: There is no "save changes" or "undo" on this page. Assignment
changes are live instantly. So before adding or removing assignments,
make sure you're doing what you mean to.

Also note: Removing an assignment does *not* delete any extractions
already completed by user. It only removes that extraction from their
"Do extractions" list. It will still be available in the extractions
export page, if it was completed.

Assignments can be manually added or removed by clicking the extractor
name in the grid on the reference set assignment page. When the user
name has a "✓" beside it, the assignment has been successfully
added. When the user name has an "✗" beside it, the assignment has
been successfully removed.

Assignments can be added or removed en masse using the tools provided
above the grid of references on the reference set assignment
page. This is done in 3 steps:

1. Select references. Either check/uncheck the relevant rows in the
   grid, or use the "select" tools above the grid. You can select a
   random number of references, a random number of references with
   assignments/extractions already done, a random number with
   assignments/extractions already done with a particular form, or all
   the references where a user-selected column corresponds to a value
   of that column in the reference set. If Numbat is told to extract a
   greater number of random references than there are that meet the
   criteria provided (e.g. "select 50 random references" when the
   reference set contains only 10 references), all of the references
   that meet the criteria will be selected.
2. Choose a form. A user can in principle be assigned to extract the
   same reference using an arbitrary number of different forms.
3. Choose a user. The action taken using the references and form
   selected will only affect the chosen user. (Once the action is
   completed, the selections will not be cleared, so you can repeat
   them again for another user, if desired.)
4. Perform the action of assigning the selected form and reference to
   the user in question, or removing that assignment.

There is a "Download assignments" link at the top-right of the grid on
each reference set assignments page. This link allows the user to
download a TSV of all assignments for that reference set and their
extraction status.
Each extractor name can be manually clicked to assign or un-assign the
reference and form indicated by the row and column, respectively. This
is slow and cumbersome, so there is a set of tools for selecting
references above the assignment grid.

## Mass-selecting references for assignment

The most basic tool allows a user to select a random number of
references from the grid. The number of references to be selected is
by default, half of the number of references in the reference set. If
that number is equal to or greater than the number of references in
the reference set, all will be selected. The next selection tool
allows a user to select a random number of references that have
already been assigned to other extractors with a particular form. The
next selection tool allows random selection from among references that
have had a given number of extractors complete their assignments.

If the user had the forethought to include a column that contains
groupings or 

When the desired references have been selected, the checkbox at the
far left of the assignment grid will be checked. This doesn't mean
that anything has been assigned yet. You can hide the non-selected
references using the button provided ("Hide non-selected references")
and show them again with the "Show all references" button.

## Known issues

The assignment viewer page can lag substantially in the case of a
large number of references. While there is no built-in limit to the
number of references in a reference set, 

---

[Previous: Forms and extraction elements](forms.md)

[Next: Extractions](extractions.md)
