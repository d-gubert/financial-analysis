(function() {
	'use strict';

	class NuBankExtractor {
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
			return new Promise((function(self) {return function(resolve, reject) {
				self.getDataFrom('https://prod-customers.nubank.com.br/api/customers', {resolve: resolve, reject: reject});
			}})(this));
		}

		requestEventList(customer) {
			console.log(customer);
			return new Promise((function(self, customerId) {return function(resolve, reject) {
				self.getDataFrom('https://prod-notification.nubank.com.br/api/contacts/'+customerId+'/feed', {resolve: resolve, reject: reject});
			}})(this, customer.id));
		}

		processEventList(eventList) {
			let transactions = new Set();

			for ($event of eventList) {
				console.log($event);
			}
			// console.log(eventList);
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
