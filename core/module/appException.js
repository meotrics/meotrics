var trycatch = require('trycatch');

module.exports = function (app) {
	app.postEx = function (route, handle) {
		app.post(route, function (req, res, next) {
			trycatch(function () {
				handle(req, res, next);
			}, function (err) {
				next(err);
			});
		});
	};

	app.getEx = function (route, handle) {
		app.get(route, function (req, res, next) {
			trycatch(function () {
				handle(req, res, next);
			}, function (err) {
				next(err);
			});
		});
	};

	app.putEx = function (route, handle) {
		app.put(route, function (req, res, next) {
			trycatch(function () {
				handle(req, res, next);
			}, function (err) {
				next(err);
			});
		});
	};

	app.deleteEx = function (route, handle) {
		app.delete(route, function (req, res, next) {
			trycatch(function () {
				handle(req, res, next);
			}, function (err) {
				next(err);
			});
		});
	};

	app.use(function (err, req, res, next) {
		res.status(500).end(err.message);
	});
};