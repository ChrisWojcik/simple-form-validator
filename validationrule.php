<?php
/**
 * Object-oriented interface for specifying validation rules
 * 
 * @category Forms
 * @author Christopher Wojcik <cpw1485@gmail.com>
 */
class ValidationRule
{
    /**
     * The name of the field associated with this rule
     * 
     * @var string
     */
    private $_fieldname;
    
    /**
     * The error message to display if validation fails
     * 
     * @var string 
     */
    private $_message;
    
    /**
     * One of a predefined set of types of rule
     * 
     * @var string 
     */
    private $_ruletype;
    
    /**
     * Optional additional criteria to evaluate the rule (e.g. a min length)
     * 
     * @var mixed
     */
    private $_criteria = null;
    
    /**
     * Class constructor
     * @param   string  $fieldname
     * @param   string  $message
     * @param   string  $ruletype
     * @param   mixed   $criteria 
     */
    public function __construct($fieldname, $message, $ruletype, $criteria = null)
    {
        $this->_fieldname = $fieldname;
        $this->_message = $message;
        $this->_ruletype = $ruletype;
        $this->_criteria = $criteria;
    }
    
    /**
     * Magic method to get private properties, prepends an underscore
     * 
     * @param   string $property The requested property
     * @return  mixed
     */
    public function __get($property)
    {
        $name = '_' . $property;
        if (isset ($this->$name)) {
            return $this->$name;
        }
        return false;
    }
}