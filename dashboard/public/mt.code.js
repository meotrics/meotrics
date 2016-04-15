var mt = mt || {
			track: function (event, data) {
				mt.rq.push(['track', event, data, new Date()]);
			},
			identify: function (data) {
				mt.rq.push(['identify', data]);
			},
			clear: function () {
				mt.rq.push(['clear']);
			}
		};

mt.rq = mt.rq || [];