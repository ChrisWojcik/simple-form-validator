# Simple Form Validator

This simplified form validator includes two class files (the main FormValidator 
and a second class to store user-defined rules) to provide basic server-side 
validation of form inputs. Though nowhere near as robust as other form validators 
(e.g. PEAR HTML_QuickForm2), this may be sufficient for very simple projects.

This code was originally created as part of a class project.


## Features

* Automatically "sanitizes" form inputs to strip HTML tags and deal with magic 
quotes.
* Can validate form inputs according to a variety of pre-defined ruletypes
* Allows you to specify a callback function to add a custom ruletype

## FormValidator Basic Methods

### addRule(fieldname, message, ruletype, criteria=null)
Add a validation rule by specifying the fieldname, a custom error message if the 
validation fails, the type of rule to test against (see below), and a criteria 
for those ruletypes which require it.

You can add multiple rules for each field and they will be checked in the order supplied

The following ruletypes are supported:

* 'required' - Input must not be blank.
* 'minlength' - Input must be longer than the number of characters specified by 
  the criteria parameter (criteria must be an integer).
* 'maxlength' - Input must be shorter than the number of characters specified by 
  the criteria parameter (criteria must be integer).
* 'numeric' - Input must be a number (either integer or float).
* 'numeric-range' - Input must be a number and be within the range specified by 
  the criteria parameter. The criteria must be an array including the min and max
  values: array(min, max)
* 'in-array' - Input must match one of the values in the supplied array. Pass the 
  variable containing the array into the criteria parameter.
* 'regex' - Input must match the regular expression passed to the criteria parameter.
* 'email' - Input must be a properly formatted email address, e.g. email@domain.com.
* 'phone' - Input must be a properly formmated US phone number. TODO: add support for 
  country codes and extensions
* 'zip' - Input must be a properly formmated US zip code
* 'creditcard' - Input must be a properly formmated credit card number. Supported 
  card types: Visa, Mastercard, American Express, Discover.
* 'callback' - Pass the name of a user-defined function as the criteria. This ruletype 
is especially useful if you want to combine two different fields and test them 
together. Your callback should return a boolean.

### addEntries(fields)
Pass in an array of form data in the format: fieldname => value, e.g. the $_POST array. 
This method also automatically sanitizes the form inputs.

### getEntries()
Retrieve the form entries after they've been cleaned

### validate()
Loops through the supplied rules and tests each field against its rules.

### foundErrors()
Returns true if any field failed to validate, or false if all fields passed.

### getErrors()
Retrieve the error messages in the form: fieldname => msg.




