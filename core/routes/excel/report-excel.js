'use strict';

const globalVariables = require('../../lib/utils/global-variables.js');
const mongoUtils = require('../../lib/utils/mongo-utils.js');
const express = require('express');
const consts = require('../../lib/consts/consts.js');
const _ = require('lodash');
const config = require('config');
const validator = require('validator');
const mongodb = require('mongodb');
const async = require('async');
let util = require('util');
let fs = require('fs');
const router = express.Router();
var Excel = require('exceljs');

const defaultUserFields = ['name', 'email'];
const fieldsForWork = ['_segments', '_ctime', '_mtid', '_isUser', '_typeid']
// filter segment in action type
router.post('/report-excel/:appid/:segmentId', validate, getConverter, function(req, res, next){
  let appid = req.params.appid;
  let segmentId = req.params.segmentId;

  let collection = config.mongod.prefix + 'app' + appid;

  let match = {
    $match: {
        $or: [
            {
                [req.meotrics_converters['_isUser']]: true,
                [req.meotrics_converters['_segments']]: {
                  $in: [new mongodb.ObjectID(segmentId)]
                }
            },
            generateQueryAction(req)
        ]
    }
  }

  let group = {
    $group: generateGroup(req)
  }



  console.log(JSON.stringify(match), group, collection)

  globalVariables
      .get('db')
      .collection(collection)
      .aggregate([match, group])
      .toArray( (err, result) => {
          if (err) {
              return res.json({
                  ec: consts.CODE.ERROR,
                  error: util.inspect(err)
              });
          }

          generateExcel(req, res, result);
      });



});

function generateExcel(req, res, data) {

  var options = {
    filename: './streamed-workbook.xlsx',
    useStyles: true,
    useSharedStrings: true
  };
  var workbook = new Excel.stream.xlsx.WorkbookWriter(options);
  var worksheet = workbook.addWorksheet('My Sheet', {properties:{tabColor:{argb:'FFC0000'}}});
  let actionFields = req.body.actionFields.map(value => {
    return value.field;
  }) || [];

  let columns = []
  actionFields.push('_id')
  actionFields.concat(req.body.userFields).forEach(value => {
    columns.push({
      header: value,
      key: value,
      width: 50
    })
  })

  worksheet.columns = columns;
  console.log(columns);
  data.forEach(row => {
    let obj = {};
    actionFields.concat(req.body.userFields).forEach(key => {
      obj[key] = Array.isArray(row[key]) ? row[key].join('\n') : row[key]
    });
    console.log(obj);
    worksheet.addRow(obj).commit();
  });


  worksheet.commit();
  // Finished the workbook.
  workbook.commit()
    .then(function() {
      // the stream has been written
      res.end();
    });
}

// Middleware for checking data
function validate(req, res, next) {
  let appid = req.params.appid;
  let segmentId = req.params.segmentId;
  let time = req.body.time;
  let userFields = req.body.userFields;
  let actionFields = req.body.actionFields;

  if(_.isInteger(appid)
  || !validator.isMongoId(segmentId)) {
    return res.json({
      ec: consts.CODE.WRONG_PARAM,
      message: `appid must be an integer\nsegmentId must be an object id`
    });
  }

  let timeTemp = {
    from: Date.now() - 86400000,
    to: Date.now()
  }

  if(_.isPlainObject(time)) {
    req.body.time = _.merge(timeTemp, time);
  } else {
    req.body.time = timeTemp;
  }

  if(!Array.isArray(userFields)) {
    userFields = []
  }

  if(!Array.isArray(actionFields)) {
    actionFields = []
  }

  req.body.userFields = filterValidFields(userFields).concat(defaultUserFields);
  req.body.actionFields = actionFields;

  next()
}

function getConverter(req, res, next) {
    let fieldsAction = req.body.actionFields.map(value => {
      return value.field;
    })

    globalVariables.get('converter').toIDs([
      ...defaultUserFields,
      ...fieldsForWork,
      ...req.body.userFields,
      ...fieldsAction
    ], ids => {
        req.meotrics_converters = ids;
        return next();
    });
}

function filterValidFields(values) {
  let temp = [];
  values.forEach(value => {
    if(_.isString(value)) {
      temp.push(value);
    }
  });
  return temp;
}

function generateQueryAction(req) {
  let actionFields = req.body.actionFields || [];

  let typesAction = actionFields.map(value => {
    return value.type;
  })

  return {
    [req.meotrics_converters['_typeid']]: {
      $in: typesAction || []
    }
  }
}

function generateGroup(req) {
  let group = {
    _id: `$${req.meotrics_converters['_mtid']}`
  }
  let fieldsAction = req.body.actionFields.map(value => {
    return value.field;
  })

  fieldsAction.concat(req.body.userFields).forEach(value => {
    group[value] = {
      $addToSet: `$${req.meotrics_converters[value]}`
    }
  })

  return group;
}

module.exports = function (app) {
    app.use('/', router);
};
