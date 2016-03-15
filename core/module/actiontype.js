exports.TypeMgr = function (db, mongodb) {
	this.create = function (req, res) {
		var data = req.body;
		data._appid = Number(req.params.appid);
		var collection = prefix + "actiontype";
		db.collection(collection).insertOne(data) // add options: w, j, timeout ...
				.then(function (r) {
					res.json({_id: r.insertedId});
				}).catch(mtthrow);
	}

	this.listall = function (req, res) {
		var appid = Number(req.params.appid);
		var collection = prefix + "actiontype";
		db.collection(collection).find({_appid: appid}, {_appid: 0}).toArray()
				.then(function (results) {
					res.json(results);
				}).catch(mtthrow);
	}

	this.match = function (req, res) {
		// var appid = req.params.appid;
		var atid = req.params.id;
		var collection = prefix + "actiontype";
		db.collection(collection).find({_id: new mongodb.ObjectID(atid)}, {_appid: 0}).toArray()
				.then(function (results) {
					res.json(results);
				}).catch(mtthrow);
	}

	this.update = function (req, res) {
		var data = req.body;
		// var appid = req.params.appid;
		var atid = req.params.id;
		var collection = prefix + "actiontype";
		db.collection(collection).updateOne({_id: new mongodb.ObjectID(atid)}, {$set: data})
				.then(function (results) {
					res.status(200).end();
				}).catch(mtthrow);
	}

	this.delete = function (req, res) {
		// var appid = req.params.appid;
		var atid = req.params.id;
		var collection = prefix + "actiontype";
		db.collection(collection).deleteOne({_id: new mongodb.ObjectID(atid)})
				.then(function (results) {
					res.status(200).end();
				}).catch(mtthrow);
	}
}