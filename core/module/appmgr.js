exports.AppMgr = function (db, mongodb, async, converter, prefix, typeCrud, segmentCrud, trendCrud) {
	var me = this;
	this.isSetup = function (appid, callback) {
		me.countAction(appid, function (count) {
			callback(count > 1);
		});
	};

	this.countAction = function (appid, callback) {
		converter.toIDs(['_isUser'], function (ids) {
			var query = {};
			query[ids._isUser] = {$exists: false};
			db.collection(prefix + appid).count(query, function (err, count) {
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
				{pname: "Payment type", pcode: "paymentype"}
			],
			deftrendobjects: [
				{desc: "Number of time purchased", operation: "count", param: "_id"},
				{desc: "Number of people purchased", operation: "count", param: "_mtid"},
				{desc: "Total ammount of purchasing", operation: "sum", param: "amount"},
			],
			fields: [
				{pname: "Product ID", pcode: "pid"},
				{pname: "Product's Name", pcode: "pname"},
				{pname: "Category", pcode: "cid"},
				{pname: "Category ID", pcode: "cname"},
				{pname: "Price of product", pcode: "price"},
				{pname: "Total amount", pcode: "amount"},
				{pname: "Quantity", pcode: "quantity"},
				{pname: "Payment type", pcode: "paymentype"}]
		};

		var pageview =
		{
			codename: "pageview",
			name: "Pageview",
			desc: "User view a page",
			fields: [],
			deftrendfields: [
				{pname: "URL", pcode: "url"},
			],
			deftrendobjects: [
				{desc: "Number of pageview", operation: "count", param: "_id"},
				{desc: "Number of unique visitor", operation: "count", param: "_mtid"},
				{desc: "Time on page", operation: "sum", param: "timeonpage"},
			],
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
				{desc: "Number of unique people clicked", operation: "count", param: "_mtid"},
			],
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
				{pname: "Product", pcode: "pid"},
			],
			deftrendobjects: [
				{desc: "Number of time rated", operation: "count", param: "_id"},
				{desc: "Number of unique people rated", operation: "count", param: "_mtid"},
			],
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
			desc: "User register to a page",
			fields: []
		};

		var login =
		{
			codename: "login",
			name: "Login",
			desc: "User login to site",
			fields: []
		};

		var quit =
		{
			codename: "quit",
			name: "Quit",
			desc: "User quit website",
			fields: []
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
			condition: [{}]
		};

		converter.toIDs(['_mtid'], function(ids)
		{
			db.collection(prefix + 'appid', {ids._mtid : 1}, {sparse: true});
			db.collection(prefix + 'appid', {ids._id : 1, ids._isUser}, {sparse: true});
			db.collection(prefix + 'appid', {ids.ctime, ids._segments : 1, ids_typeid: 1}, {sparse: true});
		});
		

		typeCrud.createRaw(appid, purchase, function () {
			typeCrud.createRaw(appid, pageview, function () {
				typeCrud.createRaw(appid, click, function () {
					typeCrud.createRaw(appid, rate, function () {
						typeCrud.createRaw(appid, like, function () {
							typeCrud.createRaw(appid, download, function () {
								typeCrud.createRaw(appid, register, function () {
									typeCrud.createRaw(appid, login, function () {
										typeCrud.createRaw(appid, quit, function () {
											trendCrud.createRaw(appid, trend1, function(){
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
	}
}
;
