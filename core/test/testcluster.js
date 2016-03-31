"use strict";

var cluster = require('cluster');

if (cluster.isMaster) {
	var config = require('config');
	var numWebservers = config.get("numWebservers");
	console.log('Master cluster setting up ' + numWebservers + ' webservers...');
	for (let i = 0; i < numWebservers; i++) {
		cluster.fork();
	}

	cluster.on('exit', function(){
		console.log('exited');
	});
} else {
	require('./clustersampleapp.js');
}