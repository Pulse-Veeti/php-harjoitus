<?php

$name = "John Doe";
$age = 30;
$is_student = false;
$money = 19.99;
$salary = null;

echo "Name: " . $name . "\n";
echo "Hello {$name}, you are {$age} years old.\n";
echo $is_student."\n";
echo $money."\n";
echo $salary,"\n";

// Gettype
echo gettype(value: $name)."\n";
echo gettype($age)."\n";
echo gettype($is_student)."\n";
echo gettype($money)."\n";
echo gettype($salary)."\n";