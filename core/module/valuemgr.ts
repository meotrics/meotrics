import * as redis from 'redis';
//import * as redisscanner from './redisscanner';
import rscn = require('../utils/redisscanner');

export class ValueMgr {
	private redisscanner: rscn.RedisScanner;

	public constructor(private rdb: redis.RedisClient) {

		this.redisscanner = new rscn.RedisScanner(rdb);
	}

	public convert(appid: string, object: any, fields: string[], callback: () => any) {
		var c = 0;
		for (var f of fields)
			this.cineValue(appid, f, object[f], function (number) {
				c++;
				if (c == fields.length)
					return callback;
				object[f] = number;
			});
	}

	// create if not exist (cine)
	// value can be array or string, if value is an array, this method do not
	// guarrenty the order of value
	public cineValue(appid: string, field: string, value: any, callback?: (number) => any) {
		var me = this;

		// if value is an array then return array of converted value
		if (value instanceof Array) {
			var c = 0;
			var rets = [];
			var i = 0;

			function doNext(v: string) {
				me.cineValue(appid, field, v, function (ret) {
					rets.push(ret);
					i++;
					if (i == value.length) {
						if (callback) return callback(rets);
					}
					return doNext(value[i]);
				});
			}
		}

		// value is a string
		var prop = this.queryToPattern(appid, field, 'eq', value);
		this.rdb.get(prop, function (err, ret) {
			if (err) throw err;
			if (ret != null) {
				if (callback) return callback(parseInt(ret));
				return;
			}
			else {
				var pattern = this.queryToPattern(appid, field) + "*";
				this.redisscanner.count(pattern, function (ret) {
					// must be synchonyze
					me.rdb.set(this.queryToPattern(appid, field) + ":" + value, ret + 1);
					if (callback) callback(ret + 1);
					return;
				})
			}
		});
	}

	private queryToPattern(appid: string, field: string, operator?: string, value?: string): string {
		var pattern = "field" + ":" + appid + ":" + field + ":";
		switch (operator) {
			case 'eq':
				pattern += value;
				break;
			case 'con':
				pattern += "*" + value + "*";
			case 'ncon':
				pattern += value;
			case 'sw':
				pattern += "*" + value;
			case 'ew':
				pattern += value + "*";
			default:
				break;
		}
		return pattern;
	}

	public convertCondition(appid: string, field: string, operator: string, value: string, callback: (string) => void) {
		var me = this;
		var pattern = this.queryToPattern(appid, field, operator, value);
		this.redisscanner.scan(pattern, function (keys) {
			me.rdb.mget(keys, function (err, values: string[]) {
				if (err) throw err;
				var condition = {};
				if (condition == 'ncon' || condition == 'ne')
					condition[field] = { $nin: values };
				else
					condition[field] = { $in: values };
				callback(condition);
			});
		});
	}
}