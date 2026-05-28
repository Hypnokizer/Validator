<?php 
// @TODO extends Database...
// @TODO finish docblock
// @TODO check spacing
// @TODO type hinting
// @TODO periods!



/**
 * Quickly validate data
 * 
 * long desc @TODO. based on ?
 * 
 * @author Nathan Kizer <hypnokizer@gmail.com>
 * @version 7.0
 * @revision 2026-05-18 Added ability to chain methods
 */

namespace Hypnokizer;

use DateTime;
use Exception;

class Validator {

    /**
     * Data to validate.
     * @access protected;
     * @var array
     */
    protected $data;

    /**
     * Currently selected key/field to validate data.
     * @access protected
     * @var string
     */
    protected $currentfield;

    /**
     * Alias to use in error messages instead of field name.
     * @access protected
     * @var string
     */
    protected $currentalias;

    /**
     * Error message responses. you can change messages here. The "{field}" tag refers to the field name.
     * @access protected
     * @var array
     */
    protected $responses;
    
    /**
     * Error messages generated after the validation of each field.
     * @access public
     * @var array
     */
    public $errors;

    /**
     * Check to see if the next validation on the field should run or not.
     * @access protected
     * @var bool
     */
    protected $next;


    /**
     * Create new instance of validator class.
     * 
     * Sets many of the variable defaults. Defines the responses for error messages.
     * 
     * @param array $data Data to validate.
     * @return object Validator
     */
    public function __CONSTRUCT($data) {
        $this->data = $data;
        $this->currentfield = NULL;
        $this->currentalias = NULL;

        // @TODO auto pull POST or GET if DATA not given?
        // @TODO basic escaping? trim() and htmlspecialchars() ?
        // @TODO give positive messages: {field} must contain characters A-Z or 0-9
        // @TODO set these here or use method setErrorMessage()?
        // @TODO match up responses to completed methods
        $this->responses = array(
            'alpha' => '{field} must contain alphabetic characters only',
            'alphanumeric' => '{field} must contain alphanumeric characters only',
            'contains' => '{field} must contain any of the following characters: {chars}',
            'date' => '{field} must be a valid date',
            'dateafter' => '{field} is not after {date}',
            'datebefore' => '{field} is not before {date}',
            'email' => '{field} must be a valid email address',
            'enum' => '{field} is not in the define list of accepted values',
            'equals' => '{field} does not match',
            'integer' => '{field} is not an integer',
            'length' => '{field} should be {length} characters',
            'match' => '{field} does not match',
            'maxlength' => '{field} is too long',
            'maxvalue' => '{field} is too high',
            'minlength' => '{field} is too short',
            'minvalue' => '{field} is too low',
            'money' => '{field} contains non-currency characters',
            'numeric' => '{field} must contain numbers only',
            'phone' => '{field} is not a valid phone number',
            'required' => '{field} is required',
            'state' => '{field} is not a valid U.S. state',
            'zip' => '{field} is not a valid zip code'
        );

        $this->errors = array();
        $this->next = true;
    }



    /**
     * create and add an error message after each validation field
     * @TODO can I add error responses like {@link construct()}?
     * 
     * @param string $type ???
     * @param array $others Additional tags used in the error response.
     * @return ???
     */
    protected function addErrorMessage($type, $others = array()) {
        // decide whether to use field name or alias
        if($this->currentalias) {
            $fieldname = ucfirst($this->currentalias);
        }
        else {
            $fieldname = ucfirst($this->currentfield);
        }

        // define response message
        $message = str_replace('{field}', $fieldname, $this->responses[$type]);

        // replace additional tags in response
        foreach($others as $key => $val) {
            $message = str_replace('{' . $key . '}', $val, $message);
        }

        // add response to errors
        $this->errors[$this->currentfield] = $message;
    }





    /**
     * Determines if the current field or value exists. Used in most validation checks.
     * 
     * @return bool 
     */
    protected function exists() {
        if(!isset($this->data[$this->currentfield]) || !$this->data[$this->currentfield]) {
            return false;
        }
        else {
            return true;
        }
    }



    /**
     * Sets custom error response within methods.
     * 
     * @param string $field Field name to associate error message.
     * @param string $message Error message.
     * @return object 
     */
    protected function setErrorMessage(string $field, string $message) {
        $this->responses[$field] = $message;
    }




    /**
     * Sets the field name to start the validation.
     * 
     * @param string $name The name of the field/key to validate.
     * @param string $alias Optional alias to use on error messages instead of the field name. 
     * @return static 
     */
    public function field(string $name, string $alias = NULL) {
        $this->currentfield = $name;
        $this->next = true;
        $this->currentalias = $alias;
        return $this;
    }





    // @TODO create a null method to convert empty strings to NULL? watch for zero values showing as empty...



    /**
     * Check for alphabetic characters.
     * 
     * @param array $ignore Optional characters to allow, including whitespace. 
     * @return static 
     */
    public function alpha($ignore = array()) {
        if($this->next && $this->exists() && !ctype_alpha(str_replace($ignore, '', $this->data[$this->currentfield]))) {
            $this->addErrorMessage('alpha');
            $this->next = false;
        }

        return $this;
    }



    /**
     * Check for alphanumeric characters.
     * 
     * @param array $ignore Optional characters to allow, including whitespace.
     * @return static 
     */
    public function alphanumeric($ignore = array()) {
        if($this->next && $this->exists() && !ctype_alnum(str_replace($ignore, '', $this->data[$this->currentfield]))) {
            $this->addErrorMessage('alphanumeric');
            $this->next = false;
        }

        return $this;
    }




    /**
     * Change case of string: capitalize, uppercase, lowercase.
     * 
     * @param string $case Transformation to perform on text string. 
     * @return static 
     */
    public function changecase($case) {
        switch($case) {
            case 'capitalize':
                $this->data[$this->currentfield] = ucwords(strtolower($this->data[$this->currentfield]), " \t\r\n\f\v'-");
                break;

            case 'uppercase':
                $this->data[$this->currentfield] = strtoupper($this->data[$this->currentfield]);
                break;

            case 'lowercase':
                $this->data[$this->currentfield] = strtolower($this->data[$this->currentfield]);
                break;
        }

        return $this;
    }



    /**
     * Check if the value contains specific characters.
     * 
     * @param string $chars Set of characters to search for in one string (Ex: '@#abc123').
     * @return static 
     * @todo review the preg_match
     */
    public function contains($chars) {
        if($this->next && $this->exists()) {
            if(preg_match("/[" . $chars . "]/", $this->data[$this->currentfield]) == false) {
                $this->addErrorMessage('contains', ['chars' => $chars]);
                $this->next = false;
            }
        }

        return $this;
    }



    /**
     * Check for a valid date.
     * 
     * @return static 
     */
    public function date() {
        if($this->next && $this->exists()) {
            try {
                $dt = new DateTime($this->data[$this->currentfield]);
                $this->data[$this->currentfield] = $dt->format('Y-m-d');
            }
            catch(Exception $e) {
                $this->addErrorMessage('date');
                $this->next = false;
            }
        }

        return $this;
    }




    /**
     * Check if a date comes after the given date.
     * 
     * @param string $date The date to compare. 
     * @return static 
     */
    public function dateafter($date) {
        if($this->next && $this->exists()) {
            try {
                $dt1 = new DateTime($this->data[$this->currentfield]);
                $dt2 = new DateTime($date);
            }
            catch(Exception $e) {
                // set a custom message
                $this->setErrorMessage($this->currentfield, 'The beginning date range is not a valid format');
                $this->addErrorMessage($this->currentfield);
                $this->next = false;
            }

            // compare dates
            if($dt1 < $dt2) {
                $this->addErrorMessage('dateafter', ['date' => $date]);
                $this->next = false;
            }
        }

        return $this;
    }



    /**
     * Check if a date comes before the given date.
     * 
     * @param string $date The date to compare. 
     * @return static 
     * @todo check same date comparisons
     */
    public function datebefore($date) {
        if($this->next && $this->exists()) {
            try {
                $dt1 = new DateTime($this->data[$this->currentfield]);
                $dt2 = new DateTime($date);
            }
            catch(Exception $e) {
                // set a custom message
                $this->setErrorMessage($this->currentfield, 'The ending date range is not a valid format');
                $this->addErrorMessage($this->currentfield);
                $this->next = false;
            }

            // compare dates
            if($dt1 > $dt2) {
                $this->addErrorMessage('datebefore', ['date' => $date]);
                $this->next = false;
            }
        }

        return $this;
    }




    /**
     * Check for email address.
     * 
     * @return static 
     */
    public function email() {
        if($this->next && $this->exists() && !filter_var($this->data[$this->currentfield], FILTER_VALIDATE_EMAIL)) {
            $this->addErrorMessage('email');
            $this->next = false;
        }

        return $this;
    }



    /**
     * Check if a value is in the list of approved values.
     * 
     * @param array $list List of approved values.
     * @return static 
     */
    public function enum($list) {
        if($this->next && $this->exists() && !in_array($this->data[$this->currentfield], $list)) {
            $this->addErrorMessage('enum');
            $this->next = false;
        }

        return $this;
    }



    /**
     * Check if value is equal to a given value.
     * 
     * @param mixed $value The value to match. Can be a string, integer, bool, or float.
     * @return static 
     */
    public function equals($value) {
        if($this->next && $this->exists() && $this->data[$this->currentfield] !== $value) {
            $this->addErrorMessage('equals');
            $this->next = false;
        }

        return $this;
    }



    /**
     * Check for integer
     * 
     * @return static 
     */
    public function integer() {
        if($this->next && $this->exists() && filter_var($this->data[$this->currentfield], FILTER_VALIDATE_INT) === false) {
            $this->addErrorMessage('integer');
            $this->next = false;
        }

        return $this;
    }


    /**
     * Check for exact length of string.
     * 
     * @param int $length The required length of string.
     * @return static 
     */
    public function length($length) {
        if($this->next && $this->exists() && strlen($this->data[$this->currentfield]) !== $length) {
            $this->addErrorMessage('length', ['length' => $length]);
            $this->next = false;
        }

        return $this;
    }


    /**
     * Check for maximum length of string.
     * 
     * @param int $length The maximum allowed length of string.
     * @return static 
     * @see minlength()
     */
    public function maxlength($length) {
        if($this->next && $this->exists() && strlen($this->data[$this->currentfield]) > $length) {
            $this->addErrorMessage('maxlength');
            $this->next = false;
        }

        return $this;
    }


    /**
     * Check for maximum value of integer or float.
     * 
     * @param mixed $value The maximum value of the data. This can be an integer or float. 
     * @return static 
     * @see numeric()
     * @see minvalue()
     */
    public function maxvalue($value) {
        if($this->next && $this->exists() && $this->data[$this->currentfield] > $value) {
            $this->addErrorMessage('maxvalue');
            $this->next = false;
        }

        return $this;
    }


    /**
     * Check for minimum length of string.
     * 
     * @param int $length The maximum allowed length of string. 
     * @return static
     * @see maxlength() 
     */
    public function minlength($length) {
        if($this->next && $this->exists() && strlen($this->data[$this->currentfield]) < $length) {
            $this->addErrorMessage('minlength');
            $this->next = false;
        }

        return $this;
    }


    /**
     * Check for minimum value of integer or float.
     * 
     * @param mixed $value The minimum value of the data. This can be an integer or float. 
     * @return static
     * @see numeric()
     * @see maxvalue() 
     */
    public function minvalue($value) {
        if($this->next && $this->exists() && $this->data[$this->currentfield] < $value) {
            $this->addErrorMessage('minvalue');
            $this->next = false;
        }

        return $this;
    }



    /**
     * Check for currency. Changes string to decimal by removing dollar signs, commas, decimals, and negative signs.
     * 
     * @param array $ignore Optional characters to allow, including whitespace. 
     * @return static 
     * @todo review this 
     * @todo type cast as float after removing symbols?
     */
    public function money($ignore = array('$', ',', '.', '-')) {
        // remove all characters other than numbers, dollars, commas; negative signs?
        if($this->next && $this->exists() && !ctype_digit(str_replace($ignore, '', $this->data[$this->currentfield]))) {
            $this->addErrorMessage('money');
            $this->next = false;
        }

        // remove all characters except numbers, decimals, and negative signs
        $this->data[$this->currentfield] = str_replace(['$', ','], '', $this->data[$this->currentfield]);

        return $this;
    }


    /**
     * Check for numeric characters.
     * 
     * @return static 
     */
    public function numeric() {
        if($this->next && $this->exists() && !is_numeric($this->data[$this->currentfield])) {
            $this->addErrorMessage('numeric');
            $this->next = false;
        }

        return $this;
    }


    /**
     * Check for a date string to a period (Ex: Y-m-01)
     * 
     * @return static
     * @todo review this
     */
    public function period() {
        if($this->next && $this->exists()) {
            try {
                $dt = new DateTime($this->data[$this->currentfield]);
                $this->data[$this->currentfield] = $dt->format('Y-m-01');
            }
            catch(Exception $e) {
                $this->setErrorMessage($this->currentfield, '{field} is not a valid date string');
                $this->addErrorMessage($this->currentfield);
                $this->next = false;
            }
        }

        return $this;
    }



    /**
     * Check for valid U.S. phone number. Removes non-numeric characters and confirms length of string.
     * 
     * @return static 
     */
    public function phone() {
        // remove non-numeric characters from the string
        $pattern = '/[^0-9]/';

        $this->data[$this->currentfield] = preg_replace($pattern, '', $this->data[$this->currentfield]);

        // find length of resulting string
        $length = strlen($this->data[$this->currentfield]);

        // define valid string lengths
        $validlengths = array(7,10,11);

        if($this->next && $this->exists() && !in_array($length, $validlengths)) {
            $this->addErrorMessage('phone');
            $this->next = false;
        }

        return $this;
    }



    /**
     * Check against a regex pattern
     * 
     * @param string $pattern The regex pattern to match. 
     * @return static
     * @todo verify if this needs slashes, etc in parameter. I believe it does.
     */
    public function regex($pattern) {
        if($this->next && $this->exists()) {
            if(preg_match($pattern, $this->data[$this->currentfield]) == false) {
                $this->setErrorMessage('regex', '{field} does not match the defined pattern');
                $this->addErrorMessage('regex');
                $this->next = false;
            }
        }

        return $this;
    }



    /**
     * Check if the required value exists.
     * 
     * @return static 
     */
    public function required() {
        if(!$this->exists()) {
            $this->addErrorMessage('required');
            $this->next = false;
        }

        return $this;
    }



    /**
     * Check for valid U.S. state abbreviation.
     * 
     * @return static 
     */
    public function state() {
        // list of 51 valid states + D.C. (?)
		$states = array(
			'AL','AK','AZ','AR','CA','CO','CT','DE','DC','FL',
			'GA','HI','ID','IL','IN','IA','KS','KY','LA','ME',
			'MD','MA','MI','MN','MS','MO','MT','NE','NV','NH',
			'NJ','NM','NY','NC','ND','OH','OK','OR','PA','RI',
			'SC','SD','TN','TX','UT','VT','VA','WA','WV','WI','WY');
        
        if($this->next && $this->exists() && !in_array($this->data[$this->currentfield], $states, true)) {
            $this->addErrorMessage('state');
            $this->next = false;
        }

        return $this;
    }


    /**
     * Check for valid U.S. zip code.
     * 
     * @return static 
     */
    public function zip() {
        $pattern = '/^[0-9]{5}(?:-[0-9]{4})?$/';

        if($this->next && $this->exists() && !preg_match($pattern, $this->data[$this->currentfield])) {
            $this->addErrorMessage('zip');
            $this->next = false;
        }

        return $this;
    }





    /**
     * Check to see if all validations are successful.
     * 
     * @return bool 
     */
    public function isValid() {
        if(empty($this->errors)) {
            return true;
        }
        else {
            return false;
        }
    }



    /**
     * Display the entire object for debugging purposes.
     * 
     * @return string 
     */
    public function showObject() {
        echo '<pre>';
        print_r($this);
        echo '</pre>';
    }


} // end class

?>