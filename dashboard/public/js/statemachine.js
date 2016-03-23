function StateMachine(_guid, transitions, handlers, refreshhandlers) {
	var guid = "state-" + _guid;
	var me = this;
	//var currentstate; state-CB83A33D-756B-4353-AC4B-7DA5F6B05AC0-9FAFA321-A9EB-4291-BD37-3DEB73DF7631

	var state;
	//first time
	if (localStorage[guid] == undefined) {
		state = {};
		state.current = state.last = "init";
		localStorage[guid] = JSON.stringify(state);
	}
	else {
		//page get refreshed
		state = JSON.parse(localStorage[guild]);
		refreshhandlers[state.current]();
	}

	this.switch = function (newstate) {
		var state = localStorage[guid];
		state.last = state.current;
		state.current = newstate;
		localStorage[guid] = JSON.stringify(state);
		//call handler
		handlers[[state.last, state.current]]();
	};
}