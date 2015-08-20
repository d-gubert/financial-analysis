<?php

namespace FinancialAnalysis\BankStatementReader;

const CATEGORY_HEALTH;
const CATEGORY_WITHDRAWAL;
const CATEGORY_GROCERIES;

class OperationCategoryMapper {
	private $rules = [
		'zaffari' = CATEGORY_GROCERIES,
		'panvel'  = CATEGORY_HEALTH,
		'saque'   = CATEGORY_WITHDRAWAL
	];

	public function mapOperationIdentificationToCategory($identification) {
		foreach ($this->rules as $rule => $category) {
			if ($rule[0] === '/' && preg_match($rule, $identification)) {
				return $category;
			} elseif (strpos(strtolower($identification), $rule) !== false) {
				return $category;
			}
		}
	}
}