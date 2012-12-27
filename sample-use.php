<?php
require_once('formvalidator.php');
require_once('validationrule.php');

// Set a default value for each form field (blank)
$name = '';
$zip = '';
$state = '---';
$month = '---';
$year = '---';

// Arrays used to populate the select boxes, see below
$states = array( "AK", "AL", "AR", "AZ", "CA", "CO", "CT", "DC",
                 "DE", "FL", "GA", "HI", "IA", "ID", "IL", "IN", "KS", "KY", "LA",
                 "MA", "MD", "ME", "MI", "MN", "MO", "MS", "MT", "NC", "ND", "NE",
                 "NH", "NJ", "NM", "NV", "NY", "OH", "OK", "OR", "PA", "RI", "SC",
                 "SD", "TN", "TX", "UT", "VA", "VT", "WA", "WI", "WV", "WY");

$months = array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');

// Form will be validated when the form is posted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Instantiate the validator object
    $validator = new FormValidator();
    
    /* 
     * Add validation rules (fieldname, error msg, rule type, criteria)
     * A field can have multiple rules and will validate them in the order they
     * are provided
     */
    $validator->addRule('name', 'Name is a required field', 'required');
    $validator->addRule('name', 'Name must be longer than 2 characters', 'minlength', 2);
    $validator->addRule('zip', 'Zip Code is a required field', 'required');
    $validator->addRule('zip', 'Invalid Zip Code', 'zip');
    $validator->addRule('state', 'Please select a state', 'in-array', $states);
    $validator->addRule('birthday', 'Please select your birthday', 'callback', 'validBirthday');
    
    /*
     * Callback function to validate the birthday month and year as one field
     * You can pass in the validator object to gain access to its public methods
     * Callbacks must return a boolean
     */
    function validBirthday($validator)
    {
        $months = array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep',
                        'Oct','Nov','Dec');
        
        if (in_array($_POST['month'], $months) && 
            $validator->numberBetween($_POST['year'], array(1900, 2013))) {
            return true;
        }
        return false;
    }
    
    // Input the POST data and check it
    $validator->addEntries($_POST);
    $validator->validate();
    
    // Retrieve an associative array of "sanitized" form inputs (HTML tags stripped, etc.)
    $entries = $validator->getEntries();
    
    // Replace the default field values with what the user submitted
    foreach ($entries as $key => $value) {
        ${$key} = $value;
    }
    
    /* 
     * Conditional logic can be used based on whether errors were found
     * e.g. redirecting to a different page on success
     */
    if ($validator->foundErrors()) {
        $errors = $validator->getErrors();
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Simple Form Validator</title>
    <meta name="description" content="Simple Form Validator">
    <meta name="author" content="Christopher Wojcik">
    <style>
        form ol {
            list-style: none;
            padding: 0;
            overflow: hidden;
        }
        
        label {
            float: left;
        }
        
        input, select, textarea {
            float: left;
            clear: both;
        }
        
        #birthday select {
            clear: none;
            margin-right: 10px;
        }
        
        .combo-field {
            clear: both;
        }
        
        form li {
            margin-top: 20px;
            float: left;
            clear: both;
        }
        
        #error-block {
            background: pink;
            border: 1px solid red;
            color: red;
            width: 50%;
        }
    </style>
</head>
<body>
   <h1>Simple Form Validation</h1>
<?php if (!empty($errors)) : ?>
    <div id="error-block">
        <ul>
<?php foreach($errors as $field => $msg) : ?>
            <li><?php echo $msg; ?></li>
<?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
    <form method="post" action="">
        <ol>
            <li>
                <label for="name">*Name</label>
                <input type="text" name="name" id="name" value="<?php echo $name; ?>"/>
            </li>
            <li>
                <label for="zip">*Zip Code</label>
                <input type="text" name="zip" id="zip" value="<?php echo $zip; ?>"/>
            </li>
            <li>
                <label for="state">*State</label>
                <select name="state" id="state">
                    <option value="---" <?php if ($state == '---') : ?>selected<?php endif; ?>>State</option>
<?php foreach ($states as $s) : ?>
                        <option value="<?php echo $s; ?>" <?php if ($state == $s) : ?>selected<?php endif; ?>><?php echo $s; ?></option>
<?php endforeach; ?>
                </select>
            </li>
            <li id="birthday">
                <label>*Birthday:</label>
                <div class="combo-field">
                    <select name="month" id="month">
                        <option value="---" <?php if ($month == '---') : ?>selected<?php endif; ?>>Month</option>
<?php foreach ($months as $m) : ?>
                        <option value="<?php echo $m; ?>" <?php if ($month == $m) : ?>selected<?php endif; ?>><?php echo $m; ?></option>
<?php endforeach; ?>
                    </select>
                    <select name="year" id="year">
                        <option value="---" <?php if ($year == '---') : ?>selected<?php endif; ?>>Year</option>
<?php for($y = 1900; $y < 2013; $y++) : ?>
                        <option value="<?php echo $y; ?>" <?php if ($year == $y) : ?>selected<?php endif; ?>><?php echo $y; ?></option>
<?php endfor; ?>
                    </select>
                </div>
            </li>
            <li>
                <input type="submit" name="submit" value="Submit" id="submit"/>
            </li>
        </ol>
    </form>
</body>
</html>