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
    'payment' => '-$4,226.95',
    'mycustom' => 'asdf'
);

$v = new Validator($data);


// validation happens in if() statement...
// @TODO how does this work practically? find example in my code...
if(1 == 1) {
    $v->field('mycustom')->customError('{field} equals 1 equals 1');
}

$v->field('mycustom');
$v->field('fname')->required()->changecase('capitalize');
$v->field('lname', 'Last name')->required();
$v->field('username')->required()->regex('/^[A-Za-z0-9!]+$/');
$v->field('emailaddy')->required()->email();
$v->field('age')->minvalue(45)->maxvalue(55)->integer();
$v->field('sex')->enum(['male', 'female']);
$v->field('state')->required()->state();
$v->field('zip')->required()->zip();
$v->field('phone')->required()->phone();
$v->field('password')->minlength(10)->maxlength(15)->contains('a-z')->contains('@')->contains('A-Z');
// $v->field('password-confirm')->required()->equals($data['password']);
$v->field('dob')->required()->date()->dateafter('January 2, 1976')->datebefore('January 3, 1976');
$v->field('month')->required()->period();
$v->field('payment')->required()->money();


if($v->isValid() == false) {
    echo '<pre>ERRORS FOUND:';
    print_r($v->errors);
    echo '</pre>';
}
else {
    echo 'it is all validated';
}

$v->showObject();



?>