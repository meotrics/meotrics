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

// Get a specific action type
router.get('/action-type/:appid/:_id', validate, function(req, res, next){
    const defaultField = [
        'name',
        '_id',
    ];

    const appid = req.params.appid;
    const _id = req.params._id;

    const projection = {};
    const totalFields = defaultField.concat(req.moreFields);
    totalFields.forEach(v => {
        projection[v] = 1;
    });
    const options = {};

    const collection = config.mongod.prefix + consts.ACTION_TYPE_COLLECTION;

    globalVariables.get('db').collection(collection).findOne({
        _appid: appid,
        _id: new mongodb.ObjectID(_id)
    }, projection, options, (err, r) => {
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
    console.log('action-type:get-specific:', req.params, req.query);

    const _id = req.params._id;

    if(!validator.isMongoId(_id)) {
        res.json({
            ec: consts.CODE.WRONG_PARAM,
            reason: "_id must be an object id"
        });

        return next('route');
    }

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
