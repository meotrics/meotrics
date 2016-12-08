exports.PropMgr = function (db, mongodb, async, converter, prefix, mtthrow) {
	var me = this;
	var props = [
		// {
		// 	name: "Campaign", code: "_campaign",
		// 	operators: [{name: "Equal", code: "eq"},
		// 		{name: "Contain", code: "con"},
		// 		{name: "Start with", code: "sw"},
		// 		{name: "End with", code: "ew"},
		// 		{name: "Not contain", code: "ncon"}]
		// },
		{
			name: "First-touch Campaign", code: "_firstcampaign",
			operators: [{name: "Equal", code: "eq"},
				{name: "Contain", code: "con"},
				{name: "Start with", code: "sw"},
				{name: "End with", code: "ew"},
				{name: "Not contain", code: "ncon"}]
		},
		{
			name: "Last-touch Campaign", code: "_lastcampaign",
			operators: [{name: "Equal", code: "eq"},
				{name: "Contain", code: "con"},
				{name: "Start with", code: "sw"},
				{name: "End with", code: "ew"},
				{name: "Not contain", code: "ncon"}]
		},
		{
			name: "Campaign Source", code: "_utm_source",
			operators: [{name: "Equal", code: "eq"},
				{name: "Contain", code: "con"},
				{name: "Start with", code: "sw"},
				{name: "End with", code: "ew"},
				{name: "Not contain", code: "ncon"}]
		},
		{
			name: "Campaign Medium", code: "_utm_medium",
			operators: [{name: "Equal", code: "eq"},
				{name: "Contain", code: "con"},
				{name: "Start with", code: "sw"},
				{name: "End with", code: "ew"},
				{name: "Not contain", code: "ncon"}]
		},
		{
			name: "Campaign Term", code: "_utm_term",
			operators: [{name: "Equal", code: "eq"},
				{name: "Contain", code: "con"},
				{name: "Start with", code: "sw"},
				{name: "End with", code: "ew"},
				{name: "Not contain", code: "ncon"}]
		},
		{
			name: "Campaign Content", code: "_utm_content",
			operators: [{name: "Equal", code: "eq"},
				{name: "Contain", code: "con"},
				{name: "Start with", code: "sw"},
				{name: "End with", code: "ew"},
				{name: "Not contain", code: "ncon"}]
		},
		{
			name: "Channel", code: "_reftype",
			operators: [{name: "Equal", code: "eq"},
				{name: "Contain", code: "con"},
				{name: "Start with", code: "sw"},
				{name: "End with", code: "ew"},
				{name: "Not contain", code: "ncon"}]
		},{
			name: "email", code: "email",
			operators: [{name: "Equal", code: "eq"},
				{name: "Contain", code: "con"},
				{name: "Start with", code: "sw"},
				{name: "End with", code: "ew"},
				{name: "Not contain", code: "ncon"}]
		},{
			name: "Referrer", code: "_ref",
			operators: [{name: "Equal", code: "eq"},
				{name: "Contain", code: "con"},
				{name: "Start with", code: "sw"},
				{name: "End with", code: "ew"},
				{name: "Not contain", code: "ncon"}]
		},
		{
			name: "Number of Purchase",
			code: '_numberPurchase',
			operators: [{name: "Equal", code: "eq"}]
		},
		{
			name: "Create time", code: "_ctime",
			operators: [
				{name: "From", code: "gte"},
				{name: "To", code: "lte"},
			]
		},
		{
			name: "Age", code: "age",
			operators: [
				{name: "Greater than", code: "gt"},
				{name: "Greater or equal", code: "gte"},
				{name: "Equal", code: "eq"},
				{name: "Less or equal", code: "lte"},
				{name: "Less than", code: "lt"},
			]
		}, {
			name: "Gender", code: "gender",
			operators: [{name: "Equal", code: "eq"}]
		}, 
		//{
		//	name: "Phone", code: "phone",
		//	operators: [{ name: "Equal", code: "eq" }]
		//}, {
		//	name: "Email", code: "email",
		//	operators: [{ name: "Equal", code: "eq" },
		//	{name: "Contain", code: "con"},
		//		{name: "Start with", code: "sw"},
		//		{name: "End with", code: "ew"},
		//		{name: "Not contain", code: "ncon"}]
		//},

		 {
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
		},
		// {
		// 	name: "Time zone", code: "_timezone",
		// 	operators: [{name: "Equal", code: "eq"}]
		// },
		{
			name: "Device Type", code: "_devicetype",
			operators: [{name: "Equal", code: "eq"}]
		}, {
			name: "Device", code: "_deviceid", 
			operators:  [{name: "Equal", code: "eq"},
                                {name: "Contain", code: "con"},
                                {name: "Start with", code: "sw"},
                                {name: "End with", code: "ew"},
                                {name: "Not contain", code: "ncon"}]
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
			operators: [
				{name: "Equal", code: "eq"},
				{name: "Contain", code: "con"},
				{name: "Start with", code: "sw"},
				{name: "End with", code: "ew"},
				{name: "Not contain", code: "ncon"}
			]
		}, {
			name: "Screen Resolution", code: "_scr",
			operators: [
				{name: "Equal", code: "eq"},
				{name: "Contain", code: "con"},
				{name: "Start with", code: "sw"},
				{name: "End with", code: "ew"},
				{name: "Not contain", code: "ncon"}
			]
		},
		// {
		// 	name: "Location", code: "_location",
		// 	operators: [{name: "Equal", code: "eq"},
		// 		{name: "Contain", code: "con"},
		// 		{name: "Start with", code: "sw"},
		// 		{name: "End with", code: "ew"},
		// 		{name: "Not contain", code: "ncon"}]
		// },
		{
			name: "OS Version", code: "_osver",
			operators: [{name: "Equal", code: "eq"},
				{name: "Contain", code: "con"},
				{name: "Start with", code: "sw"},
				{name: "End with", code: "ew"},
				{name: "Not contain", code: "ncon"}]
		},{
			name: "Browser Version", code: "_browserver",
			operators: [{name: "Equal", code: "eq"},
				{name: "Contain", code: "con"},
				{name: "Start with", code: "sw"},
				{name: "End with", code: "ew"},
				{name: "Not contain", code: "ncon"}]
		}, {
			name: "Revenue", code: "_revenue",
			operators: [
				{name: "Greater than", code: "gt"},
				{name: "Greater or equal", code: "gte"},
				{name: "Equal", code: "eq"},
				{name: "Less or equal", code: "lte"},
				{name: "Less than", code: "lt"},
			]
		}
		// , {
		// 	name: "Did Purchase", code: "purchase",
		// 	operators: [{name: "Count", code: "count"}]
		// }

	];
		
	this.list = function (req, res) {
		
		res.json(props);
	};
};
