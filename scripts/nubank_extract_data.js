(function() {
	'use strict';

	class NuBankExtractor {
		openXhr: function(link, method) {
			let xhr = new XMLHttpRequest;

			if (method === undefined) method = 'GET';

			xhr.open(method, link, true);

			xhr.setRequestHeader('Authorization', "Bearer eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCIsImtpZCI6IjIwMTUtMTItMDRUMTc6MzY6MjIuNjY0LXU5ZC1ldWN1Ri1zQUFBRlJiaER3aUEifQ.eyJpc3MiOiJodHRwczpcL1wvd3d3Lm51YmFuay5jb20uYnIiLCJhdWQiOiJvdGhlci5sZWdhY3kiLCJzdWIiOiI1NWRkMTljMC01YzY2LTRmOTEtYTgwNy01OWI3OTBlMWI1OWMiLCJleHAiOjE0NTU2NDE0NjQsInNjb3BlIjoiYXV0aFwvdXNlciB1c2VyIiwidmVyc2lvbiI6IjIiLCJpYXQiOjE0NTUwMzY2NjQsImp0aSI6Img4S2d2Rm1ZaXgwQUFBRlN4dkdMcmcifQ.JxkOSltRPMGUN-xMSH0qnIVYrJAw9w6nJ07WLlfXOC8KnmkxNnenRPnm1Oi_T4MWGPCI_GZAiTDrB-AJ6-ZgMuHiL5emsX4-a_nxO-xTUirs3C-qOhvoROhRneAYq6U0gTTxTq54OROn-KeJ-Dqw8dunWhTv-V05vYE1wSFEsYA-lzYUKGEgEox2ugMIMyZGRJ8zNIwC2qx7EkwMesQeRdOiDGRWfPxgOnIutpWBcAQWlctEMK0blYDYgEr759cDm4Gz9nsRyfEBRr4v8q64GDKL1AOAiEeyHlxz74_eHjeXnt1zDk1RtX1wZAqJWCDOX38lhGEtz5jyQdvZPWg3QQ");
		},

		requestCustomerId: function() {
			return new Promise(function(resolve, reject) {
				let xhr = this.openXhr('https://prod-customers.nubank.com.br/api/customers', {resolve: resolve, reject: reject});

				xhr.onload = function() {
					if (xhr.status === 200 && xhr.response.length > 0) {
						resolve(JSON.parse(xhr.response));
					} else {
						reject('Unexpected error');
					}
				}

				xhr.onerror = function() {
					reject('Network error');
				}
			});
		},

		requestEventList: function(customer) {
			return new Promise(function(resolve, reject) {
				let xhr = this.openXhr('https://prod-notification.nubank.com.br/api/contacts/'+customer.id+'/feed');
			});
		},

		run: function() {
			this.requestCustomerId().then(this.requestEventList)
		}
	};

	let csv = '';
	let xhr = new XMLHttpRequest;

	// xhr.open('GET', 'https://prod-notification.nubank.com.br/api/contacts/55dd19c0-5c66-4f91-a807-59b790e1b59c/feed', true);
	xhr.open('GET', 'https://prod-customers.nubank.com.br/api/customers', true);

	xhr.onload = function() {
		console.log(xhr);
		// let transactions = JSON.parse(xhr.response);
	}

	xhr.send();
})();
