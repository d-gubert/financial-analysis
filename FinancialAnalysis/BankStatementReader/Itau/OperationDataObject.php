<?php

namespace FinancialAnalysis\BankStatementReader\Itau;

class OperationDataObject extends \FinancialAnalysis\BankStatementReader\AbstractBankOperation {
	public $line;

	private
		$operation_identifier_string,
		$remaining_balance;

	public function setOperationIdentifierString($string) {
		if (!is_string($string))
			throw new \InvalidArgumentException("Invalid operation identifier string $string");

		$this->operation_identifier_string = $string;

		return $this;
	}

	public function getOperationIdentifierString() {
		return $this->operation_identifier_string;
	}

	public function setRemainingBalance($remaining_balance) {
		if (!empty($value) && !is_numeric($remaining_balance))
			throw new \InvalidArgumentException("Invalid operation remaining balance $remaining_balance");

		$this->remaining_balance = (float) $remaining_balance;

		return $this;
	}

	public function getRemainingBalance() {
		return $this->remaining_balance;
	}
}