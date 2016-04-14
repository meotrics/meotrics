var mt = mt || {
			track: function (event, data) {
				mt.rq.push(['t', event, data, new Date()]);
			},
			identify: function (data) {
				mt.rq.push(['i', data]);
			},
			clear: function () {
				mt.rq.push(['c']);
			}
		};

mt.rq = mt.rq || [];