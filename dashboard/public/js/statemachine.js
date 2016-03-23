function StateMachine(_guid, handlers, refreshhandlers) {
	var guid = "state-" + _guid;
	var me = this;
	var state;
	//first time
	if (localStorage[guid] == undefined) {
		state = {};
		me.current = me.last = state.current = state.last = "init";
		localStorage[guid] = JSON.stringify(state);
	}
	else {
		//page get refreshed
		state = JSON.parse(localStorage[guild]);
		me.current = state.current;
		me.last = state.last;
		refreshhandlers[state.current]();
	}

	this.switch = function (newstate) {
		var state = localStorage[guid];
		me.last = state.last = state.current;
		me.current = state.current = newstate;
		localStorage[guid] = JSON.stringify(state);
		//call handler
		handlers[[state.last, state.current]]();
	};
}