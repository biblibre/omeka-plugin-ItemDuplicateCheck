# Item Duplicate Check

This Omeka plugin allows to define rules in order to check while adding an item
if it already exists in Omeka database.

A rule is a combination of an item type and a list of elements.

A rule applies to an item only if its item type matches.

A duplicate is an item which have the same values in the elements defined in
rule as the item being added.

When a duplicate is found, the item is not saved and an error message is shown.
