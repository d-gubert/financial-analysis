<?php

namespace FinancialAnalysis\BankStatementReader;

abstract class AbstractBankOperation {
	const DEFAULT_CATEGORY = 'DEFAULT';

	protected
		$operation_date,
		$operation_value,
		$category;

	// Banks still don't offer this kind of information, so we need to
	// guess it from what we have. The bank operation object must handle
	// this information, not the caller
	final public function setCategory() {
		$this->parseCategory();

		if (empty($this->category))
			throw new \BadMethodCallException('Method parseCategory() should fill the $category property!');
	}

	// abstract protected function parseCategory();


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
			throw new \InvalidArgumentException("Invalid operation value $value");

		$this->operation_value = (float) $value;

		return $this;
	}

	public function getOperationValue() {
		return $this->operation_value;
	}
}