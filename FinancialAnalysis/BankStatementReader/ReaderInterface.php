<?php

namespace FinancialAnalysis\BankStatementReader;

interface ReaderInterface {
	public function readFromFile($path_to_file, array &$collection);
}