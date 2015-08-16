<?php

namespace FinancialAnalysis\BankStatementReader\Bradesco;

class Reader implements \FinancialAnalysis\BankStatementReader\ReaderInterface {
	public function readFromFile($path_to_file, array &$collection) {
		$file_resource = fopen($path_to_file, 'r');

		$data_count = count($collection);

		$was_last_line_an_operation = false;

		while($line = str_replace(PHP_EOL, '', fgets($file_resource))) {
			// Nothing interesting beyond that line
			if (utf8_encode($line) === 'Últimos Lançamentos') break;

			// Line starting with a date in the format dd/mm/yy
			if (preg_match('/^\d{2}\/\d{2}\/\d{2};/', $line)) {
				$collection[] = $this->parseOperationLine($line);
				$data_count++;
				$was_last_line_an_operation = true;
			} elseif ($line[0] === ';' && $was_last_line_an_operation) {
				$this->parseOperationDescriptionLine($collection[$data_count-1], $line);
				$collection[$data_count-1]->line .= $line;
				$was_last_line_an_operation = false;
			}
		}
	}

	private function parseOperationLine($line) {
		$operation = new OperationDataObject;

		$operation->line = $line;

		$data = explode(';', $line);

		if ($data[1] === 'SALDO ANTERIOR')
			return $line;

		try {
			$operation
				->setOperationDate(\DateTime::createFromFormat('d/m/y', $data[0]))
				->setOperationIdentifierString($data[1])
				->setOperationCode($data[2])
				->setOperationValue(str_replace(['"', '.', ','], ['', '', '.'], (empty($data[4]) ? $data[3] : $data[4])))
				->setRemainingBalance(str_replace(['"', '.', ','], ['', '', '.'], $data[5]));
		} catch (Exception $e) {
			var_dump($data, $line, $operation, $e->getMessage());
			var_dump(str_replace(['"', '.', ','], ['', '', '.'], (empty($data[4]) ? $data[3] : $data[4])),
					 is_numeric(str_replace(['"', '.', ','], ['', '', '.'], (empty($data[4]) ? $data[3] : $data[4])))
					);
			exit;
		}

		return $operation;
	}

	private function parseOperationDescriptionLine(OperationDataObject &$object, $line) {
		$object->setDescription(substr($line, 1, strpos($line, ';', 1) - 1));
	}
}