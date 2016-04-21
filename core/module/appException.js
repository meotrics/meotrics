var trycatch = require('trycatch');

module.exports = function (app) {
	function next(res, err)
	{
		res.status(500).end(err.message);
		console.err(err, err.stack);
	}

	app.postEx = function (route, handle) {
		app.post(route, function (req, res) {
			trycatch(function () {
				handle(req, res);
			}, function (err) {
				next(res, err);
			});
		});
	};

	app.getEx = function (route, handle) {
		app.get(route, function (req, res) {
			trycatch(function () {
				handle(req, res);
			}, function (err) {
				next(res, err);
			});
		});
	};

	app.putEx = function (route, handle) {
		app.put(route, function (req, res) {
			trycatch(function () {
				handle(req, res);
			}, function (err) {
				next(res, err);
			});
		});
	};

	app.deleteEx = function (route, handle) {
		app.delete(route, function (req, res) {
			trycatch(function () {
				handle(req, res);
			}, function (err) {
				next(res, err);
			});
		});
	};
};