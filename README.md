# Validator Class
This is a simple validator class. It requires no dependencies. It is based on `https://github.com/devwithkunal/php-validator-class`. Easily validate common data types. If a dataset is not specified, the class imports POST or GET data to validate.




## Basic Use

Create the class instance. The constructor is the associative data array to be validated. If the `$data` is not specified, the class attempts to pull in POST data, then GET data.

```
$v = new Validator($data);
```

Run validation checks by chaining methods to the `field()` method. The `field()` method must start every method chain.

Check if the data is valid using the `isValid()` method.

```
if($v->isValid() == false) {
    print_r($v->errors);
}
```



## Basic Methods

To start validation of a field, use the `field()` method. Once all validations are performed, use the `isValid()` method to determine if they were successful.

|Method|Description|
|------|-----------|
|`field($name, $alias)` |Set the field name to start validation.<br />`$name` - Name of the field/key to validate.<br />`$alias` - Alias to use in error messages instead of the field name.|
|`isValid()`            |Check if all validations are successful.|



## Validation methods available

After the `field()` method is called for a field, you can chain validation methods for each field.

|Method|Description|
|------|-----------|
|`alpha($ignore)`                    |Check for alphabetic characters.|
|`alphanumeric($ignore)`             |Check for alphanumeric characters.|
|`changecase($case)`                 |Change case of string: capitalize, uppercase, lowercase.|
|`contains($chars)`                  |Check if the value contains specific characters.|
|`date()`                            |Check for a valid date.|
|`dateafter($date)`                  |Check if a date comes after the given date.|
|`datebefore($date)`                 |Check if a date comes before the given date.|
|`email()`                           |Check for email address.|
|`enum($list)`                       |Check if a value is in the list of approved values.|
|`equals($value)`                    |Check if value is equal to a given value.|
|`integer()`                         |Check for integer.|
|`length($length)`                   |Check for exact length of string.|
|`maxlength($length)`                |Check for maximum length of string.|
|`maxvalue($value)`                  |Check for maximum value of integer or float.|
|`minlength($length)`                |Check for minimum length of string.|
|`minvalue($value)`                  |Check for minimum value of integer or float.|
|`money($ignore)`                    |Check for currency. Changes string to decimal by removing dollar signs, commas, decimals, and negative signs.|
|`numeric()`                         |Check for numeric characters.|
|`period()`                          |Check for a date string and convert it to a period (Ex: Y-m-01).|
|`phone()`                           |Check for valid U.S. phone number. Removes non-numeric characters and confirms length of string.|
|`regex($pattern)`                   |Check against a regex pattern.|
|`required()`                        |Check if the required value exists.|
|`state()`                           |Check for valid U.S. state abbreviation.|
|`zip()`                             |Check for valid U.S. zip code.|




## Example
```
require('Validator.php');

use Hypnokizer\Validator;

// test data to validate
$data = array(
    'fname' => 'nathan randall',
    'lname' => 'Kizer',
    'username' => 'hypnokizer1729!',
    'emailaddy' => 'nathan.kizer@test.com',
    'password' => 'P@ssword!!',
    'password-confirm' => 'mypassword2',
    'age' => 50,
    'sex' => 'male',
    'state' => 'WY',
    'zip' => '79407-3711',
    'phone' => '806-441-8282',
    'dob' => '01/02/1976',
    'month' => 'Jan2026',
    'payment' => '-$4,226.95'
);

$v = new Validator($data);

$v->field('fname')->required()->changecase('capitalize');
$v->field('lname', 'Last name')->required()->length(4);
$v->field('username')->required()->regex('/^[A-Za-z0-9]+$/');
$v->field('emailaddy')->required()->email();
$v->field('age')->minvalue(45)->maxvalue(55)->integer();
$v->field('sex')->enum(['male', 'female']);
$v->field('state')->required()->state();
$v->field('zip')->required()->zip();
$v->field('phone')->required()->phone();
$v->field('password')->minlength(10)->maxlength(15)->contains('a-z')->contains('@')->contains('A-Z');
$v->field('password-confirm')->required()->equals($data['password']);
$v->field('dob')->required()->date()->dateafter('January 2, 1976')->datebefore('January 3, 1976');
$v->field('month')->required()->period();
$v->field('payment')->required()->money();

// custom error; validation takes place in if() statement
if(1 == 2) {
    $v->field('mycustom')->customError('{field} is incorrect');
}


if($v->isValid() == false) {
    echo '<pre>ERRORS FOUND:';
    print_r($v->errors);
    echo '</pre>';
}
```
