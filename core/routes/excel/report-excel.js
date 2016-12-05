'use strict';

const shortid = require('shortid')
const globalVariables = require('../../lib/utils/global-variables.js');
const mongoUtils = require('../../lib/utils/mongo-utils.js');
const express = require('express');
const consts = require('../../lib/consts/consts.js');
const _ = require('lodash');
const config = require('config');
const validator = require('validator');
const mongodb = require('mongodb');
const async = require('async');
const util = require('util');
const fs = require('fs');
const router = express.Router();
const Excel = require('exceljs');

const defaultUserFields = ['name', 'email','phone',"_ctime"];
const fieldsForWork = ['_segments', '_ctime', '_mtid', '_isUser', '_typeid'];
const mappingFields = {
  _id: '_mtid'
}
// filter segment in action type
router.post('/report-excel/:appid/:segmentId', validate, getConverter, function(req, res, next){
  let appid = req.params.appid;
  let segmentId = req.params.segmentId;

  let collection = config.mongod.prefix + 'app' + appid;
  generateQueryAction(req,function(queryAction){
    let match = {
      $match: {
        $or: [
          {
            [req.meotrics_converters['_isUser']]: true,
            [req.meotrics_converters['_segments']]: {
              $in: [new mongodb.ObjectID(segmentId)]
            },
            [req.meotrics_converters['_ctime']]: {
              $gt: req.body.time.from,
              $lt: req.body.time.to
            }
          },
          queryAction
        ]
      }
    }



  let group = {
    $group: generateGroup(req)
  }


  globalVariables
      .get('db')
      .collection(collection)
      .aggregate([match])
      .toArray( (err, result) => {
          if (err) {
              return res.json({
                  ec: consts.CODE.ERROR,
                  error: util.inspect(err)
              });
          }
          generateExcel(req, res, result);
      });


  })
});

function generateExcel(req, res, data) {
  let idFile = shortid.generate();

  let options = {
    // filename: `./public/${idFile}.xls`,
    filename: `./export/${idFile}.xls`,
    useStyles: true,
    useSharedStrings: true
  };

  let workbook = new Excel.stream.xlsx.WorkbookWriter(options);
  let worksheet = workbook.addWorksheet('Report', {properties:{tabColor:{argb:'FFC0000'}}});

  let actionFields = req.body.actionFields.map(value => {
    return value.field;
  }) || [];
  let totalFields = actionFields.concat(req.body.userFields).concat(['_id']);
  totalFields.sort((e1, e2) => {
    e1 = mappingFields[e1] ? mappingFields[e1] : e1;
    e2 = mappingFields[e2] ? mappingFields[e2] : e2;

    if(e1 === '_mtid') {
      return false;
    }

    if(e2 === '_mtid') {
      return true;
    }

    if(req.body.userFields.indexOf(e1) !== -1) {
      return false;
    }
    return true;
  });
  totalFields.push("_mtid");


  let columns = []
  totalFields.forEach(value => {
    columns.push({
      header: value,
      key: value,
      width: 50
    })
  })

  worksheet.columns = columns;
  for(let i=0;i<totalFields.length;i++) {
    // worksheet.getColumn(3).outlineLevel = 1;
  }
  // data.forEach(row => {
  //   let obj = {};
  //   totalFields.forEach(key => {
  //     if(key == "_ctime" || key == "_lastSeen"){
  //         var time =  new Date(row[key]*1000);
  //         obj[key] = time.toLocaleString();
  //     }else if(key == "_id" || key == "_mtid"){
  //         var strkey = String(row[key]);
  //         obj[key] = strkey;
  //     }
  //     else{
  //     obj[key] = Array.isArray(row[key]) ? row[key].join(`\n`) : row[key];
  //     }
  //   });
  //   worksheet.addRow(obj).commit();
  // });
  data.forEach(row => {
    console.log(row)
    let obj = {};
    totalFields.forEach(key => {
      if(key == "_ctime" || key == "_lastSeen"){
      var time =  new Date(row[key]*1000);
      obj[key] = time.toLocaleString();
    }else if(key == "_id" || key == "_mtid"){
      var strkey = String(row[key]);
      obj[key] = strkey;
    }
    else{
      obj[key] = Array.isArray(row[key]) ? row[key].join(`\n`) : row[key];
    }
  });
    worksheet.addRow(obj).commit();
});


  worksheet.commit();
  // Finished the workbook.
  workbook.commit()
    .then(function() {
      res.json({
        ec: consts.CODE.SUCCESS,
        id: idFile
      });
    })
    .catch((err) => {
      console.log(err);
      res.json({
        ec: consts.CODE.ERROR
      });
    })
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
    }) || [];

    globalVariables.get('converter').toIDs([
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

function getListMtidUser(req,callback){
  let appid = req.params.appid;
  let segmentId = req.params.segmentId;
  let segments = config.mongod.prefix + 'segment' + segmentId;
      globalVariables
      .get('db')
      .collection(segments).find({value:1},{value:0}).toArray(function(err,docs){
        callback(docs);
      });
}

function generateQueryAction(req,callback) {
  getListMtidUser(req,function(listuser){
    let actionFields = req.body.actionFields;
    let typesAction = actionFields.map(value => {
          return value.type;
    })
      var val = {
        [req.meotrics_converters['_typeid']]: {
          $in: typesAction || []
        },
        [req.meotrics_converters['_mtid']]:{
          $in: listuser.map(function(value){
            return new mongodb.ObjectID(value._id);
          })
        },
        [req.meotrics_converters['_ctime']]: {
          $gt: req.body.time.from,
          $lt: req.body.time.to
        }
      };
    callback(val);
  });
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
