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

const router = express.Router();

// filter segment in action type
router.post('/segment-result/:appid/:_id', validate, function(req, res, next){
    const appid = req.params.appid;
    const _id = req.params._id;
    const type = req.body.type;
    const inf = req.body.inf;

    if(type === 'action') {
        const queryAction = mongoUtils.translateOperator(inf.fieldName, inf.oper, inf.value);
        queryAction._typeid = inf._typeid;

        const match1 = {
            $match: {
                $or: [
                    {
                        _isUser: true,
                        _segments: {
                            $in: [new mongodb.ObjectID(_id)]
                        }
                    },
                    queryAction
                ]
            }
        };


        const project1 = {
            $project: {
                _mtid: 1,
                [inf.fieldName]: 1,
                _isUser: {
                    $ifNull: [0, 1]
                },
                _id: 0
            }
        }

        const group1 = {
            $group: {
                _id: {
                    _mtid: '$_mtid',
                    [inf.fieldName]: `$${inf.fieldName}`
                },
                _isUser: {$sum: '$_isUser'}
            }
        }

        const match2 = {
            $match: {
                _isUser: {
                    $eq: 0
                }
            }
        }

        const group2 = {
            $group: {
                _id: `$_id.${inf.fieldName}`,
                count: {$sum: 1}
            }
        }

        const sort1 = {
            $sort: {
                count: -1
            }
        }

        const project2 = {
            $project: {
                key: "$_id",
                count: 1,
                _id: 0,
            }
        };

        const collection = config.mongod.prefix + "app" + appid;

        globalVariables
            .get('db')
            .collection(collection)
            .aggregate([match1, project1, group1, match2, group2, sort1, project2])
            .toArray( (err, r) => {
                if (err) {
                    return next(err);
                }

                console.log(r);
                res.json({
                    ec: consts.CODE.SUCCESS,
                    data: {
                        string: r.splice(0, 10),
                        number: handleNumberCase(r, inf)
                    }
                });
            });
    } else {
        res.json({
            ec: consts.CODE.FAIL,
            reason: `not supported yet for ${type}`
        });
    }
});

// Middleware for checking data
function validate(req, res, next) {
    const appid = req.params.appid;
    const _id = req.params._id;
    const type = req.body.type;
    const inf = req.body.inf;

    if(!validator.isMongoId(_id)) {
        res.json({
            ec: consts.CODE.WRONG_PARAM,
            reason: '_id must be an object id'
        });

        return next('route');
    }

    next();
}

function handleNumberCase(data, inf) {
    let r = [];
    let min = undefined;
    let max = undefined;
    const numArray = [];
    data.forEach(e => {
        const value = e.key;
        if(_.isNumber(value)) {
            if(min === undefined) {
                min = value;
                max = value;
            } else if(value < min) {
                min = value;
            } else if(value > max) {
                max = value;
            }

            numArray.push(e);
        }

    });

    if(min !== undefined) {
        r = segmentUtils.calculateRange(min, max);
        const distance = (max - min) / r.length;

        if(distance === 0) {
            console.log(numArray);
           r[0].count = numArray[0].count;
        } else {
            numArray.forEach(e => {
                const value = e.key;
                let index = (value - min) / distance;
                if(index === r.length) {
                    index--;
                }
                r[index].count += e.count;
            });
        }
    }

    return r;
}

module.exports = function (app) {
    app.use('/', router);
};
