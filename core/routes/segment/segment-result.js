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

let allFields = [
    '_id',
    '_typeid',
    '_isUser',
    '_segments',
    '_mtid'
];

let bodyFormat = {
    type: '',
    inf: {
        _typeid: '',
        oper: '',
        fieldName: '',
        value: ''
    }
};

// filter segment in action type
router.post('/segment-result/:_appid/:_id', validate, getConverter, function(req, res, next){
    let _appid = req.params._appid;
    let _id = req.params._id;
    let type = req.body.type;
    let inf = req.body.inf;

    if(type === 'action') {

        let queryAction = mongoUtils.translateOperator(req.meotrics_converters[inf.fieldName], inf.oper, inf.value);
        queryAction[req.meotrics_converters['_typeid']] = inf._typeid;

        let match1 = {
            $match: {
                $or: [
                    {
                        [req.meotrics_converters['_isUser']]: true,
                        [req.meotrics_converters['_segments']]: {
                            $in: [new mongodb.ObjectID(_id)]
                        }
                    },
                    queryAction
                ]
            }
        };


        let project1 = {
            $project: {
                [req.meotrics_converters['_mtid']]: 1,
                [req.meotrics_converters[inf.fieldName]]: 1,
                [req.meotrics_converters['_isUser']]: {
                    $ifNull: [0, 1]
                },
                [req.meotrics_converters['_id']]: 0
            }
        }

        let group1 = {
            $group: {
                _id: {
                    _mtid: `$${req.meotrics_converters['_mtid']}`,
                    [inf.fieldName]: `$${req.meotrics_converters[inf.fieldName]}`
                },
                _isUser: {
                    $sum: `$${req.meotrics_converters['_isUser']}`
                }
            }
        }

         let match2 = {
            $match: {
                _isUser: {
                    $eq: 0
                }
            }
        }

        let group2 = {
            $group: {
                _id: `$_id.${inf.fieldName}`,
                count: {$sum: 1}
            }
        }

        let sort1 = {
            $sort: {
                count: -1
            }
        }

        let project2 = {
            $project: {
                key: "$_id",
                count: 1,
                _id: 0,
            }
        };

        let collection = config.mongod.prefix + 'app' + _appid;

        globalVariables
            .get('db')
            .collection(collection)
            .aggregate([match1, project1, group1, match2, group2, sort1, project2])
            .toArray( (err, result) => {
                if (err) {
                    return res.json({
                        ec: consts.CODE.ERROR,
                        error: util.inspect(err)
                    });
                }

                res.json({
                    ec: consts.CODE.SUCCESS,
                    data: {
                        string: result.splice(0, 10),
                        number: handleNumberCase(result, inf.fieldName)
                    }
                });
            });
    } else {
        res.json({
            ec: consts.CODE.FAIL,
            reason: `not supported yet for type: ${type}`
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

    next();
}

function handleNumberCase(data, fieldName) {
    let numbers = [];
    data.forEach(e => {
        let value = e.key;
        if(_.isNumber(value)) {
            numbers.push(e);
        }
    });

    if(numbers.length === 0) {
        return [];
    }

    let max = _.maxBy(numbers, element => {
        return element.key;
    }).key;

    let min = _.minBy(numbers, element => {
        return element.key;
    }).key;

    let ranges = segmentUtils.calculateRange(min, max, fieldName);
    let distance = (max - min) / ranges.length;

    if(distance === 0) {
        ranges[0].count = numbers[0].count;
    } else {
        numbers.forEach(e => {
            let value = e.key;
            let index = Math.floor((value - min) / distance);
            if(index === ranges.length) {
                index--;
            }
            ranges[index].count += e.count;
        });
    }

    return ranges;
}

function getConverter(req, res, next) {
    let fieldName = req.body.inf.fieldName;

    globalVariables.get('converter').toIDs([fieldName, ...allFields], ids => {
        req.meotrics_converters = ids;
        return next();
    });
}

module.exports = function (app) {
    app.use('/', router);
};
