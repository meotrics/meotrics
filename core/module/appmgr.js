exports.AppMgr = function (db, mongodb, async, converter, prefix, typeCrud) {
	this.isSetup = function (appid, callback) {
		db.collection(prefix + appid).count({_isUser: {$exists: false}}, function (err, count) {
			if (err) throw err;
			return callback(count > 1);
		});
	};

	this.initApp = function (appid, callback) {

		var purchase = {
			codename: "purchase",
			name: "Purchase",
			desc: "A purchase action",
			fields: [
				{pname: "Product ID", pcode: "pid"},
				{pname: "Product's Name", pcode: "pname"},
				{pname: "Category's Name", pcode: "pid"},
				{pname: "Category ID", pcode: "cname"},
				{pname: "Price", pcode: "price"},
				{pname: "Quantity", pcode: "Quantity"},
				{pname: "Payment type", pcode: "paymentype"}]
		};

		var pageview =
		{
			codename: "pageview",
			name: "Pageview",
			desc: "User view a page",
			fields: []
		};

		var click =
		{
			codename: "click",
			name: "Click",
			desc: "User click on a object",
			fields: [
				{pname: "Object ID", pcode: "oid"},
				{pname: "Object Type", pcode: "type"}
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
				{pname: "Category ID", pcode: "pid"},
				{pname: "Category Name", pcode: "cname"},
				{pname: "Level", pcode: "level"}
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
				{pname: "Category ID", pcode: "pid"},
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

		var quit =
		{
			codename: "quit",
			name: "Quit",
			desc: "User quit website",
			fields: []
		};

		typeCrud.createRaw(appid, purchase, function () {
			typeCrud.createRaw(appid, pageview, function () {
				typeCrud.createRaw(appid, click, function () {
					typeCrud.createRaw(appid, rate, function () {
						typeCrud.createRaw(appid, like, function () {
							typeCrud.createRaw(appid, download, function () {
								typeCrud.createRaw(appid, register, function () {
									typeCrud.createRaw(appid, quit, callback);
								});
							});
						});
					});
				});
			});
		});
	}
};
