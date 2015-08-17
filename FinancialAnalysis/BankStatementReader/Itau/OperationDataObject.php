<?php

namespace FinancialAnalysis\BankStatementReader\Itau;

class OperationDataObject extends \FinancialAnalysis\BankStatementReader\AbstractBankOperation {
	private
		$remaining_balance;

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