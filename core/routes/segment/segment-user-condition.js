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
    '_typeid',
    '_mtid',
    '_isUser',
    '_segments'
];

let defaultFields = [
    '_mtid',
    'email',
    '_lastSeen'
];

let restrictFields = [
    '_id'
];

let bodyFormat = {
    type: '',
    inf: {
        _typeid: '',
        fieldName: '',
        value: '' // '' || {from: , to: }
    }
};

let LIMIT = 10;

router.post('/segment-user-condition/:_appid/:_id', validate, handleMoreFields, getConverter, function(req, res, next){
    let _segmentId = req.params._segmentId;
    let _appid = req.params._appid;
    let type = req.body.type;

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
    let _id = req.params._id;

    if(!validator.isMongoId(_id)) {
        return res.json({
            ec: consts.CODE.ERROR,
            error: util.inspect((new TypeError('_id must be an ObjectId')))
        });
    }

    req.body = _.merge(_.cloneDeep(bodyFormat), req.body);

    console.log(req.body);
    return next();
}

function handleMoreFields(req, res, next) {
    let more = req.query.more || '';
    let moreFields = more.split(',');
    let temp = [];

    moreFields.forEach(field => {
        if(field && defaultFields.indexOf(field) === -1) {
            temp.push(field);
        }
    });

    req.meotrics_moreFields = defaultFields.concat(temp);
    next();
}

function getConverter(req, res, next) {
    let moreFields = req.meotrics_moreFields;
    let field = req.body.inf.fieldName;

    globalVariables.get('converter').toIDs([field, ...moreFields, ...defaultFields, ...fieldsForWork, ...restrictFields], ids => {
        req.meotrics_converters = ids;
        return next();
    });
}

function handleUserField(req, res) {
    let field = req.body.inf.fieldName;
    let value = req.body.inf.value;
    let _appid = req.params._appid;
    let segmentId = req.params._id;
    let collection = config.mongod.prefix + 'app' + _appid;

    if(!checkEnoughCondition(field, value)) {
        return res.json({
            ec: consts.CODE.SUCCESS,
            data: []
        });
    }

    let query = _.merge({
        [req.meotrics_converters['_isUser']]: true,
        [req.meotrics_converters['_segments']]: new mongodb.ObjectID(segmentId)
    }, getOperatorCompare(req.meotrics_converters, field, value));

    let projection = mongoUtils.generateProjection(req.meotrics_converters, [field, ...defaultFields], restrictFields);

    let options = {
        sort: {
            [req.meotrics_converters['_lastSeen']]: -1
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


            return res.json({
                ec: consts.CODE.SUCCESS,
                data: result
            });
        });
}

function getOperatorCompare(converters, field, value) {
    if(_.isPlainObject(value)) {
        return {
            $and: [
                {
                    [converters[field]]: {
                        $gte: value.from
                    }
                },
                {
                    [converters[field]]: {
                        $lte: value.to
                    }
                }
            ]
        };
    } else {
        return {
            [converters[field]]: value
        };
    }
}

function checkEnoughCondition(field, value) {
    if(!field) {
        return false;
    }

    if(_.isPlainObject(value) && (!_.isNumber(value.from) || !_.isNumber(value.to))) {
        return false;
    }

    return true;
}

function handleActionField(req, res) {
    let _typeid = req.body.inf._typeid;
    let field = req.body.inf.fieldName;
    let value = req.body.inf.value;
    let _appid = req.params._appid;
    let segmentId = req.params._id;
    let collection = config.mongod.prefix + 'app' + _appid;

    if(!_typeid || !checkEnoughCondition(field, value)) {
        return res.json({
            ec: consts.CODE.SUCCESS,
            data: []
        });
    }

    let queryAction = _.merge({
        [req.meotrics_converters['_typeid']]: _typeid,
    }, getOperatorCompare(req.meotrics_converters, field, value));

    let match_n1 = {
        $match: {
            $or: [
                {
                    [req.meotrics_converters['_isUser']]: true,
                    [req.meotrics_converters['_segments']]: new mongodb.ObjectID(segmentId)
                },
                queryAction
            ]
        }
    };

    let project_n1 = {
        $project: {
            [req.meotrics_converters['_mtid']]: 1,
            [req.meotrics_converters['_isUser']]: {
                $cond: {
                    if: {
                        $eq: [`$${req.meotrics_converters['_isUser']}`, true]
                    },
                    then: 1,
                    else: 0
                }
            },
            [req.meotrics_converters['_id']]: 0,
            [req.meotrics_converters[field]]: {
                $cond: {
                    if: {
                        $eq: [`$${req.meotrics_converters['_isUser']}`, true]
                    },
                    then: 0,
                    else: 1
                }
            },
            [req.meotrics_converters['_lastSeen']]: {
                $cond: {
                    if: {
                        $eq: [`$${req.meotrics_converters['_isUser']}`, true]
                    },
                    then: `$${req.meotrics_converters['_lastSeen']}`,
                    else: 0
                }
            }
        }
    };

    let group_n1 = {
        $group: {
            _id: `$${req.meotrics_converters['_mtid']}`,
            _isUser: {
                $sum: `$${req.meotrics_converters['_isUser']}`
            },
            _lastSeen: {
                $sum: `$${req.meotrics_converters['_lastSeen']}`
            },
            belong: {
                $sum: `$${req.meotrics_converters[field]}`
            }
        }
    };

    let match_n2 = {
        $match: {
            _isUser: 1,
            belong: {
                $ne: 0
            }
        }
    };

    let sort_n1 = {
        $sort: {
            _lastSeen: 1
        }
    };

    let limit_n1 = {
        $limit: LIMIT
    };

    globalVariables
        .get('db')
        .collection(collection)
        .aggregate([match_n1, project_n1, group_n1, match_n2, limit_n1])
        .toArray( (err, result) => {
            if (err) {
                return res.json({
                    ec: consts.CODE.ERROR,
                    error: util.inspect(err)
                });
            }

            let listId = [];
            result.forEach(rowData => {
                listId.push(rowData._id);
            });

            let query = {
                [req.meotrics_converters['_mtid']]: {
                    $in: listId
                },
                [req.meotrics_converters['_isUser']]: true
            };

            let projection = mongoUtils.generateProjection(req.meotrics_converters, [field, ...req.meotrics_moreFields], restrictFields);

            let options = {
                sort: {
                    [req.meotrics_converters['_lastSeen']]: -1
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

                    res.json({
                        ec: consts.CODE.SUCCESS,
                        data: result
                    });
                });


        });
}

module.exports = function (app) {
    app.use('/', router);
};
