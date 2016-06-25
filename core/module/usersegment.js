"use strict";

exports.UserSegment = function (db, mongodb, async, converter, prefix) {
	const _self = this;
	const maxNumberUsers = 500;
	const numberUsersPerPage = 15;

	this.getUsers = function(appName, segmentId, fields, start, callback) {
		console.log(appName, segmentId, fields, start);

		if(start >= maxNumberUsers) {
			return callback([]);
		}

		let limit = numberUsersPerPage;
		const skip = start;

		if((maxNumberUsers - start) < numberUsersPerPage) {
			limit = maxNumberUsers - start;
		}

		// Get users from mongodb
		let fieldToTranslate = fields.slice().concat(['_segments', '_ctime', '_isUser', '_mtid', 'name']);
		converter.toIDs(fieldToTranslate, (ids) => {
			let query = {
				[ids['_segments']]: segmentId,
				[ids['_isUser']]: true
			};

			let sort = {
				[ids['_ctime']]: -1
			}

			db.collection(prefix + appName).find(query).sort(sort).skip(skip).limit(limit).toArray((err, users) => {
				if(err) throw err;
				const length = users.length;
				let usersReturn = [];

				if(length !== 0){
					for(let i=0; i<length; i++){
						usersReturn[i] = {};
						// Add default field here
						usersReturn[i][ids['name']] = users[i][ids['name']];
						usersReturn[i][ids['_mtid']] = users[i][ids['_mtid']];
					}
					for(let j=0; j<fields.length; j++) {
						let field = fields[j];
						for(let i=0;i<length; i++){
							let temp = users[i][ids[field]];
							if(isDemoGraphic(field)){
								// check if temp is array or not
								if(Array.isArray(temp)){
									usersReturn[i][ids[field]] = temp.pop();
									continue;
								}

							}
							usersReturn[i][ids[field]] = temp || '';
						}
					}
					callback(usersReturn);
				} else {
					callback(users);
				}
			});
		});
	}

	function isDemoGraphic(fieldName) {
		const demoGraphics = ['_os', '_devicetype', '_browser', '_campaign'];

		if(demoGraphics.indexOf(fieldName) !== -1){
			return true;
		}

		return false;
	}
}