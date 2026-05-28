<?php

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

// validate data 
$v->field('fname')->required()->changecase('capitalize');
$v->field('lname', 'Last name')->required()->length(4);

$v->field('username')->required()->regex('/^[A-Za-z0-9]+$/');

$v->field('emailaddy')->required()->email();
$v->field('age')->minvalue(45)->maxvalue(55)->integer();
$v->field('sex')->enum(['male', 'female']);

$v->field('state')->required()->state();
$v->field('zip')->required()->zip();
$v->field('phone')->required()->phone();


// password: minlength, maxlength, contains, contains
// $v->field('password')->required()->contains('@123');
$v->field('password')->minlength(10)->maxlength(15)->contains('a-z')->contains('@')->contains('A-Z');



$v->field('password-confirm')->required()->equals($data['password']);

$v->field('dob')->required()->date()->dateafter('January 2, 1976')->datebefore('January 3, 1976');

$v->field('month')->required()->period();

$v->field('payment')->required()->money();

$v->showObject();


if($v->isValid() == false) {
    echo '<pre>ERRORS FOUND:';
    print_r($v->errors);
    echo '</pre>';
}


?>