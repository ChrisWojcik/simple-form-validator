<?php
/**
 * Validate form entries according to specified rules
 * 
 * @category Forms
 * @author Christopher Wojcik <cpw1485@gmail.com>
 */
class FormValidator 
{    
    /**
     * An array of rule objects
     * 
     * @var array
     */
    private $_rules = array();
    
    /**
     * An array of fields to be checked in the form: fieldname => value
     * 
     * @var array 
     */
    private $_fields = array();
    
    /**
     * An array of errors  in the form: fieldname => msg
     * 
     * @var array 
     */
    private $_errors = array();
    
    /**
     * Add a new validation rule
     * 
     * @param   string  $fieldname  The name of the field
     * @param   string  $message    The error message to display on failure
     * @param   string  $ruletype   The type of rule (e.g. 'required')
     * @param   mixed   $criteria   Any additional criteria needed to evaluate the rule
     * @return  void
     */
    public function addRule($fieldname, $message, $ruletype, $criteria = null)
    {
        $this->_rules[] = new ValidationRule($fieldname, $message, $ruletype, $criteria);
    }
    
    /**
     * Take in an array of form data in the form fieldname => value
     * e.g. the $_POST array, and clean-up and store each one
     * 
     * @param   array   $fields The form data
     * @return  void
     */
    public function addEntries($fields) {
        foreach ($fields as $fieldname => $value) {
            $this->_fields[$fieldname] = $this->sanitize($value);
        }
    }
    
    /**
     * Return the fields after they've been cleaned
     * 
     * @return  array   The form data
     */
    public function getEntries() {
        return $this->_fields;
    }
    
    /**
     * Loop through all the supplied rules and test each field against its rule
     * 
     * @see _testRule()
     * @return  void 
     */
    public function validate() {
        foreach ($this->_rules as $rule) {
            $this->_testRule($rule);
        }
    }
    
    /**
     * Check if errors were found
     * 
     * @return  boolean 
     */
    public function foundErrors()
    {
        if (count($this->_errors)) {
            return true;
        }
        return false;
    }
    
    /**
     * Retrieve the error messages in the form: fieldname => msg
     * 
     * @return  array   The error messages 
     */
    public function getErrors()
    {
        return $this->_errors;
    }  
    
    /**
     * Test if a string is longer than a minimum length
     * 
     * @param   string  $value  The string to be tested
     * @param   integer $min    The minimum length
     * @return  boolean 
     */
    public function longerThan($value, $min)
    {
        if (strlen($value) >= $min) {
            return true;
        }
        return false;
    }
    
    /**
     * Test if a string is shorter than a maximum length
     * 
     * @param   string  $value  The string to be tested
     * @param   integer $max    The maximum length
     * @return  boolean 
     */
    public function shorterThan($value, $max)
    {
        if (strlen($value) <= $max) {
            return true;
        }
        return false;
    }
    
    /**
     * Test if a given value is a valid number (integer or float)
     * 
     * @param   mixed   $value  The value to test
     * @return  boolean 
     */
    public function asNumber($value)
    {
        if (preg_match("/^[-+]?[0-9]*\.?[0-9]+$/", $value)) {
            return true;
        }
        return false;
    }
    
    /**
     * Check if a number is between two values
     * 
     * @param   mixed   $value  The number to test
     * @param   array   $range  An array containing the min and max
     * @return  boolean 
     */
    public function numberBetween($value, $range)
    {
        $min = $range[0];
        $max = $range[1];
        
        if ((preg_match("/^[-+]?[0-9]*\.?[0-9]+$/", $value)) && $value >= $min && $value <= $max) {
            return true;
        }
        return false;
    }
    
    /**
     * Validate the input as a zipcode
     * 
     * @param   string    $value    The value to test
     * @return  boolean 
     */
    public function asZip($value)
    {
        /*
         * 5 digits followed by optional - and 4 more
         */
        if(preg_match("/^[0-9]{5}( *- *[0-9]{4})?$/",$value)) { 
            return true;
        }
        return false;
    }
    
    /**
     * Validate the input as an email address
     * 
     * @param   string  $value The value to test
     * @return  boolean
     */    
    public function asEmail($value)
    {
        if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return true;
        }
        return false;
    }
    
    /**
     * Validate the input as a phone number
     * 
     * @param   string  $value  The value to test
     * @return  boolean 
     */
    public function asPhoneNumber($value)
    {
        /*
         * Optional enclosing parentheses, 3 digits, a set of 3 digits followed
         * by a set of 4 with optional spaces and a dash between them         *  
         */
        if (preg_match("/^\(?[0-9]{3}\)? *-? *[0-9]{3} *-? *[0-9]{4}$/", $value)) {
            return true;
        }
        return false;
    }
    
    /**
     * Validate the input as a credit card number
     * 
     * @param   type    $value  The value to test
     * @return  boolean 
     */
    public function asCreditCard($value)
    {
        /*
         * Strip out any spaces or dashes to leave only the digits
         */
        $value = preg_replace("/[- ]/", '', $value);
        
        /*
         * Supported card types: Visa, Mastercard, American Express, Discover
         */
        if (!(preg_match("/^(?:4[0-9]{12}(?:[0-9]{3})?|5[1-5][0-9]{14}|6(?:011|5[0-9][0-9])[0-9]{12}|3[47][0-9]{13})$/", $value))) {
            return false;
        }
        
        /*
         * Take the given number and convert it to an array of digits
         */
        $digits = str_split($value);
    
        /*
         * Pop off the last digit to use as the check digit
         */
        $check_dig = array_pop($digits);
    
        /*
         * Reverse the order of the array so we can move from right to left
         */
        $digits = array_reverse($digits);
    
        /*
         * Starting with the first digit, double every other one then sum its digits
         */
        for ($i = 0, $j = count($digits); $i < $j; $i += 2) {
            $digits[$i] *= 2;
            $digits[$i] = array_sum(str_split($digits[$i]));
        }
    
        /*
         * The sum of all the new digits + the check must be evenly divisible by 10
         */
        $sum = array_sum($digits) + $check_dig;        
        return ($sum % 10) == 0;
    }
    
    /**
     * Clean up a string to remove html tags, strip slashes, etc.
     * 
     * @param   string  $text   The input text
     * @return  string
     */
    public function sanitize($text) 
    {
        $text = trim(strip_tags($text));

        if (get_magic_quotes_gpc()) {
            $text = stripslashes($text);
        }
        return $text;
    }
    
    /**
     * Test a field against a given validation rule
     * 
     * @param   ValidationRule  $rule   A rule object
     * @return  void 
     */
    private function _testRule($rule) 
    {
        /*
         * If there's already an error for this field, don't overwrite it
         */
        if (isset($this->_errors[$rule->fieldname])) {
            return;
        }
        
        /*
         * A field may not have been set (e.g. checkboxes)
         */
        if (isset($this->_fields[$rule->fieldname])) {
            $value = $this->_fields[$rule->fieldname];
        } 
        else {
            $value = null;
        }
        
        /*
         * Determine the rule type and then perform the test
         * Supported rule types:    required, 
         *                          minlength, 
         *                          maxlength, 
         *                          numeric, 
         *                          numeric-range,
         *                          in-array,
         *                          regex,
         *                          email,
         *                          phone,
         *                          zip,
         *                          creditcard,
         *                          callback         * 
         */
        switch ($rule->ruletype) {
            case 'required' :
                if (empty($value)) {
                    $this->_errors[$rule->fieldname] = $rule->message;
                    return;
                }
                break;
            case 'minlength' :
                if (!($this->longerThan($value, $rule->criteria))) {
                    $this->_errors[$rule->fieldname] = $rule->message;
                    return;
                }
                break;
            case 'maxlength' :
                if (!($this->shorterThan($value, $rule->criteria))) {
                    $this->_errors[$rule->fieldname] = $rule->message;
                    return;
                }
                break;
            case 'numeric' :
                if (!($this->asNumber($value))) {
                    $this->_errors[$rule->fieldname] = $rule->message;
                    return;
                }
                break;
            case 'numeric-range' :
                if (!($this->numberBetween($value, $rule->criteria))) {
                    $this->_errors[$rule->fieldname] = $rule->message;
                    return;
                }
                break;
            case 'in-array' :
                if (!(in_array($value, $rule->criteria))) {
                    $this->_errors[$rule->fieldname] = $rule->message;
                    return;
                }
                break;
            case 'regex' :
                if (!(preg_match($rule->criteria, $value))) {
                    $this->_errors[$rule->fieldname] = $rule->message;
                    return;
                }
                break;
            case 'email' :
                if (!($this->asEmail($value))) {
                    $this->_errors[$rule->fieldname] = $rule->message;
                    return;
                }
                break;
            case 'phonenumber' :
                if (!($this->asPhoneNumber($value))) {
                    $this->_errors[$rule->fieldname] = $rule->message;
                    return;
                }
                break;
            case 'zip' :
                if (!($this->asZip($value))) {
                    $this->_errors[$rule->fieldname] = $rule->message;
                    return;
                }
                break;
            case 'creditcard' :
                if (!($this->asCreditCard($value))) {
                    $this->_errors[$rule->fieldname] = $rule->message;
                    return;
                }
                break;
            case 'callback' :
                if (is_callable($rule->criteria)) {
                    $return = call_user_func($rule->criteria, $this);
                    if (!$return) {
                        $this->_errors[$rule->fieldname] = $rule->message;
                        return;
                    }
                }
                break;
        }
    }
}