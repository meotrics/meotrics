var types = types || [];
var segments = segments || [];
//load segment

var refreshhandlers = {
	init: function () {
		//noop
	},
	makedraft: function () {
	},
	editdraft: function () {
	},
	segmenation: function () {
	},
	editsegment: function () {
	}
};

var handlers = {
	init_makedraf: function () {
		//TODO:
		//make sure segment select id draf
		//reload the builder
		//reload the grapth
	},
	init_editdraft: function () {
		//do nothing
	},
	init_segmentation: function () {
		//TODO:
		//make sure segment select is segment name
		//set the builder to segment data
		//reload the graph
	},
	makedraft_editdraft: function () {
		//TODO:
		//do nothing
	},
	editdraft_segmentation: function () {
		handlers.init_segmentation();
	},
	makedraft_segmentation: function () {
		handlers.init_segmentation();
	},
	segmentation_makedraft: function () {
		handlers.init_makedraf();
	},
	segmentation_editsegment: function () {
		//do nothing
	},
	editsegment_makedraft: function () {
		handlers.init_makedraf();
	},
	editsegment_segmentation: function () {
		handlers.init_makedraf();
	}
};


//EVENT


sq.change(function () {
	switch (state.current) {
		case "makedraft":
			//TODO: ENSURE


			state.switch("editdraft");
			break;
		case "editdraft":
			//TODO: ENSURE

			break;
		case "segmentation":
			//TODO: ENSURE
			break;
		case "editsegment":
			//TODO: ENSURE
			break;
		case "init":
			state.switch("editdraft");
			break;
		default:
			throw "wrong state";
	}
});


$se.change(function () {
	var val = $se.val();

	switch (state.current) {
		case "makedraft":
			//TODO: ENSURE

			if (val == "draft") {//noop
			}
			else
				state.switch('segmentation');
			break;
		case "editdraft":
			//TODO: ENSURE
			break;
		case "segmentation":
			//TODO: ENSURE


			if (val == "draft")
				state.switch("makedraft");
			else
				state.switch('segmentation');
			break;
		case "editsegment":
			//TODO: ENSURE

			if (val == "draft")
				state.switch("makedraft");
			else
				state.switch('segmentation');
			break;

			break;
		case "init":
			if (val == "draft")
				state.switch("makedraft");
			else
				state.switch('segmentation');
			break;
		default:
			throw "wrong state";
	}

});

$('.id_savesegmentasbt').click(function()
{	switch (state.current) {
	case "makedraft":
		//TODO: ENSURE
			//DO SAVE
			state.switch('segmentation');
		break;
	case "editdraft":
		//TODO: ENSURE

		//DO SAVE
		state.switch('segmentation');
		break;
	case "segmentation":
		//TODO: ENSURE
		//DO SAVE
		break;
	case "editsegment":
		//TODO: ENSURE

		//DO SAVE
		state.switch('segmentation');

		break;
	case "init":
		break;
	default:
		throw "wrong state";
}

});

var state = new StateMachine("F8EAC42A-9370-48B7-96B2-CB1C138B5498-2C835E56-9E4F-4BEC-9E11-2911C3FA6EC6", handlers, refreshhandlers);
