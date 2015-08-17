<?php

namespace FinancialAnalysis\BankStatementReader\Itau;

class Reader implements \FinancialAnalysis\BankStatementReader\ReaderInterface {
	private $document_year;

	public function readFromFile($path_to_file, array &$collection) {
		$file_resource = fopen($path_to_file, 'r');

		while ($line = str_replace(PHP_EOL, '', fgets($file_resource))) {
			$matches = [];
			if (preg_match('/^Data: [A-Za-z]+\/(\d{4})/', $line, $matches)) {
				$this->document_year = $matches[1];
			} elseif (preg_match('/^;\d{2}\/\d{2};;/', $line)) {
				if (($operation = $this->parseOperationLine($line)) !== null) {
					$collection[] = $operation;
				}
			}
		}
	}

	private function parseOperationLine($line) {
		$operation = new OperationDataObject;

		$operation->line = $line;

		$data = explode(';', $line);

		// Unimportant, just reports bank balance at the date
		if (strpos($data[3], 'SALDO') !== false)
			return null;

		$operation
			->setOperationDate(\DateTime::createFromFormat('d/m/Y', $data[1].'/'.$this->document_year))
			->setOperationIdentifierString($data[3])
			->setOperationValue(str_replace(['"', '.', ','], ['', '', '.'], $data[5]))
			->setRemainingBalance(str_replace(['"', '.', ','], ['', '', '.'], $data[7]));

		return $operation;
	}
}
