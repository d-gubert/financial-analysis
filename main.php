#!/usr/bin/env php
<?php
require 'autoloader.php';

$reader = new FinancialAnalysis\BankStatementReader\Itau\Reader;

$collection = [];

$reader->readFromFile('data/ExtratoItau.csv', $collection);

var_dump($collection);

/*
$reader = new FinancialAnalysis\BankStatementReader\Bradesco\Reader;

$collection = [];

$reader->readFromFile('data/Bradesco_02082015_222633.csv', $collection);

var_dump($collection);
*/