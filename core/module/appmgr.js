exports.AppMgr = function (db, mongodb, async, converter, prefix, mtthrow) {
	this.isSetup = function(appid, callback)
	{
		db.collection(prefix + appid).count({_isUser: { $exists: false }}, function(err, count)
		{
			if(err) throw err;
			return callback(count > 1);
		});
	}
}
