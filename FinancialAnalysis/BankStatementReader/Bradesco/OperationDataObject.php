<?php

namespace FinancialAnalysis\BankStatementReader\Bradesco;

class OperationDataObject extends \FinancialAnalysis\BankStatementReader\AbstractBankOperation {
	public $line;

	private
		$operation_identifier_string,
		$operation_code,
		$operation_value,
		$remaining_balance,
		$description;

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