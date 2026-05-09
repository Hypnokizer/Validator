<?php

require('Validator.php');

use App\Controllers\Validator;

// test data to validate
$data = array(
    'fname' => 'nathan randall',
    'lname' => NULL,
    'username' => 'hypnokizer1729',
    'emailaddy' => 'nathan.kizer@test.com',
    'password' => 'mypassword',
    'password-confirm' => 'mypassword2',
    'age' => 50,
    'sex' => 'male',
    'state' => 'WY',
    'zip' => '79407-3711',
    'phone' => '806-441-8282',
    'dob' => '01/02/1976'
);

$v = new Validator($data);

// validate data 
$v->field('fname')->required()->changecase('capitalize');
$v->field('lname', 'Last name')->required();
$v->field('username')->required()->alphanumeric();
$v->field('emailaddy')->required()->email();
$v->field('age')->minvalue(45)->maxvalue(55);
$v->field('sex')->enum(['male', 'female']);

$v->field('state')->required()->state();
$v->field('zip')->required()->zip();
$v->field('phone')->required()->phone();

$v->field('password')->required();
$v->field('password-confirm')->required()->equals($data['password']);

$v->field('dob')->required()->date();

$v->showObject();


if($v->isValid()) {
    echo 'it is all valid';
}
else {
    echo '<pre>ERRORS FOUND:';
    print_r($v->errors);
    echo '</pre>';
}



// // Data to validate
// $data = [
//     "name" => "John Doe",
//     "age" => 25,
//     "email" => "john@example.com",
//     "password" => "pass@123",
//     "confirm_password" => "pass@123",
//     "sex" => "male",
//     "phone" => "1236547895",
//     "dob" => "1998-07-11"
// ];


// Run validation
// $v->field('password')->required()->min_len(8)->max_len(16)->must_contain('@#$&')->must_contain('a-z')->must_contain('A-Z')->must_contain('0-9');
// $v->field('confirm_password')->required()->equals($data['password']);
// $v->field('dob', 'date of birth')->date()->date_after('1998-01-01')->date_before('2002-12-31');

