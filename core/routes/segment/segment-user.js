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
    '_segments',
    '_isUser',
    '_lastSeen'
];

let defaultFields = [
    '_mtid',
    'email',
];

let restrictFields = [
    '_id'
];

let queryFormat = {
    page: '',
    forceMore: ''
};

let LIMIT = 10;

router.get('/segment-user/:_appid/:_id', validate, handleMoreFields, getConverter, function(req, res, next){
    let segmentId = req.params._id;
    let _appid = req.params._appid;
    let page = req.query.page;
    let moreFields = req.meotrics_moreFields;

    let skip = page*LIMIT;
    let collection = config.mongod.prefix + 'app' + _appid;

    let query = {
        [req.meotrics_converters['_isUser']]: true,
        [req.meotrics_converters['_segments']]: new mongodb.ObjectID(segmentId)
    };

    let projection = mongoUtils.generateProjection(req.meotrics_converters, moreFields, restrictFields);

    let options = {
        sort: {
            [req.meotrics_converters['_lastSeen']]: -1
        },
        limit: LIMIT,
        skip: skip
    }

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

            result.forEach(data => {
                moreFields.forEach(field => {
                    if(!_.has(data, req.meotrics_converters[field]) && defaultFields.indexOf(field) === -1) {
                        data[field] = '';
                    }
                });
            });

            return res.json({
                ec: consts.CODE.SUCCESS,
                data: result
            })
        });
});

// Middleware for checking data
function validate(req, res, next) {
    let _id = req.params._id;

    if(!validator.isMongoId(_id)) {
        return res.json({
            ec: consts.CODE.ERROR,
            error: util.inspect((new TypeError('_id must be an ObjectId')))
        });
    }

    req.query = _.merge(_.cloneDeep(queryFormat), req.query);

    let page = req.query.page;
    if(!validator.isInt(page, {min: 0})) {
        req.query.page = 0;
    } else {
        req.query.page = _.toInteger(page);
    }

    next();
}

function handleMoreFields(req, res, next) {
    let more = req.query.forceMore;
    let moreFields = more.split(',');

    let temp = [];
    moreFields.concat(defaultFields).forEach(field => {
        if(field) {
            temp.push(field);
        }
    });

    req.meotrics_moreFields = temp;

    next();
}

function getConverter(req, res, next) {
    let moreFields = req.meotrics_moreFields;

    globalVariables.get('converter').toIDs([...moreFields, ...fieldsForWork], ids => {
        req.meotrics_converters = ids;
        return next();
    });
}

module.exports = function (app) {
    app.use('/', router);
};
