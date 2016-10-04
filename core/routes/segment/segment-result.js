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

const router = express.Router();

// filter segment in action type
router.post('/segment-result/:appid/:_id', validate, function(req, res, next){
    const appid = req.params.appid;
    const _id = req.params._id;
    const type = req.body.type;
    const inf = req.body.inf;

    if(type === 'action') {
        // const queryAction = mongoUtils.translateOperator(inf.fieldName, inf.oper, inf.value);
        const queryAction = {};
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
        }

        const project = {
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
                _isUser: 0
            }
        }

        const group2 = {
            $group: {
                _id: `$_id.${inf.fieldName}`,
                count: {$sum: 1}
            }
        }

        const sort = {
            $sort: {
                count: -1
            }
        }

        const collection = config.mongod.prefix + "app" + appid;

        globalVariables.get('db').collection(collection).aggregate([match1, project, group1, match2, group2, sort]).toArray(function (err, r) {
            if (err) {
                return next(err);
            }

            console.log(r);
            res.json({
                ec: consts.CODE.SUCCESS,
                data: {
                    string: r.splice(0, 10),
                    number: {}
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

}

function range(min, max, field) {
    var results = [];
    //if field is age then split into [18, 24, 24, 44, 54, 54+]
    if (field == 'age') {
        results[0] = {count: 0, key: {from: 0, to: 18}};
        results[1] = {count: 0, key: {from: 18, to: 24}};
        results[2] = {count: 0, key: {from: 24, to: 34}};
        results[3] = {count: 0, key: {from: 34, to: 44}};
        results[4] = {count: 0, key: {from: 44, to: 54}};
        results[5] = {count: 0, key: {from: 54}};
        results[6] = {count: 0, key: {to: 0}};
    } else {
        //else split in to 5 equal space using min, max
        var spaces = 1;
        var distance = 0;

        if (max - min >= 5) {
            spaces = 5;
            distance = Math.floor((max - min) / 5);
        }

        for (var i = 0; i < spaces; i++) {
            var element = {};
            element.key = {
                from: min + i * distance,
                to: min + (i + 1) * distance
            };
            if (i == spaces - 1) {
                element.key.to = max;
            }
            element.count = 0;
            results.push(element);
        }
    }
    return results;
}


module.exports = function (app) {
    app.use('/', router);
};
