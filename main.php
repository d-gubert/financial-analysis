#!/usr/bin/env php
<?php
require 'autoloader.php';

class Main {
	private $collection = [];

	public function run(array $filenames) {
		$this->readFiles($filenames);
		var_dump($this->collection);
		$this->groupByMonth();
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

	private function groupByMonth() {

	}
}

$app = new Main;

$app->run(glob('data/*.csv'));