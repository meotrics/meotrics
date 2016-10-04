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
router.get('/action-type/:appid', validate, function(req, res, next){
    const defaultField = [
        'name',
        '_id',
    ];

    const appid = req.params.appid;

    const projection = {};
    const totalFields = defaultField.concat(req.moreFields);
    totalFields.forEach(v => {
        projection[v] = 1;
    });

    const options = {
        sort: '_ctime'
    };

    const collection = config.mongod.prefix + consts.ACTION_TYPE_COLLECTION;

    globalVariables.get('db').collection(collection).find({
        _appid: appid
    }, projection, options).toArray((err, r) => {
        if(err) {
            return next(err);
        }

        console.log(r);
        res.json({
            ec: consts.CODE.SUCCESS,
            data: r
        });
    })
});

// Middleware for checking data
function validate(req, res, next) {
    console.log('action-type:get:', req.params, req.query);
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
