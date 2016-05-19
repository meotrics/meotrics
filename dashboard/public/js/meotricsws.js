var appcode = appcode || -1;
(function(){
	window.websock = {
		data: {
			'action_count': 0,
			'status' : 0
		},
		register: function(name, callback)
		{
			if(tree[name]=== undefined)
				tree[name] = [];
			tree[name].push(callback);
		}
	};
	var tree = {};

	function start() {
		var conn = new WebSocket('ws://' + window.location + '/ws?appid=' + appcode);

		conn.onopen = function (e) {
			//connected
		};

		conn.onmessage = function (e) {
			var name = e.name;
			window.websock.data[name] = e.value;
			if (tree[name] === undefined)
				tree[name] = [];

			for (var i in tree[name]) if(tree[name].hasOwnProperty(i))
				tree[name][i]();
		};

		conn.onclose = function () {
			//try to reconnect in 5 seconds
			setTimeout(start, 5000);
		};
	}

	start();

})();
