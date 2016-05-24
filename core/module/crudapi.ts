import * as mongodb from 'mongodb';
import * as async from 'async';

var trycatch = require('trycatch');
import bodyParser = require('body-parser');
var CRUD = require('./crud.js').CRUD;

var appException = require('./appException.js');
var TrendMgr = require('./trendmgr.js').TrendMgr;
//var ActionMgr = require('./module/actionmgr.js').ActionMgr;
import * as ActionMgr from './actionmgr';
var PropMgr = require('./propmgr.js').PropMgr;
var AppMgr = require('./appmgr.js').AppMgr;
var SegMgr = require('./segment.js').SegmentExr;
var TypeMgr = require('./typemgr.js').TypeMgr;
var ValueMgr = require('./valuemgr.js').ValueMgr;
var Dashboard = require('./dashboard').Dashboard;
import http = require('http');

import fs = require('fs');
import qs = require('querystring');
import url = require('url');
export class CrudApi {
	public  constructor(private db:mongodb.Db, private converter, private prefix, private dashboarddelay)
	{
	}

	public dashboard = new Dashboard(this.db, mongodb, this.converter, this.prefix, this.dashboarddelay);
	public trendMgr= new TrendMgr(this.db, mongodb, async, this.converter, this.prefix, "trend");
	public actionMgr= new ActionMgr.ActionMgr(this.db, this.converter, this.prefix, "mapping");
	public propmgr= new PropMgr();
	public typeCRUD= new CRUD(this.db, mongodb, async, this.converter, this.prefix, "actiontype");
	public trendCRUD= new CRUD(this.db, mongodb, async, this.converter, this.prefix, "trend");
	public segCRUD= new CRUD(this.db, mongodb, async, this.converter, this.prefix, "segment");
	public propCRUD= new CRUD(this.db, mongodb, async, this.converter, this.prefix, "userprop");
	public camCRUD= new CRUD(this.db, mongodb, async, this.converter, this.prefix, "campaign");
	public segMgr= new SegMgr(this.db, mongodb, async, this.converter, this.prefix);
	public valuemgr = new ValueMgr(this.db, this.prefix);
	public typemgr = new TypeMgr(this.db, mongodb, this.converter, async, this.prefix, this.typeCRUD, "actiontype");
	public appmgr = new AppMgr(this.db, this.converter, this.prefix, this.typeCRUD, this.segCRUD, this.trendCRUD);

	public route(app):void {
		var me = this;
			// CRUD actiontype
		app.postEx('/actiontype/:appid', this.typemgr.create); // create an actiontype
		app.getEx('/actiontype/:appid', this.typemgr.list);	// get all actiontypes
		app.getEx('/actiontype/:appid/:id', this.typemgr.match); // get an actiontype
		app.putEx('/actiontype/:appid/:id', this.typemgr.update); // update an actiontype
		app.deleteEx('/actiontype/:appid/:id', this.typemgr.delete); // delete an actiontype

		// CRUD trend
		app.postEx('/trend/:appid', this.trendCRUD.create);
		app.getEx('/trend/:appid', this.trendCRUD.list);
		app.getEx('/trend/:appid/:id', this.trendCRUD.match);
		app.putEx('/trend/:appid/:id', this.trendCRUD.update);
		app.deleteEx('/trend/:appid/:id', this.trendCRUD.delete);
		// Query trend

		app.get('/trend/query/:appid/:id/:segid?/:starttime?/:endtime?', this.trendMgr.query);

		// CRUD segment
		app.postEx('/segment/:appid', function (req, res) {
			me.segCRUD.create(req, res, function (id) {
				me.segMgr.excuteSegment(id);
			});
		});

		app.getEx('/segment/:appid', this.segCRUD.list);
		app.getEx('/segment/:appid/:id', function (req, res) {
			me.segCRUD.match(req, res, function () {
				me.segMgr.excuteSegment(req.params.id);
			});
		});

		app.putEx('/segment/:appid/:id', this.segCRUD.update);
		app.deleteEx('/segment/:appid/:id', this.segCRUD.delete);

		//update or
		app.get('/segment/query1/:appid/:id/:field1/', function (req, res) {
			me.segMgr.querySegment(req.params.appid, req.params.id, req.params.field1, undefined, function (results) {
				res.json(results);
			});
		});

		//update or
		app.get('/segment/query2/:appid/:id/:field1/:field2', function (req, res) {
			me.segMgr.querySegment(req.params.appid, req.params.id, req.params.field1, req.params.field2, function (results) {
				res.json(results);
			});
		});

		// CRUD user
		app.postEx('/userprop/:appid', this.propCRUD.create);
		app.getEx('/userprop/:appid', this.propCRUD.list);
		app.getEx('/userprop/:appid/:id', this.propCRUD.match);
		app.putEx('/userprop/:appid/:id', this.propCRUD.update);
		app.deleteEx('/userprop/:appid/:id', this.propCRUD.delete);

		app.get('/prop/:appid', this.propmgr.list);

		// CRUD campaign
		app.postEx('/campaign/:appid', this.camCRUD.create);
		app.getEx('/campaign/:appid', this.camCRUD.list);
		app.getEx('/campaign/:appid/:id', this.camCRUD.match);
		app.putEx('/campaign/:appid/:id', this.camCRUD.update);
		app.deleteEx('/campaign/:appid/:id', this.camCRUD.delete);

		// save a action
		app.postEx('/r/:appid', this.actionMgr.save);
		// identity an user
		app.postEx('/i/:appid', this.actionMgr.identify);
		// set up a new cookie
		app.getEx('/s/:appid', this.actionMgr.setup);

		app.postEx('/f/:appid/:actionid', this.actionMgr.fix);

		app.get('/app/init/:appid', function (req, res) {
			me.appmgr.initApp(req.params.appid, function () {
				res.send('OK');
				res.status(200).end();
			});
		});

		app.getEx('/dashboard/:appid', function (req, res) {
			me.dashboard.getDashboard(req.params.appid, function (result) {
				res.json(result);
			});
		});

		// count number of action in app
		app.getEx('/api/counter/:appid', function (req, res) {
			me.appmgr.countAction(req.params.appid, function (ret) {
				res.send(ret + "");
			});
		});

		//check whether user has setup tracking code
		app.get('/api/status/:appid', function (req, res) {
			me.appmgr.isSetup(req.params.appid, function (ret) {
				res.send(ret + "");
				res.status(200).end();
			});
		});
	}
}