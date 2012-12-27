# Simple Form Validator

This simplified form validator includes two class files to provide basic server-
side validation of form inputs. Though nowhere near as robust as other form
validators (e.g. PEAR HTML_QuickForm2), this project is lightweight and may be
sufficient for very simple projects.

This code was originally created as part of a class project.


## Features

Automatically "sanitizes" form inputs to strip HTML tags and deal with magic 
quotes.

Provides methods for validating form inputs according to the following criteria:

* Input is required (must not be blank)
* Input must match a supplied regular expression
* Input must one of the values in a supplied array
* Input must be longer or shorter than a specified number of characters
* Input must be a valid number (either integer or float)
* Input must be a number within a certain range
* Input must be a properly formatted zip code
* Input must be a properly formmated email address
* Input must be a US Phone number (country codes and ext not currently supported)
* Input must be a properly formmatted credit card number (Visa, Mastercard, 
  American Express, Discover)
* Input must validate according to a custom callback function