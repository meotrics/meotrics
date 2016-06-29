import * as mongodb from 'mongodb';

export class SessionMgr {

	constructor(private db: mongodb.Db, private prefix: string) {
	}

	create(mtid: string, callback: (id: string) => void) {
		var me = this;
		me.db.collection(me.prefix + "session").insert({ mtid: mtid }, function (err, r) {
			if (err) throw err;
			callback(r.insertedId.toHexString());
		});
	}
}