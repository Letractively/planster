# Actors #

**User** — A typical non-technical user, using planster through a web-browser.

**System** — The planster application running on a server to which the user connects.



# Use Cases #

The following use-cases show the main scenario for each case. Where applicable _extensions_ show additional possibilities that may occur in a scenario.


## 1. Create a new Plan ##

  1. _User_ visits the planster homepage where he is presented with a simple plan creation form.
  1. _User_ enters the name of the plan in the form and presses "create".
  1. _System_ creates an plan with the name and a random ID and presents the newly created plan.



## 2. Changing the Title/Comment ##

  1. _User_ visits the page of a created plan.
  1. _User_ hovers the mouse over title or comment field, receives an indication that the field is editable by clicking on it.
  1. _User_ clicks on the title or on the comment. The title or comment changes into an input field with clearly marked buttons "ok" and "cancel".
  1. _User_ changes the input and presses "ok"
  1. _System_ updates the title or comment with the new value.

**Extensions:**

  * After _user_ has updated the title or comment, _system_ presents a non-obtrusive notification with the message "The title has been updated. Undo?" Clicking on "undo" reverts the title to the previous value.



## 3. Adding an Item ##

_todo_



## 4. Adding a User ##

_todo_



## 5. Hiding and Showing the Display of Total ##

_todo_