'use strict';

const globalVariables = require('../../lib/utils/global-variables.js');
const express = require('express');
const consts = require('../../lib/consts/consts.js');
const _ = require('lodash');
const config = require('config');
const validator = require('validator');
const async = require('async');

const router = express.Router();

// Get all action type
router.get('/action-type/:appid', validate, handleMoreFields, function(req, res, next){
    const appid = req.params.appid;

    const query = {
        _appid: appid
    };

    const defaultFields = [
        'name',
        '_id',
    ];
    const projection = {};
    defaultFields.concat(req.moreFields).forEach(v => {
        projection[v] = 1;
    });


    const options = {
        sort: "name" // alpha-beta sort
    };

    const collection = config.mongod.prefix + consts.ACTION_TYPE_COLLECTION;

    globalVariables
        .get('db')
        .collection(collection)
        .find(query, projection, options)
        .toArray((err, r) => {
            if(err) {
                return next(err);
            }

            console.log(r);
            res.json({
                ec: consts.CODE.SUCCESS,
                data: r
            });
        });
});

// Middleware for checking data
function validate(req, res, next) {
    console.log(req.params);
    next();
}

// Handle get more fields
function handleMoreFields(req, res, next) {
    let moreFields = [];
    let extras = req.query.extras;
    if(!_.isUndefined(extras)) {
        let temp = extras.split(',');
        temp.forEach(v => {
            if(validator.isLength(v, 1)) {
                moreFields.push(v);
            }
        });
    }

    req.moreFields = moreFields;
    next();
}


module.exports = function (app) {
    app.use('/', router);
};
