#!/usr/bin/env php
<?php
require 'autoloader.php';

class Main {
	private $collection = [];

	public function run(array $filenames) {
		$this->readFiles($filenames);
		var_dump(count($this->collection));
		$this->groupExpensesByMonth();
		$this->groupIncomeByMonth();
	}

	private function readFiles(array $filenames) {
		$bradesco = new FinancialAnalysis\BankStatementReader\Bradesco\Reader;
		$itau     = new FinancialAnalysis\BankStatementReader\Itau\Reader;

		foreach ($filenames as $file) {
			switch (true) {
				case (strpos(strtolower($file), 'itau') !== false):
					$itau->readFromFile($file, $this->collection);
					break;

				case (strpos(strtolower($file), 'bradesco') !== false):
					$bradesco->readFromFile($file, $this->collection);
					break;
			}
		}
	}

	private function groupExpensesByMonth() {
		$data = [];

		foreach ($this->collection as $operation) {
			list($month, $year) = explode('/', $operation->getOperationDate()->format('m/Y'));

			if (!isset($data[$year]))
				$data[$year] = [];

			if (!isset($data[$year][$month]))
				$data[$year][$month] = 0;

			$operation_value = $operation->getOperationValue();

			if ($operation_value < 0)
				$data[$year][$month] += $operation_value;
		}

		var_dump($data);
	}

	private function groupIncomeByMonth() {
		$data = [];

		foreach ($this->collection as $operation) {
			list($month, $year) = explode('/', $operation->getOperationDate()->format('m/Y'));

			if (!isset($data[$year]))
				$data[$year] = [];

			if (!isset($data[$year][$month]))
				$data[$year][$month] = 0;

			$operation_value = $operation->getOperationValue();

			if ($operation_value > 0)
				$data[$year][$month] += $operation_value;
		}

		var_dump($data);
	}
}

$app = new Main;

$app->run(glob('data/*.csv'));
// $app->run(array('data/ExtratoItauJulho.csv'));