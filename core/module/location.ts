import * as request from 'request';
import * as mongodb from 'mongodb';

export class LocationMgr {
	private collection: mongodb.Collection;
	public constructor(private db: mongodb.Db, private prefix: string) {
		this.collection = db.collection(prefix + "ip");
	}

	public parse(ip: string, callback: (data) => void) {
		var me = this;
		// 1 find in database
		me.collection.find({ ip: ip }).limit(1).toArray(function (err, res) {
			if (err) throw err;
			if (res.length !== 0)
				return callback(res[0]);

			// 2 using the api
			request('http://ipinfo.io/' + ip, function (err, res, body)
			{
				if (!err && res.statusCode == 200) {
					callback(body);
					body = JSON.parse(body);
					me.collection.insertOne(body, function (err, ret) {
						if (err) throw err;
					});
				}
			});
		});
	}
}