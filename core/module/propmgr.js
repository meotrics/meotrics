exports.PropMgr = function (db, mongodb, async, converter, prefix, mtthrow) {
	var me = this;
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
			name: "Phone", code: "phone",
			operators: [{ name: "Equal", code: "eq" }]
		}, {
			name: "Country", code: "_country",
			operators: [{name: "Equal", code: "eq"},
				{name: "Contain", code: "con"},
				{name: "Start with", code: "sw"},
				{name: "End with", code: "ew"},
				{name: "Not contain", code: "ncon"}]
		}, {
			name: "City", code: "_city",
			operators: [{name: "Equal", code: "eq"},
				{name: "Contain", code: "con"},
				{name: "Start with", code: "sw"},
				{name: "End with", code: "ew"},
				{name: "Not contain", code: "ncon"}]
		}, {
			name: "Time zone", code: "_timezone",
			operators: [{name: "Equal", code: "eq"}]
		}, {
			name: "Device Type", code: "_devicetype",
			operators: [{name: "Equal", code: "eq"}]
		}, {
			name: "Operating System", code: "_os",
			operators: [{name: "Equal", code: "eq"},
				{name: "Contain", code: "con"},
				{name: "Start with", code: "sw"},
				{name: "End with", code: "ew"},
				{name: "Not contain", code: "ncon"}]
		}, {
			name: "Browser", code: "_browser",
			operators: [{name: "Equal", code: "eq"},
				{name: "Contain", code: "con"},
				{name: "Start with", code: "sw"},
				{name: "End with", code: "ew"},
				{name: "Not contain", code: "ncon"}]
		}, {
			name: "Language", code: "_lang",
			operators: [{name: "Equal", code: "eq"}]
		}, {
			name: "Screen Resolution", code: "_scr",
			operators: [{name: "Equal", code: "eq"}]
		}, {
			name: "Channel", code: "_reftype",
			operators: [{name: "Equal", code: "eq"},
				{name: "Contain", code: "con"},
				{name: "Start with", code: "sw"},
				{name: "End with", code: "ew"},
				{name: "Not contain", code: "ncon"}]
		},/* {
			name: "Campaign", code: "_campaign",
			operators: [{name: "Equal", code: "eq"},
				{name: "Contain", code: "con"},
				{name: "Start with", code: "sw"},
				{name: "End with", code: "ew"},
				{name: "Not contain", code: "ncon"}]
		},*/{
			name: "Last-touch Campaign", code: "_firstcampaign",
			operators: [{name: "Equal", code: "eq"},
				{name: "Contain", code: "con"},
				{name: "Start with", code: "sw"},
				{name: "End with", code: "ew"},
				{name: "Not contain", code: "ncon"}]
		},{
			name: "First-touch Campaign", code: "_lastcampaign",
			operators: [{name: "Equal", code: "eq"},
				{name: "Contain", code: "con"},
				{name: "Start with", code: "sw"},
				{name: "End with", code: "ew"},
				{name: "Not contain", code: "ncon"}]
		}, {
			name: "Revenue", code: "_revenue",
			operators: [{name: "Less than", code: "lt"},
				{name: "Greater than", code: "gt"},
				{name: "Equal", code: "eq"},
				{name: "From .. to ..", code: "in"}]
		}];
		
	this.list = function (req, res) {
		
		res.json(props);
	};
};