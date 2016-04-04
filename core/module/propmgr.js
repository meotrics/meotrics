exports.PropMgr = function (db, mongodb, async, converter, prefix, mtthrow) {
	var me = this;
	this.list = function (req, res) {
		var props = [{
			name: "Age", code: "age",
			operators: [{name: "Less than", code: "lt"},
				{name: "Greater than", code: "gt"},
				{name: "Equal", code: "eq"},
				{name: "From .. to ..", code: "in"}]
		}, {
			name: "Gender", code: "gender",
			operators: [{name: "Equal", code: "eq"}]
		}, {
			name: "Country", code: "country",
			operators: [{name: "Equal", code: "eq"},
				{name: "Contain", code: "con"},
				{name: "Start with", code: "sw"},
				{name: "End with", code: "ew"},
				{name: "Not contain", code: "ncon"}]
		}, {
			name: "City", code: "city",
			operators: [{name: "Equal", code: "eq"},
				{name: "Contain", code: "con"},
				{name: "Start with", code: "sw"},
				{name: "End with", code: "ew"},
				{name: "Not contain", code: "ncon"}]
		}, {
			name: "Time zone", code: "timezone",
			operators: [{name: "Equal", code: "eq"}]
		}, {
			name: "Device Type", code: "devicetype",
			operators: [{name: "Equal", code: "eq"}]
		}, {
			name: "Operating System", code: "osid",
			operators: [{name: "Equal", code: "eq"},
				{name: "Contain", code: "con"},
				{name: "Start with", code: "sw"},
				{name: "End with", code: "ew"},
				{name: "Not contain", code: "ncon"}]
		}, {
			name: "Browser", code: "browserid",
			operators: [{name: "Equal", code: "eq"},
				{name: "Contain", code: "con"},
				{name: "Start with", code: "sw"},
				{name: "End with", code: "ew"},
				{name: "Not contain", code: "ncon"}]
		}, {
			name: "Language", code: "lang",
			operators: [{name: "Equal", code: "eq"}]
		}, {
			name: "Screen Resolution", code: "screenres",
			operators: [{name: "Equal", code: "eq"}]
		}, {
			name: "Referrer", code: "referrer",
			operators: [{name: "Equal", code: "eq"},
				{name: "Contain", code: "con"},
				{name: "Start with", code: "sw"},
				{name: "End with", code: "ew"},
				{name: "Not contain", code: "ncon"}]
		}, {
			name: "Campaign", code: "campaign",
			operators: [{name: "Equal", code: "eq"},
				{name: "Contain", code: "con"},
				{name: "Start with", code: "sw"},
				{name: "End with", code: "ew"},
				{name: "Not contain", code: "ncon"}]
		}, {
			name: "Revenue", code: "revenue",
			operators: [{name: "Less than", code: "lt"},
				{name: "Greater than", code: "gt"},
				{name: "Equal", code: "eq"},
				{name: "From .. to ..", code: "in"}]
		}];
		res.json(props);
	};
};