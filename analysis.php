#!/usr/bin/env php
<?php
//echo PHP_EOL, file_get_contents('Bradesco_02082015_222633.csv'), PHP_EOL;
$file_resource = fopen('Bradesco_02082015_222633.csv', 'r');

$data = [];
$data_count = 0;

$was_last_line_an_operation = false;

while($line = str_replace(PHP_EOL, '', fgets($file_resource))) {
	if (utf8_encode($line) === 'Últimos Lançamentos') break;

	if (preg_match('/^\d{2}\/\d{2}\/\d{2};/', $line)) {
		$data[] = parseOperationLine($line);
		$data_count++;
		$was_last_line_an_operation = true;
	} elseif ($line[0] === ';' && $was_last_line_an_operation) {
		parseOperationDescriptionLine($data[$data_count-1], $line);
		$data[$data_count-1]->line .= $line;
		$was_last_line_an_operation = false;
	}
}
var_dump($data);

function parseOperationLine($line) {
	$operation = new BradescoOperationDataObject;

	$operation->line = $line;

	$data = explode(';', $line);

	if ($data[1] === 'SALDO ANTERIOR')
		return $line;

	try {
		$operation
			->setOperationDate(DateTime::createFromFormat('d/m/y', $data[0]))
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

function parseOperationDescriptionLine(BradescoOperationDataObject &$object, $line) {
	$object->setDescription(substr($line, 1, strpos($line, ';', 1) - 1));
}

class BradescoOperationDataObject {
	public $line;

	private
		$operation_date,
		$operation_identifier_string,
		$operation_code,
		$operation_value,
		$remaining_balance,
		$description,
		$operation_type;

	public function setOperationDate(DateTime $date) {
		$this->operation_date = $date;

		return $this;
	}

	public function getOperationDate($format = null) {
		if (is_null($format)) {
			return $this->operation_date;
		} else {
			return $this->operation_date->format($format);
		}
	}

	public function setOperationIdentifierString($string) {
		if (!is_string($string))
			throw new InvalidArgumentException("Invalid operation identifier string $string");

		$this->operation_identifier_string = $string;

		return $this;
	}

	public function getOperationIdentifierString() {
		return $this->operation_identifier_string;
	}

	public function setOperationCode($code) {
		if (!is_numeric($code))
			throw new InvalidArgumentException("Invalid operation code $code");

		$this->operation_code = (int) $code;

		return $this;
	}

	public function getOperationCode() {
		return $this->operation_code;
	}

	public function setRemainingBalance($remaining_balance) {
		if (!empty($value) && !is_numeric($remaining_balance))
			throw new InvalidArgumentException("Invalid operation remaining balance $remaining_balance");

		$this->remaining_balance = (float) $remaining_balance;

		return $this;
	}

	public function getRemainingBalance() {
		return $this->remaining_balance;
	}

	public function setOperationValue($value) {
		if (!is_numeric($value))
			throw new InvalidArgumentException("Invalid operation value $value");

		$this->operation_value = (float) $value;

		return $this;
	}

	public function getOperationValue() {
		return $this->operation_value;
	}

	public function setDescription($description) {
		if (!is_string($description))
			throw new InvalidArgumentException("Invalid operation description $description");

		$this->description = $description;

		return $this;
	}

	public function getDescription() {
		return $this->description;
	}
}
