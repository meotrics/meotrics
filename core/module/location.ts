import * as request from 'request';
import * as mongodb from 'mongodb';
var maxmind = require('maxmind');

class Location {
	public constructor(public city: string, public country: string) { }
}
export class LocationMgr {
	private collection: mongodb.Collection;
	private lookup;
	public constructor(private db: mongodb.Db, private prefix: string) {
		this.collection = db.collection(prefix + "ip");

		this.lookup = maxmind.open('./asset/GeoLite2-City.mmdb', {
			cache: {
				max: 1000, // max items in cache
				maxAge: 1000 * 60 * 60 // life time in milliseconds
			}
		});
	}

	public parse(ip: string, callback: (data: Location ) => void) {
		var loc = this.lookup.get(ip);
		if (loc === null) loc = { country: { names: { en: null } }, city: { names: { en: null } } };
		console.log(loc.city.names.en, loc.country.names.en);
		return callback(new Location(loc.city.names.en, loc.country.names.en));
	}
}