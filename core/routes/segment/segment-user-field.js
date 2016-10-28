'use strict';

const globalVariables = require('../../lib/utils/global-variables.js');
const mongoUtils = require('../../lib/utils/mongo-utils.js');
const express = require('express');
const consts = require('../../lib/consts/consts.js');
const _ = require('lodash');
const config = require('config');
const validator = require('validator');
const async = require('async');
const mongodb = require('mongodb');
const segmentUtils = require('../../lib/utils/segment-utils.js');
let util = require('util');

const router = express.Router();

let fieldsForWork = [
    '_id',
    '_typeid',
    '_mtid',
    '_isUser',
    '_ctime',
    '_lastSeen'
];

let defaultFields = [
    '_lastSeen',
    '_ctime'
];

let restrictFields = [
    '_id'
];

let queryFormat = {
    field: '',
    _typeid: '',
    type: ''
};

let LIMIT = 10;

router.get('/segment-user-field/:_appid/:_mtid', validate, handleMoreFields, getConverter, function(req, res, next){
    let type = req.query.type;

    if(type === 'user') {
        handleUserField(req, res);
    } else if(type === 'action') {
        handleActionField(req, res);
    } else {
        return res.json({
            ec: consts.CODE.SUCCESS,
            data: []
        });
    }
});

// Middleware for checking data
function validate(req, res, next) {
    let _mtid = req.params._mtid;

    if(!validator.isMongoId(_mtid)) {
        return res.json({
            ec: consts.CODE.ERROR,
            error: util.inspect((new TypeError('meotrics id must be an ObjectId')))
        });
    }

    req.query = _.merge(_.cloneDeep(queryFormat), req.query);
    return next();
}

function handleMoreFields(req, res, next) {
    req.meotrics_moreFields = [];
    next();
}

function getConverter(req, res, next) {
    let moreFields = req.meotrics_moreFields;
    let field = req.query.field;

    globalVariables.get('converter').toIDs([field, ...moreFields, ...fieldsForWork], ids => {
        req.meotrics_converters = ids;
        return next();
    });
}

function handleUserField(req, res) {
    let field = req.query.field;
    let _mtid = req.params._mtid;
    let _appid = req.params._appid;

    let collection = config.mongod.prefix + 'app' + _appid;

    if(!field) {
        return res.json({
            ec: consts.CODE.SUCCESS,
            data: []
        });
    }

    let query = {
        [req.meotrics_converters['_isUser']]: true,
        [req.meotrics_converters['_mtid']]: new mongodb.ObjectID(_mtid)
    };

    let projection = mongoUtils.generateProjection(req.meotrics_converters, [field, ...defaultFields], restrictFields);
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

            result = result || {};
            if(!_.has(result, req.meotrics_converters[field])) {
                result[req.meotrics_converters[field]] = '';
            }

            if(!_.has(result, req.meotrics_converters['_lastSeen'])) {
                result[req.meotrics_converters['_lastSeen']] = Date.now();
            }

            return res.json({
                ec: consts.CODE.SUCCESS,
                data: [{
                    value: result[req.meotrics_converters[field]],
                    time: result[req.meotrics_converters['_lastSeen']]
                }]
            });
        });
}

function handleActionField(req, res) {
    let _mtid = req.params._mtid;
    let _appid = req.params._appid;
    let field = req.query.field;
    let _typeid = req.query._typeid;
    let collection = config.mongod.prefix + 'app' + _appid;

    if(!field || !_typeid) {
        return res.json({
            ec: consts.CODE.SUCCESS,
            data: []
        });
    }

    let query = {
        [req.meotrics_converters['_typeid']]: _typeid,
        [req.meotrics_converters['_mtid']]: new mongodb.ObjectID(_mtid)
    };

    let projection = mongoUtils.generateProjection(req.meotrics_converters, [field, ...defaultFields], restrictFields);

    let options = {
        sort: {
            [req.meotrics_converters['_ctime']]: -1
        },
        limit: LIMIT
    };

    globalVariables
        .get('db')
        .collection(collection)
        .find(query, projection, options)
        .toArray((err, result) => {
            if(err) {
                return res.json({
                    ec: consts.CODE.ERROR,
                    error: util.inspect(err)
                });
            }

            let temp = [];
            result.forEach(rowData => {
                if(!_.has(rowData, req.meotrics_converters[field])) {
                    rowData[req.meotrics_converters[field]] = '';
                }

                temp.push({
                    value: rowData[req.meotrics_converters[field]],
                    time: rowData[req.meotrics_converters['_ctime']]
                });
            });

            return res.json({
                ec: consts.CODE.SUCCESS,
                data: temp
            });
        });
}

module.exports = function (app) {
    app.use('/', router);
};
