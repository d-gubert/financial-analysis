(function() {
	'use strict';

	class NuBankExtractor {
		constructor() {
			this.transactionsByDate = new Map();
			this.maxDate = new Date();
		}
		
		getDataFrom(link, promiseMethods, method) {
			let xhr = new XMLHttpRequest;

			if (method === undefined) method = 'GET';

			xhr.open(method, link, true);

			xhr.setRequestHeader('Authorization', "Bearer eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCIsImtpZCI6IjIwMTUtMTItMDRUMTc6MzY6MjIuNjY0LXU5ZC1ldWN1Ri1zQUFBRlJiaER3aUEifQ.eyJpc3MiOiJodHRwczpcL1wvd3d3Lm51YmFuay5jb20uYnIiLCJhdWQiOiJvdGhlci5sZWdhY3kiLCJzdWIiOiI1NWRkMTljMC01YzY2LTRmOTEtYTgwNy01OWI3OTBlMWI1OWMiLCJleHAiOjE0NTU2NDE0NjQsInNjb3BlIjoiYXV0aFwvdXNlciB1c2VyIiwidmVyc2lvbiI6IjIiLCJpYXQiOjE0NTUwMzY2NjQsImp0aSI6Img4S2d2Rm1ZaXgwQUFBRlN4dkdMcmcifQ.JxkOSltRPMGUN-xMSH0qnIVYrJAw9w6nJ07WLlfXOC8KnmkxNnenRPnm1Oi_T4MWGPCI_GZAiTDrB-AJ6-ZgMuHiL5emsX4-a_nxO-xTUirs3C-qOhvoROhRneAYq6U0gTTxTq54OROn-KeJ-Dqw8dunWhTv-V05vYE1wSFEsYA-lzYUKGEgEox2ugMIMyZGRJ8zNIwC2qx7EkwMesQeRdOiDGRWfPxgOnIutpWBcAQWlctEMK0blYDYgEr759cDm4Gz9nsRyfEBRr4v8q64GDKL1AOAiEeyHlxz74_eHjeXnt1zDk1RtX1wZAqJWCDOX38lhGEtz5jyQdvZPWg3QQ");

			if (typeof promiseMethods === 'object' && typeof promiseMethods.resolve === 'function' && typeof promiseMethods.reject === 'function') {
				xhr.onload = function() {
					if (xhr.status === 200 && xhr.response.length > 0) {
						promiseMethods.resolve(JSON.parse(xhr.response));
					} else {
						promiseMethods.reject('Unexpected error');
					}
				}

				xhr.onerror = function() {
					promiseMethods.reject('Network error');
				}
			}

			xhr.send();

			return xhr;
		}

		requestCustomerId() {
			console.info("Querying customer...");
			return new Promise((function(self) {return function(resolve, reject) {
				self.getDataFrom('https://prod-customers.nubank.com.br/api/customers', {resolve: resolve, reject: reject});
			}})(this));
		}

		requestEventList(customer) {
			console.info("Querying events...");
			return new Promise((function(self, customerId) {return function(resolve, reject) {
				self.getDataFrom('https://prod-notification.nubank.com.br/api/contacts/'+customerId+'/feed', {resolve: resolve, reject: reject});
			}})(this, customer.id));
		}
		
		ensureTransactionDateExists(date) {
			if (!this.transactionsByDate.has(date.getTime())) this.transactionsByDate.set(date.getTime(), []);
		}

		processEventList(eventList) {
			console.info("Processing events...");
			for (var event of eventList) {
				if (event.category !== 'transaction') continue;
				
				let transactionDate = new Date(event.time.substring(0,10)); //Gets only the date portion
				
				if (event.details.charges === undefined) {
					this.processSinglePurchase(event, transactionDate);
				} else {
					this.processInstallmentPurchase(event, transactionDate);
				}
			}
			
			console.log(this.transactionsByDate);
			
			this.generateReport();
		}
		
		processInstallmentPurchase(transaction, transactionDate) {
			transaction.amount = transaction.details.charges.amount;
			
			for (let i = 1, installmentDate = new Date(transactionDate), transactionDescription = transaction.description; 
			     i <= transaction.details.charges.count; i++) {
				installmentDate.setMonth(transactionDate.getMonth() + i - 1);
				
				if (installmentDate > this.maxDate) break;
				
				transaction.description = transactionDescription + " (Installment #" + i + ")";
				
				this.processSinglePurchase(transaction, installmentDate);
			}
		}
		
		processSinglePurchase(transaction, transactionDate) {
			this.ensureTransactionDateExists(transactionDate);
			
			this.transactionsByDate.get(transactionDate.getTime()).push({
				description: transaction.description,
				value: transaction.amount / 100,
				category: transaction.title,
				datetime: new Date(transaction.time),
				nubankId: transaction.id
			});	
		}
		
		generateReport() {
			let report = 'description,value,category,datetime,nubankId';
			
			this.transactionsByDate.forEach(function(transactionList) {
				for (let transaction of transactionList) {
					report += "\n" + 
					          transaction.nubankId + "," +
					          '"' + transaction.description + "\"," +
					          transaction.value + "," +
					          transaction.category + "," +
					          transaction.datetime.toLocaleString();
				}
			});
			
			this.downloadReport(report);
		}
		
		downloadReport(report) {
			let anchor = document.createElement('a');
			
			anchor.setAttribute("href", "data:attachment/csv," + encodeURIComponent(report));
			anchor.setAttribute("download", "extrato_nubank_" + (new Date()).toLocaleDateString().replace(/\//g,'-') + ".csv");

			anchor.click();
		}

		run() {
			this.requestCustomerId().then((function(self) {
				return function(response) {
					return self.requestEventList(response.customer);
				}
			})(this)).then((function(self) {
				return function(response) {
					return self.processEventList(response.events);
				}
			})(this));
		}
	};

	let app = new NuBankExtractor;

	app.run();
})();
