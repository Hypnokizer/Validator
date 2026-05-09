<?php 

namespace App\Controllers;

use DateTime;
use Exception;

class Validator {

    /**
     * data to validate
     * @access protected;
     * @var array
     */
    protected $data;

    /**
     * currently selected key/field to validate data
     * @access protected
     * @var string
     */
    protected $currentfield;

    /**
     * alias to use in error messages instead of field name
     * @access protected
     * @var string
     */
    protected $currentalias;

    /**
     * error message responses. you can change messages here. "{field}" refers to the field name
     * @access protected
     * @var array
     */
    protected $responses;
    
    /**
     * error messages generated after the validation of each field
     * @access public
     * @var array
     */
    public $errors;

    /**
     * check to see if the next validation on the field should run or not
     * @access protected
     * @var bool
     */
    protected $next;


    /**
     * create new instance of validator class
     * @param array $data data to validate
     * @return object Validator
     */
    public function __CONSTRUCT($data) {
        $this->data = $data;
        $this->currentfield = NULL;
        $this->currentalias = NULL;

        // @TODO auto pull POST or GET if DATA not given?
        // @TODO basic escaping?
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
     * @TODO what does $others array do?
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
     * check to see if the current field or field value exists. used in most validation check.
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
     * method to set custom error response within methods
     * @param string $field field name for error message
     * @param string $message body of the error message
     */
    protected function setErrorMessage($field, $message) {
        $this->responses[$field] = $message;
    }



    /**
     * set the field name to start the validation
     * @param string $name name of the field/key as on data to validate
     * @param string $alias optional alias to use on error messages instead of field name
     */
    public function field($name, $alias = NULL) {
        $this->currentfield = $name;
        $this->next = true;
        $this->currentalias = $alias;
        return $this;
    }





    // @TODO create a null method to convert empty strings to NULL? watch for zero values showing as empty...


    /**
     * check for alphabetic characters
     * @param array $ignore optional characters to allow, including whitespace
     * @return this
     */
    public function alpha($ignore = array()) {
        if($this->next && $this->exists() && !ctype_alpha(str_replace($ignore, '', $this->data[$this->currentfield]))) {
            $this->addErrorMessage('alpha');
            $this->next = false;
        }

        return $this;
    }


    /**
     * check for alphanumeric characters
     * @param array $ignore optional characters to allow, including whitespace
     */
    public function alphanumeric($ignore = array()) {
        if($this->next && $this->exists() && !ctype_alnum(str_replace($ignore, '', $this->data[$this->currentfield]))) {
            $this->addErrorMessage('alphanumeric');
            $this->next = false;
        }

        return $this;
    }



    /**
     * changes case of string: capitalize, uppercase, lowercase
     * @param string $case transformation to perform on text string
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
     * check if the value contains characters
     * @param string $chars set of characters to search for in one string (Ex: '@#abc123')
     * @return this
     * @TODO review the preg_match
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
     * check if a valid date
     * @return this
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
     * check if date comes after a given date
     * 
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
     * check if date comes before a given date
     * @TODO check same date comparisons
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
     * check for valid email address
     */
    public function email() {
        if($this->next && $this->exists() && !filter_var($this->data[$this->currentfield], FILTER_VALIDATE_EMAIL)) {
            $this->addErrorMessage('email');
            $this->next = false;
        }

        return $this;
    }


    /**
     * check if a value is in the list of approved values
     * @param array $list list of valid values
     * @return $this
     */
    public function enum($list) {
        if($this->next && $this->exists() && !in_array($this->data[$this->currentfield], $list)) {
            $this->addErrorMessage('enum');
            $this->next = false;
        }

        return $this;
    }



    /**
     * check if the value is equal
     * @param mixed $value value to match
     * @return this
     */
    public function equals($value) {
        if($this->next && $this->exists() && $this->data[$this->currentfield] !== $value) {
            $this->addErrorMessage('equals');
            $this->next = false;
        }

        return $this;
    }


    /**
     * checks that value is an integer
     * @return this
     */
    public function integer() {
        if($this->next && $this->exists() && filter_var($this->data[$this->currentfield], FILTER_VALIDATE_INT) === false) {
            $this->addErrorMessage('integer');
            $this->next = false;
        }

        return $this;
    }


    /**
     * checks string for exact length
     * @param int $length length of string
     * @return this
     */
    public function length($length) {
        if($this->next && $this->exists() && strlen($this->data[$this->currentfield]) !== $length) {
            $this->addErrorMessage('length', ['length' => $length]);
            $this->next = false;
        }

        return $this;
    }


    /**
     * check for maximum length of a string
     */
    public function maxlength($length) {
        if($this->next && $this->exists() && strlen($this->data[$this->currentfield]) > $length) {
            $this->addErrorMessage('maxlength');
            $this->next = false;
        }

        return $this;
    }


    /**
     * check if the value of an integer/number is not larger than the limit
     * @param int $value maximum value of the number
     * @see numeric()
     */
    public function maxvalue($value) {
        if($this->next && $this->exists() && $this->data[$this->currentfield] > $value) {
            $this->addErrorMessage('maxvalue');
            $this->next = false;
        }

        return $this;
    }


    /**
     * check for minimum length of a string
     */
    public function minlength($length) {
        if($this->next && $this->exists() && strlen($this->data[$this->currentfield]) < $length) {
            $this->addErrorMessage('minlength');
            $this->next = false;
        }

        return $this;
    }


    /**
     * check if the value of an integer/number is not smaller than the limit
     * @param int $value minimum value of the number
     * @see numeric()
     */
    public function minvalue($value) {
        if($this->next && $this->exists() && $this->data[$this->currentfield] < $value) {
            $this->addErrorMessage('minvalue');
            $this->next = false;
        }

        return $this;
    }


    /**
     * changes string to decimal by removing dollar signs, commas, decimals, and negative signs
     * @TODO review
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
     * check for numeric values
     */
    public function numeric() {
        if($this->next && $this->exists() && !is_numeric($this->data[$this->currentfield])) {
            $this->addErrorMessage('numeric');
            $this->next = false;
        }

        return $this;
    }


    /**
     * changes date string to a period (Ex: Y-m-01)
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
     * check for valid U.S. phone number
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
     * check against a regex pattern
     * @param string $pattern pattern to match
     * @return this
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
     * check if the required value exists
     */
    public function required() {
        if(!$this->exists()) {
            $this->addErrorMessage('required');
            $this->next = false;
        }

        return $this;
    }


    /**
     * check if a valid U.S. state abbreviation
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
     * check for a valid zip code
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
     * check to see if all validations are successful
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
     * show the object for debugging
     */
    public function showObject() {
        echo '<pre>';
        print_r($this);
        echo '</pre>';
    }


} // end class


?>