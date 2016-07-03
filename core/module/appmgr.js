(function () {
	"use strict";
	exports.AppMgr = function (db, converter, prefix, typeCrud, segmentCrud, trendCrud) {
		var me = this;
		this.isSetup = function (appid, callback) {
			me.countAction(appid, function (count) {
				callback(count > 0);
			});
		};

		this.traffic14 = function (appid, callback) {
			converter.toIDs(['_isUser', '_ctime'], function (ids) {
				var traffic = [];
				var rightnow = new Date().getTime() / 1000;
				var last14day = Math.round(rightnow) - 14 * 24 * 3600;
				var last13day = Math.round(rightnow) - 13 * 24 * 3600;
				var last12day = Math.round(rightnow) - 12 * 24 * 3600;
				var last11day = Math.round(rightnow) - 11 * 24 * 3600;
				var last10day = Math.round(rightnow) - 10 * 24 * 3600;
				var last9day = Math.round(rightnow) - 9 * 24 * 3600;
				var last8day = Math.round(rightnow) - 8 * 24 * 3600;
				var last7day = Math.round(rightnow) - 7 * 24 * 3600;
				var last6day = Math.round(rightnow) - 6 * 24 * 3600;
				var last5day = Math.round(rightnow) - 5 * 24 * 3600;
				var last4day = Math.round(rightnow) - 4 * 24 * 3600;
				var last3day = Math.round(rightnow) - 3 * 24 * 3600;
				var last2day = Math.round(rightnow) - 2 * 24 * 3600;
				var last1day = Math.round(rightnow) - 24 * 3600;
				var query = {};
				query[ids._ctime] = {$gte: last14day, $lt: last13day};
				query[ids._isUser] = {$exists: false};
				db.collection(prefix + "app" + appid).count(query, function (err, count) {
					if (err) throw err;
					traffic[13] = count;

					query[ids._ctime] = {$gte: last13day, $lt: last12day};
					query[ids._isUser] = {$exists: false};
					db.collection(prefix + "app" + appid).count(query, function (err, count) {
						if (err) throw err;
						traffic[12] = count;

						query[ids._ctime] = {$gte: last12day, $lt: last11day};
						query[ids._isUser] = {$exists: false};
						db.collection(prefix + "app" + appid).count(query, function (err, count) {
							if (err) throw err;
							traffic[11] = count;

							query[ids._ctime] = {$gte: last11day, $lt: last10day};
							query[ids._isUser] = {$exists: false};
							db.collection(prefix + "app" + appid).count(query, function (err, count) {
								if (err) throw err;
								traffic[10] = count;

								query[ids._ctime] = {$gte: last10day, $lt: last9day};
								query[ids._isUser] = {$exists: false};
								db.collection(prefix + "app" + appid).count(query, function (err, count) {
									if (err) throw err;
									traffic[9] = count;

									query[ids._ctime] = {$gte: last9day, $lt: last8day};
									query[ids._isUser] = {$exists: false};
									db.collection(prefix + "app" + appid).count(query, function (err, count) {
										if (err) throw err;
										traffic[8] = count;

										query[ids._ctime] = {$gte: last8day, $lt: last7day};
										query[ids._isUser] = {$exists: false};
										db.collection(prefix + "app" + appid).count(query, function (err, count) {
											if (err) throw err;
											traffic[7] = count;

											query[ids._ctime] = {$gte: last7day, $lt: last6day};
											query[ids._isUser] = {$exists: false};
											db.collection(prefix + "app" + appid).count(query, function (err, count) {
												if (err) throw err;
												traffic[6] = count;

												query[ids._ctime] = {$gte: last6day, $lt: last5day};
												query[ids._isUser] = {$exists: false};
												db.collection(prefix + "app" + appid).count(query, function (err, count) {
													if (err) throw err;
													traffic[5] = count;

													query[ids._ctime] = {$gte: last5day, $lt: last4day};
													query[ids._isUser] = {$exists: false};
													db.collection(prefix + "app" + appid).count(query, function (err, count) {
														if (err) throw err;
														traffic[4] = count;

														query[ids._ctime] = {$gte: last4day, $lt: last3day};
														query[ids._isUser] = {$exists: false};
														db.collection(prefix + "app" + appid).count(query, function (err, count) {
															if (err) throw err;
															traffic[3] = count;

															query[ids._ctime] = {$gte: last3day, $lt: last2day};
															query[ids._isUser] = {$exists: false};
															db.collection(prefix + "app" + appid).count(query, function (err, count) {
																if (err) throw err;
																traffic[2] = count;

																query[ids._ctime] = {$gte: last2day, $lt: last1day};
																query[ids._isUser] = {$exists: false};
																db.collection(prefix + "app" + appid).count(query, function (err, count) {
																	if (err) throw err;
																	traffic[1] = count;
																	query[ids._ctime] = {$gte: last1day};
																	query[ids._isUser] = {$exists: false};
																	db.collection(prefix + "app" + appid).count(query, function (err, count) {
																		if (err) throw err;
																		traffic[0] = count;
																		callback(traffic);
																	});
																});
															});
														});
													});
												});
											});
										});
									});
								});
							});
						});
					});
				});
			});
		};

		this.countAction = function (appid, callback) {
			converter.toIDs(['_isUser', '_ctime'], function (ids) {
				var query = {};
				var last30day = Math.round(new Date().getTime() / 1000) - 30 * 24 * 3600;
				query[ids._ctime] = {$gte: last30day};
				query[ids._isUser] = {$exists: false};
				db.collection(prefix + "app" + appid).count(query, function (err, count) {
					if (err) throw err;
					return callback(count);
				});
			});
		};

		this.initApp = function (appid, callback) {

			var purchase = {
				codename: "purchase",
				name: "Purchase",
				desc: "A purchase action",
				deftrendfields: [
					{pname: "Category", pcode: "cid"},
					{pname: "Product", pcode: "pid"},
					{pname: "Payment type", pcode: "paymentype"},
					{pname: "Campaign", pcode: "_utm_campaign"},
					{pname: "Campaign Source", pcode: "_utm_source"},
					{pname: "Campaign Medium", pcode: "_utm_medium"},
					{pname: "Campaign Term", pcode: "_utm_term"},
					{pname: "Campaign Content", pcode: "_utm_content"},
					{pname: "Operating System", pcode: "_os"},
					{pname: "City", pcode: "_city"},
					{pname: "Referer", pcode: "_ref"},
					{pname: "Screen Resolution", pcode: "_scr"},
					{pname: "Browser Version", pcode: "_browser"},
					{pname: "OS Version", pcode: "_osver"},
					{pname: "Location", pcode: "_location"}
				],
				deftrendobjects: [
					{desc: "Number of time purchased", operation: "count", param: "_id"},
					{desc: "Number of people purchased", operation: "count", param: "_mtid"},
					{desc: "Total ammount of purchasing", operation: "sum", param: "amount"}
				],
				fields: [
					{pname: "Product ID", pcode: "pid"},
					{pname: "Product Name", pcode: "pname"},
					{pname: "Category ID", pcode: "cid"},
					{pname: "Category Name", pcode: "cname"},
					{pname: "Price", pcode: "price"},
					{pname: "Amount", pcode: "amount"},
					{pname: "Quantity", pcode: "quantity"},
					{pname: "Payment Type", pcode: "paymentype"}]
			};

			var pageview =
			{
				codename: "pageview",
				name: "Pageview",
				desc: "Occurs when user visited site",
				fields: [],
				deftrendfields: [
					{pname: "Campaign", pcode: "_utm_campaign"},
					{pname: "Campaign Source", pcode: "_utm_source"},
					{pname: "Campaign Medium", pcode: "_utm_medium"},
					{pname: "Campaign Term", pcode: "_utm_term"},
					{pname: "Campaign Content", pcode: "_utm_content"},
					{pname: "URL", pcode: "_url"},
					{pname: "Operating System", pcode: "_os"},
					{pname: "Language", pcode: "_lang"},
					{pname: "City", pcode: "_city"},
					{pname: "Referer", pcode: "_ref"},
					{pname: "Screen Resolution", pcode: "_scrres"},
					{pname: "Browser Version", pcode: "_browser"},
					{pname: "OS Version", pcode: "_osver"},
					{pname: "Location", pcode: "_location"}
				],
				deftrendobjects: [
					{desc: "Number of pageview", operation: "count", param: "_id"},
					{desc: "Number of unique visitor", operation: "count", param: "_mtid"},
					{desc: "Time on page", operation: "sum", param: "timeonpage"}
				]
			};

			var click =
			{
				codename: "click",
				name: "Click",
				desc: "User click on a object",
				fields: [
					{pname: "Object ID", pcode: "oid"},
					{pname: "Object Type", pcode: "type"}
				],
				deftrendfields: [
					{pname: "Object ID", pcode: "oid"},
					{pname: "Object Type", pcode: "type"}
				],
				deftrendobjects: [
					{desc: "Number of time clicked", operation: "count", param: "_id"},
					{desc: "Number of unique people clicked", operation: "count", param: "_mtid"}
				]
			};

			var rate =
			{
				codename: "rate",
				name: "Rate",
				desc: "User give a rating",
				fields: [
					{pname: "Product ID", pcode: "pid"},
					{pname: "Product Name", pcode: "pname"},
					{pname: "Category ID", pcode: "cid"},
					{pname: "Category Name", pcode: "cname"},
					{pname: "Level", pcode: "level"}
				],
				deftrendfields: [
					{pname: "Product", pcode: "pid"}
				],
				deftrendobjects: [
					{desc: "Number of time rated", operation: "count", param: "_id"},
					{desc: "Number of unique people rated", operation: "count", param: "_mtid"}
				]
			};

			var like =
			{
				codename: "like",
				name: "Like",
				desc: "User like a product",
				fields: [
					{pname: "Product ID", pcode: "pid"},
					{pname: "Product Name", pcode: "pname"}
				]
			};

			var download =
			{
				codename: "download",
				name: "Download",
				desc: "User like a product",
				fields: [
					{pname: "Product ID", pcode: "pid"},
					{pname: "Product Name", pcode: "pname"},
					{pname: "Category ID", pcode: "cid"},
					{pname: "Category Name", pcode: "cname"}
				]
			};

			var register =
			{
				codename: "register",
				name: "Register",
				desc: "User register",
				fields: [
					{pname: "User ID", pcode: "userid"},	
				],
				deftrendfields: [
					{pname: "Campaign", pcode: "_utm_campaign"},
					{pname: "Campaign Source", pcode: "_utm_source"},
					{pname: "Campaign Medium", pcode: "_utm_medium"},
					{pname: "Campaign Term", pcode: "_utm_term"},
					{pname: "Campaign Content", pcode: "_utm_content"},
					{pname: "URL", pcode: "_url"},
					{pname: "Operating System", pcode: "_os"},
					{pname: "Language", pcode: "_lang"},
					{pname: "City", pcode: "_city"},
					{pname: "Referer", pcode: "_ref"},
					{pname: "Screen Resolution", pcode: "_scrres"},
					{pname: "Browser Version", pcode: "_browser"},
					{pname: "OS Version", pcode: "_osver"},
					{pname: "Location", pcode: "_location"}
				]
			};

			var search = {
				codename: "search",
				name: "Search",
				desc: "User search a keyword",
				fields: [
					{pname: "Keyword", pcode: "keyword"}
				]
			};

			var login =
			{
				codename: "login",
				name: "Login",
				desc: "User login to site",
				fields: [],
				deftrendfields: [
					{pname: "Campaign", pcode: "_utm_campaign"},
					{pname: "Campaign Source", pcode: "_utm_source"},
					{pname: "Campaign Medium", pcode: "_utm_medium"},
					{pname: "Campaign Term", pcode: "_utm_term"},
					{pname: "Campaign Content", pcode: "_utm_content"},
					{pname: "URL", pcode: "_url"},
					{pname: "Operating System", pcode: "_os"},
					{pname: "Language", pcode: "_lang"},
					{pname: "City", pcode: "_city"},
					{pname: "Referer", pcode: "_ref"},
					{pname: "Screen Resolution", pcode: "_scrres"},
					{pname: "Browser Version", pcode: "_browser"},
					{pname: "OS Version", pcode: "_osver"},
					{pname: "Location", pcode: "_location"}
				]
			};

			var trend1 = {
				name: "Top purchase",
				desc: "Top purchased product",
				typeid: "purchase",
				operation: "sum",
				object: "pid", //code of properties
				param: "price",
				order: 1 // small to large
			};

			var segment1 = {
				name: "All visitor",
				description: "All visitor in site",
				condition: [ "_os", 
                "con", 
                ""]
			};
			
			
			converter.toIDs(['_mtid', '_isUser', '_typeid'], function (ids) {
			var keys = {};
				keys[ids._mtid] = 1;
				db.collection(prefix + "app" + appid).createIndex(keys, function () {
					keys = {};
					keys[ids._isUser] = 1;
					db.collection(prefix + "app" + appid).createIndex(keys, { sparse: true }, function () {
						keys = {};
						keys[ids._isUser] = 1;
						keys[ids._typeid]  = 1;
						keys[ids._ctime] = 1;

						db.collection(prefix + "app" + appid).createIndex(keys,  function () {
							keys = {};
							keys[ids._isUser] = 1;
							keys[ids._ctime] = 1;

							db.collection(prefix + "app" + appid).createIndex(keys, function () {
								//console.log('googd');
							});
						});
					});
				});
			});

			//db.collection(prefix + appid).createIndex()
			typeCrud.createRaw(appid, purchase, function () {
				typeCrud.createRaw(appid, pageview, function () {
					typeCrud.createRaw(appid, click, function () {
						typeCrud.createRaw(appid, rate, function () {
							typeCrud.createRaw(appid, like, function () {
								typeCrud.createRaw(appid, download, function () {
									typeCrud.createRaw(appid, register, function () {
										typeCrud.createRaw(appid, login, function () {
											typeCrud.createRaw(appid, search, function () {
												trendCrud.createRaw(appid, trend1, function () {
													segmentCrud.createRaw(appid, segment1, callback);
												});
											});
										});
									});
								});
							});
						});
					});
				});
			});
		};
	};
})();
