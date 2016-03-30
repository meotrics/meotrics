exports.PropMgr = function (db, mongodb, async, converter, prefix, mtthrow) {
	var me = this;
	this.list = function (req, res) {
		var props = [{
			name: "Age", code: "age",
			operators: [{
				name: "Less than", code: "lt"
			}, {
				name: "Greater than", code: "gt",
			}, {
				name: "Equal", code: "eq"
			}, {
				name: "From .. to ..", code: "in"
			}]
		}, {
			name: "Gender", code: "gender",
			operators: [{
				name: "Equal", code: "eq"
			}]
		}, {
			name: "Country", code: "country",
			operators: [{
				name: "Equal", code: "eq"
			}]
		}, {
			name: "City", code: "city",
			operators: [{
				name: "Equal", code: "eq"
			}]
		}, {
			name: "Time zone", code: "timezone",
			operators: [{
				name: "Equal",
				code: "eq"
			}]
		}, {
			name: "Device Type", code: "devicetype",
			operators: [{
				name: "Equal", code: "eq"
			}]
		}, {
			name: "Operating System", code: "osid",
			operators: [{
				name: "Equal", code: "eq"
			}]
		}, {
			name: "Browser", code: "browserid",
			operators: [{
				name: "Equal", code: "eq"
			}]
		}, {
			name: "Language", code: "lang",
			operators: [{
				name: "Equal", code: "eq"
			}]
		}, {
			name: "Screen Resolution", code: "screenres",
			operators: [{
				name: "Equal", code: "eq"
			}]
		}, {
			name: "Referrer", code: "referrer",
			operators: [{
				name: "Equal", code: "eq"
			}, {
				name: "Campaign", code: "campaign",
				operators: [{
					name: "Equal", code: "eq"
				}]
			}, {
				name: "Revenue", code: "revenue",
				operators: [{
					name: "Equal", code: "eq"
				}]
			}]
		}];
		res.json(props);
	};
};