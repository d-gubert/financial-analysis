<?php

namespace FinancialAnalysis\BankStatementReader;

abstract class AbstractBankOperation {
	private
		$operation_date,
		$operation_value,
		$category;


	public function setOperationDate(\DateTime $date) {
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

	public function setOperationValue($value) {
		if (!is_numeric($value))
			throw new InvalidArgumentException("Invalid operation value $value");

		$this->operation_value = (float) $value;

		return $this;
	}

	public function getOperationValue() {
		return $this->operation_value;
	}
}