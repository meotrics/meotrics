'use strict';

const globalVariables = require('../../lib/utils/global-variables.js');
const express = require('express');
const consts = require('../../lib/consts/consts.js');
const _ = require('lodash');
const config = require('config');
const validator = require('validator');
const async = require('async');
const util = require('util');
const router = express.Router();

// Get all action type
router.get('/action-type/:appid', validate, getConverter, function(req, res, next){
    let appid = req.params.appid;
    let collection = config.mongod.prefix + 'app' + appid;
    let fieldMapped = req.meotrics_converters._typeid;

    globalVariables
        .get('db')
        .collection(collection)
        .distinct(fieldMapped, (err, result) => {
            if(err) {
                return res.json({
                    ec: consts.CODE.ERROR,
                    error: util.inspect(err)
                });
            }

            res.json({
                ec: consts.CODE.SUCCESS,
                data: result
            });
        });
});

// Middleware for checking data
function validate(req, res, next) {
    return next();
}

function getConverter(req, res, next) {
    globalVariables.get('converter').toIDs(['_typeid'], ids => {
        req.meotrics_converters = ids;
        return next();
    });
}

module.exports = function (app) {
    app.use('/', router);
};
