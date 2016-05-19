var userid = userid || 0;
(function () {
	window.websock = {
		data: {
			0: {
				'action_count': 0,
				'status': 0
			}
		},
		change: function (name, callback) {
			if (tree[name] === undefined)
				tree[name] = [];
			tree[name].push(callback);
		},
		appChange: function (appcode, name, callback) {
			if (branch[appcode] === undefined) branch[appcode] = {};
			if (branch[appcode][name] === undefined) branch[appcode][name] = [];

			branch[appcode][name].push(callback);
		}
	};
	var tree = {};
	var branch = {};

	function start() {
		var conn = new WebSocket('ws://' + window.location + '/ws?appid=' + userid);

		conn.onopen = function (e) {
			//connected
		};

		conn.onmessage = function (e) {
			var name = e.name;

			window.websock.data[e.code][name] = e.value;
			if (tree[name] === undefined)
				tree[name] = [];

			for (var j in branch[e.code][name]) if (branch[e.code][name].hasOwnProperty(j))
				branch[e.code][name][j](e.code);

			for (var i in tree[name]) if (tree[name].hasOwnProperty(i))
				tree[name][i]();
		};

		conn.onclose = function () {
			//try to reconnect in 5 seconds
			setTimeout(start, 5000);
		};
	}

	start();

})();
