# Simple Form Validator

This simplified form validator provides basic server-side validation of form inputs. 
Though nowhere near as robust as other form validators (e.g. PEAR's HTML_QuickForm2), 
this may be sufficient for very simple projects.

The two class files are formvalidator.php and validationrule.php. The formvalidator
file contains the FormValidator class which provides an object-oriented approach 
to simple form validation. The validationrule file is used internally by the 
FormValidator to store user-defined validation rules as objects.

This code was originally created as part of a class project and was a learning
experience in working with OOP in PHP.


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

### sanitize(text)
Cleans up text to remove html tags, and deal with magic quotes. This method is 
public and may be used in a stand-alone fashion, but it is also called automatically 
on all inputs passed in by the addEntries method.


## FormValidator Validation Methods

Each ruletype has a corresponding method in the FormValidator class. When the 
validate() method is used, the FormValidator loops through the user-supplied rules 
and tests the value of the field using the corresponding method.

These methods are also public and can be used in a stand-alone fashion. Each of 
these validation methods returns a boolean value.

### longerThan(value, minlength)
Tests if a value (string) is longer than a minimum length (integer). 

### shorterThan(value, maxlength)
Tests if a value (string) is shorter than a maximum length (integer).

### asNumber(value)
Tests if a given value is numeric using a regular expression (either an integer or float).

### numberBetween(value, range)
Tests if a given value is numeric and is between the two values which are passed as 
an array to the second parameter.

### asZip(value)
Tests if the given input is a properly formatted US zip code using a regular 
expression. To be valid, the zip code must have 5 digits, which can be followed 
by an optional dash and four more digits.

### asEmail(value)
Tests if a given input is a properly formatted email address. This method utilizes 
PHP's native FILTER_VALIDATE_EMAIL.

### asPhoneNumber(value)
Validates the input as a US phone number. The number must include a 3 digit area 
code (optionally enclosed in parentheses), followed by an optional dash, 3 more 
digits, another optional dash, and finally four digits. Each of the three sets of 
digits may be optionally separated by spaces as well. TODO: add support for country
codes and extensions.

### asCreditCard(value)
Validates the input as a properly formatted credit card number. Supported card 
types: Visa, Mastercard, American Express, Discover. This method will verify that 
the card number has the proper number of digits, and that it passes against the 
mod10 algorithm. Spaces or dashes separating the digits are allowed.


## Sample Use

Please view the sample-use.php file to see the Form Validator in action with a simple
use case which demonstrates several of the above methods./////