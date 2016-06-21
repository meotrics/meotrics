var userid = userid || 0;
(function () {

	//var stack = [];

	window.websock = {
		change: function (code, callback) {
			if (tree[code] === undefined)
				tree[code] = [];
			tree[code].push(callback);
		},
		appChange: function (appcode, code, callback) {
			if (branch[appcode] === undefined) branch[appcode] = {};
			if (branch[appcode][name] === undefined) branch[appcode][code] = [];

			branch[appcode][code].push(callback);

			if(conn.readyState == 1)
				conn.send(JSON.stringify({appid: appcode, code: code}));
		}
	};
	// boardcast event
	var tree = {};

	// topic based event
	var branch = {};
	var conn ;
	function start() {
		conn = new WebSocket('wss://' + window.location.host + '/ws', 'mtdashboard');

		conn.onopen = function(event){
			console.log('ok');
			for(var i in branch) if(branch.hasOwnProperty(i))
				for(var j in branch[i]) if(branch[i].hasOwnProperty(j))
					conn.send(JSON.stringify({appid: i, code: j}));
			

			// clean the queue
			//while(stack.length != 0)
			//	conn.send(JSON.stringify(stack.pop()));
		};
		
		conn.onmessage = function (e) {
			var mes = JSON.parse(e.data);

			if (tree[mes[mes.code]] !== undefined)
				for (var i in tree[mes.code]) if (tree[mes.code].hasOwnProperty(i))
				tree[mes.code][i](mes.appid, mes.code);

			if(branch[mes.appid] !== undefined && branch[mes.appid][mes.code] !== undefined)
			for (var j in branch[mes.appid][mes.code]) if (branch[mes.appid][mes.code].hasOwnProperty(j))
				branch[mes.appid][mes.code][j](mes.appid, mes.code);
		};

		conn.onclose = function () {
			//try to reconnect in 5 seconds
			setTimeout(start, 5000);
		};
	}

	start();

})();
