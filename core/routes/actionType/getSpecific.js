'use strict';

const globalVariables = require('../../lib/utils/global-variables.js');
const express = require('express');
const consts = require('../../lib/consts/consts.js');
const _ = require('lodash');
const config = require('config');
const validator = require('validator');
const async = require('async');
const mongodb = require('mongodb');
const router = express.Router();
let util = require('util');

let allFields = [
    '_id',
    'codename',
    'name',
    'desc',
    'deftrendfields',
    'deftrendobjects',
    'fields',
    '_appid',
    '_ctime'
];

let defaultFields = [
    'fields'
];


// Get a specific action type
router.get('/action-type/:_appid/:_typeid',
    validate,
    getConverter,
    handleMoreFileds,
    function(req, res, next) {
        let _appid = req.params._appid;
        let _typeid = req.params._typeid;
        let collection = config.mongod.prefix + consts.ACTION_TYPE_COLLECTION;

        let query = {
            [req.meotrics_converters['_appid']]: _appid,
            [req.meotrics_converters['codename']]: _typeid
        };

        let projection = {};
        req.meotrics_moreFields.forEach(field => {
            projection[req.meotrics_converters[field]] = 1;
        });

        let options = {};

        globalVariables
            .get('db')
            .collection(collection)
            .findOne(query, projection, options, (err, result) => {
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
    globalVariables.get('converter').toIDs(allFields, ids => {
        req.meotrics_converters = ids;
        return next();
    });
}

function handleMoreFileds(req, res, next) {
    let more = req.query.more || '';
    let moreFields = more.split(',');

    let temp = [];
    moreFields.forEach(field => {
        if( (allFields.indexOf(field) !== -1) && (defaultFields.indexOf(field) === -1) ) {
            temp.push(field);
        }
    });

    req.meotrics_moreFields = temp.concat(defaultFields);

    return next();
}

module.exports = function (app) {
    app.use('/', router);
};
