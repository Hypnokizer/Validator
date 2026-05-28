@TODO rework this file

# Validator Class
This is a simple validator class. It requires no dependencies. It is based on `https://github.com/devwithkunal/php-validator-class`.




## Basic Use

Create the class instance. The constructor is the associative data array to be validated.

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



## Example
```
require('Validator.php');

use App\Controllers\Validator;

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


if($v->isValid() == false) {
    echo '<pre>ERRORS FOUND:';
    print_r($v->errors);
    echo '</pre>';
}
```


## List of Methods

[list of methods and descriptions, checks]





## Methods
Some methods to use
| Methods | Return | Description |
|--------|--------|-------------|
| `field(str $name, str? $alias)` | $this | Set the field name to start validation. <br/> param *string* `$name` - Name of the field/key as on data to validate. <br/> param *string* `$alias` - (optional) Alias use on error messages instead of field name. |
| `set_response_messages(arr $messages)` | void | Function to set/extend custom error. <br /> Use associative array of messages as the parameter. See the messages format on `Validator.php` file at line `20`.
| `is_valid()` | boolean | Check if all validations are successfull.

Here is a list of the validators currently available.

| Validator | Description |
| ----------|-------------|
| `required()` | Check if the value exists. |
| `alpha(arr $ignore)` | Check if the value is alpha only. <br/> param *array* `$ignore` - (optional) add charectors to allow. Ex. ['@', ' '] |
| `alpha_num()` | Check if the value is alpha numeric only. <br/> param *array* `$ignore` - (optional) add charectors to allow. Ex. ['@', ' '] |
| `numeric()` | Check if the value is numeric only. |
| `email()` | Check if the value is a valid email. |
| `max_len(int $size)` | Check if length of the value is larger than the limit. <br/> param *int* `$size` - Max length of charectors of the value. |
| `min_len(int $size)` | Check if length of the value is smaller than the limit. <br/> param *int* `$size` - Min length of charectors of the value. |
| `max_val(int $val)` | Check if the value of intiger/number is not larger than the limit. <br/> param *int* `$val` - Max value of the number. |
| `min_val(int $val)` | Check if the value of intiger/number is not smaller than the limit. <br/> param *int* `$val` - Min value of the number. |
| `enum(arr $list)` | Check if the value is in the list. <br/>  param *array* `$list` - List of valid values. |
| `equals(mix $value)` | Check if the value is equal. <br/> param *mixed* `$value` - Value to match equal. |
| `date(string $date)` | Check if the value is a valid date. <br/> param *string* `$format` - Format of the date. (ex. Y-m-d) Check out [PHP Manual](https://www.php.net/manual/en/datetime.format.php) for more. |
| `date_after(string $date)` | Check if the date appeared after the specified date. <br/> param *string* `$date` - Use format Y-m-d (ex. 2023-01-15). |
| `date_before(string $date)` | Check if the date appeared before the specified date. <br/> param *string* `$date` - Use format Y-m-d (ex. 2023-01-15). |
| `must_contain(str $chars)` | Check if the value must contains some charectors. <br/> param *string* `$chars` - Set of chars in one string. Ex. "@#&abc123"|
| `match(str $pattern)` | Check if the value matchs a pattern. <br/> param *string* `$patarn` - Rejex pattern to match. |

