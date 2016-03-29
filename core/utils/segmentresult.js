var config = require('config'),
    mongodb = require('mongodb'),
    MongoClient = mongodb.MongoClient,
    converter = require('../utils/fakeidmanager.js'),
    async = require('async'),
    converter = new converter.IdManager(),
    db = null;

var url = 'mongodb://' + config.get('mongod.host') + ':' + config.get('mongod.port') + '/' + config.get('mongod.database');

module.exports = function (collection, segmentid, field1, type1, field2, type2, callback) {
    MongoClient.connect(url)
        .then(function (database) {
            console.log('[MongoDB] connected');
            db = database;

            groupby(collection, segmentid, field1, type1, field2, type2, callback);

            // Listen for some events
            db.on('reconnect', function (data) {
                console.log(data);
                console.log('[MongoDB] reconnect success');
            });
            db.on('error', function (err) {
                console.log('[MongoDB] error', err.message);
            });
            db.on('close', function (err) {
                console.log('[MongoDB] disconnected');
            });
            // return db.collection('test').find({isUser: true}).toArray();
        }).catch(function (err) {
        console.error("[MongoDB]", err.message);
    });
}

function groupby(collection, segmentid, field1, type1, field2, type2, callback) {
    if (field2 == undefined) {
        if ((type1 == 'string') || (type1 == 'array')) {
            oneFieldString(collection, segmentid, field1, function (err, results) {
                callback(err, results);
            });
        } else {
            oneFieldNumber(collection, segmentid, field1, function (err, results) {
                callback(err, results);
            });
        }
    } else {
        if (type1 == 'string' && type2 == 'string') {
            stringstring(collection, segmentid, field1, field2, function (err, results) {
                callback(err, results);
            });
        } else {
            if (type1 == 'number') {
                if (type2 == 'number') {
                    numbernumber(collection, segmentid, field1, field2, function (err, results) {
                        callback(err, results);
                    });
                } else {
                    numberstring(collection, segmentid, field1, field2, function (err, results) {
                        callback(err, results);
                    });
                }
            } else {
                stringnumber(collection, segmentid, field1, field2, function (err, results) {
                    callback(err, results);
                });
            }
        }
    }

}

function stringnumber(collection, segmentid, field1, field2, callback) {
    converter.toIDs(['_isUser', '_segments', field1, field2], function (ids) {
        var maxfield2;
        var minfield2;

        var query = {};
        var sort = {};
        query[ids['_isUser']] = true;
        sort[ids[field2]] = -1;
        db.collection(collection).find(query).sort(sort).limit(1).toArray().then(function (r) {
            maxfield2 = r[0][ids[field2]];
            sort[ids[field2]] = 1;
            return db.collection(collection).find(query).sort(sort).limit(1).toArray();
        }).then(function (r) {
            minfield2 = r[0][ids[field2]];

            if ((typeof minfield2 == typeof maxfield2) && (typeof minfield2 == 'number')) {
                var matchClause = {"$match": {}};
                matchClause['$match'][ids['_isUser']] = true;
                //matchClause['$match'][ids['_segments']] = new mongodb.ObjectID(segmentid);

                var results2 = range(minfield2, maxfield2, field2);
                var spaces2 = results2.length;

                var prefix2 = "prefix2_";
                var projectClause2 = projectRange(results2, field2, prefix2);
                projectClause2["$project"][field1] = 1;

                var groupClause = {"$group": {}};
                groupClause["$group"]["_id"] = "$"+field1;

                for(var i=0;i<spaces2;i++){
                    groupClause["$group"][prefix2 + i] = {"$sum": "$"+prefix2+i};
                }

                groupClause["$group"]["count"] = {"$sum": 1};

                var cursor = db.collection(collection).aggregate([
                    matchClause,
                    projectClause2,
                    groupClause
                ], {
                    cursor: {batchSize: 20},
                    allowDiskUse: true
                }).toArray().then(function (r) {
                    var results = [];
                    for(var i=0;i< r.length;i++){
                        results[i] = {};
                        results[i].key = r[i]._id;
                        results[i].count = r[i].count;
                        results[i].values = [];
                        for(var j=0;j< spaces2; j++){
                            results[i].values[j] = {};
                            results[i].values[j].key = results2[j].key;
                            results[i].values[j].count = r[i][prefix2+j];
                        }
                    }
                    callback(null, results);
                }).catch(function (e) {
                    callback(e);
                });
            } else {
                callback(new Error('Type data is wrong'));
            }
        });
    });
}

function numberstring(collection, segmentid, field1, field2, callback) {
    converter.toIDs(['_isUser', '_segments', field1, field2], function (ids) {
        var maxfield1;
        var minfield1;

        var query = {};
        var sort = {};
        query[ids['_isUser']] = true;
        sort[ids[field1]] = -1;
        db.collection(collection).find(query).sort(sort).limit(1).toArray().then(function (r) {
            maxfield1 = r[0][ids[field1]];
            sort[ids[field1]] = 1;
            return db.collection(collection).find(query).sort(sort).limit(1).toArray();
        }).then(function (r) {
            minfield1 = r[0][ids[field1]];
            if ((typeof minfield1 == typeof maxfield1) && (typeof minfield1 == 'number')) {
                var matchClause = {"$match": {}};
                matchClause['$match'][ids['_isUser']] = true;
                //matchClause['$match'][ids['_segments']] = new mongodb.ObjectID(segmentid);

                var results1 = range(minfield1, maxfield1, field1);
                var spaces1 = results1.length;

                var prefix1 = "prefix1_";
                var projectClause1 = projectRange(results1, field1, prefix1);
                projectClause1["$project"][field2] = 1;

                var groupClause1 = {"$group": {}};
                var temp = {};
                for (var i = 0; i < spaces1; i++) {
                    temp[prefix1 + i] = "$" + prefix1 + i
                }
                temp[field2] = "$" + field2;
                groupClause1["$group"]["_id"] = temp;
                groupClause1["$group"]["count"] = {"$sum": 1};

                var groupClause2 = {"$group": {}};

                temp = {};
                for (var i = 0; i < spaces1; i++) {
                    temp[prefix1 + i] = "$_id." + prefix1 + i
                }
                groupClause2["$group"]["_id"] = temp;
                groupClause2["$group"]["values"] = {
                    "$push": {
                        "key": "$_id." + field2,
                        "count": "$count"
                    }
                }

                var cursor = db.collection(collection).aggregate([
                    matchClause,
                    projectClause1,
                    groupClause1,
                    groupClause2
                ], {
                    cursor: {batchSize: 20},
                    allowDiskUse: true
                }).toArray().then(function (r) {
                    for (var i = 0; i < r.length; i++) {
                        for (j = 0; j < spaces1; j++) {
                            if (r[i]["_id"][prefix1 + j] == 1) {
                                results1[j].values = r[i].values;
                                results1[j].count = r[i].count;
                            }
                        }
                    }
                    callback(null, results1);
                }).catch(function (e) {
                    callback(e);
                });
            } else {
                callback(new Error('Type data is wrong'));
            }
        });
    });
}

function numbernumber(collection, segmentid, field1, field2, callback) {

    converter.toIDs(['_isUser', '_segments', field1, field2], function (ids) {
        var maxfield1;
        var minfield1;
        var maxfield2;
        var minfield2;

        var query = {};
        var sort = {};
        query[ids['_isUser']] = true;
        sort[ids[field1]] = -1;
        db.collection(collection).find(query).sort(sort).limit(1).toArray().then(function (r) {
            maxfield1 = r[0][ids[field1]];
            sort[ids[field1]] = 1;
            return db.collection(collection).find(query).sort(sort).limit(1).toArray();
        }).then(function (r) {
            minfield1 = r[0][ids[field1]];
            if ((typeof minfield1 == typeof maxfield1) && (typeof minfield1 == 'number')) {
                sort = {};
                sort[ids[field2]] = -1;
                db.collection(collection).find(query).sort(sort).limit(1).toArray().then(function (r) {
                    maxfield2 = r[0][ids[field2]];
                    sort[ids[field2]] = 1;
                    return db.collection(collection).find(query).sort(sort).limit(1).toArray();
                }).then(function (r) {
                    minfield2 = r[0][ids[field2]];

                    if ((typeof minfield2 == typeof maxfield2) && (typeof minfield2 == 'number')) {
                        var matchClause = {"$match": {}};
                        matchClause['$match'][ids['_isUser']] = true;
                        //matchClause['$match'][ids['_segments']] = new mongodb.ObjectID(segmentid);

                        var results1 = range(minfield1, maxfield1, field1);

                        var prefix1 = "prefix1_";
                        var projectClause1 = projectRange(results1, field1, prefix1);


                        var results2 = range(minfield2, maxfield2, field2);
                        var prefix2 = "prefix2_";
                        var projectClause2 = projectRange(results2, field2, prefix2);

                        delete projectClause2["$project"]["_id"];
                        var keys = Object.keys(projectClause2["$project"]);
                        for (var i = 0; i < keys.length; i++) {
                            projectClause1["$project"][keys[i]] = projectClause2["$project"][keys[i]];
                        }

                        var projectClause = projectClause1;

                        var spaces1 = results1.length;
                        var spaces2 = results2.length;

                        var groupClause1 = {"$group": {}};
                        var temp = {};
                        for (var i = 0; i < spaces1; i++) {
                            temp[prefix1 + i] = "$" + prefix1 + i
                        }
                        //console.log(temp);
                        groupClause1["$group"]["_id"] = temp;

                        for (var i = 0; i < spaces2; i++) {
                            groupClause1["$group"][prefix2 + i] = {"$sum": "$" + prefix2 + i};
                        }

                        groupClause1["$group"]["count"] = {"$sum": 1};

                        var cursor = db.collection(collection).aggregate([
                            matchClause,
                            projectClause,
                            groupClause1
                        ], {
                            cursor: {batchSize: 20},
                            allowDiskUse: true
                        }).toArray().then(function (r) {
                            var results = [];

                            for (var i = 0; i < r.length; i++) {
                                for (var j = 0; j < spaces1; j++) {
                                    if (r[i]["_id"][prefix1 + j] == 1) {

                                        results[j] = {};
                                        results[j].key = results1[j].key;
                                        results[j].count = r[i].count;
                                        results[j].values = [];
                                        for (var k = 0; k < spaces2; k++) {
                                            results[j].values[k] = {};
                                            results[j].values[k].key = results2[k].key;
                                            results[j].values[k].count = r[i][prefix2 + k];
                                        }
                                        break;
                                    }
                                }
                            }
                            for (var i = 0; i < spaces1; i++) {
                                if (results[i] == null) {
                                    results[i] = {};
                                    results[i].key = results1[i].key;
                                    results[i].count = results1[i].count;
                                }
                            }
                            callback(null, results);
                        }).catch(function (e) {
                            callback(e);
                        });

                    } else {
                        callback(new Error('Type data is wrong'));
                    }
                });
            } else {
                callback(new Error('Type data is wrong'));
            }
        });
    });
}

function stringstring(collection, segmentid, field1, field2, callback) {
    converter.toIDs(['_isUser', '_segments', field1, field2], function (ids) {
        var matchClause = {"$match": {}};
        matchClause['$match'][ids['_isUser']] = true;
        //matchClause['$match'][ids['_segments']] = new mongodb.ObjectID(segmentid);

        var projectClause = {"$project": {}};
        projectClause['$project']['_id'] = 0;
        projectClause['$project'][ids[field1]] = 1;
        projectClause['$project'][ids[field2]] = 1;

        var groupClause1 = {"$group": {}};
        groupClause1["$group"]["_id"] = {
            "field1": "$" + field1,
            "field2": "$" + field2
        };
        groupClause1["$group"]["count"] = {"$sum": 1};

        var groupClause2 = {"$group": {}};
        groupClause2["$group"]["_id"] = "$_id.field1";

        groupClause2["$group"]["values"] = {
            "$push": {
                "key": "$_id.field2",
                "count": "$count"
            }
        }
        groupClause2["$group"]["count"] = {"$sum": "$count"};

        var cursor = db.collection(collection).aggregate([
            matchClause,
            projectClause,
            groupClause1,
            groupClause2
        ], {
            cursor: {batchSize: 20},
            allowDiskUse: true
        }).toArray().then(function (r) {
            callback(null, r);
        }).catch(function (e) {
            callback(e);
        });
    });
}

function oneFieldString(collection, segmentid, field1, callback) {
    converter.toIDs(['_isUser', '_segments', field1], function (ids) {
        var matchClause = {"$match": {}};
        matchClause['$match'][ids['_isUser']] = true;
        //matchClause['$match'][ids['_segments']] = new mongodb.ObjectID(segmentid);

        var projectClause = {"$project": {}};
        projectClause['$project']['_id'] = 0;
        projectClause['$project'][ids[field1]] = 1;

        var groupClause = {"$group": {}};
        groupClause['$group']['_id'] = '$' + ids[field1];
        groupClause['$group']['count'] = {'$sum': 1};

        var cursor = db.collection(collection).aggregate([
            matchClause,
            projectClause,
            groupClause
        ], {
            cursor: {batchSize: 20},
            allowDiskUse: true
        });

        var results = [];
        var done = false;
        async.whilst(
            function () {
                return done == false;
            },
            function (callback) {
                cursor.next().then(function (r) {
                    if (r) {
                        var element = {};
                        element.key = r._id;
                        element.count = r.count;
                        results.push(element);
                    } else {
                        done = true;
                    }
                    callback(null, results);
                }).catch(function (e) {
                    callback(e);
                });
            }, function (err, results) {
                callback(err, results);
            }
        );
    });
}

function oneFieldNumber(collection, segmentid, field1, callback) {
    converter.toIDs(['_isUser', '_segments', field1], function (ids) {
        var maxfield1;
        var minfield1;

        var query = {};
        var sort = {};
        query[ids['_isUser']] = true;
        sort[ids[field1]] = -1;
        db.collection(collection).find(query).sort(sort).limit(1).toArray().then(function (r) {
            maxfield1 = r[0][ids[field1]];
            sort[ids[field1]] = 1;
            return db.collection(collection).find(query).sort(sort).limit(1).toArray();
        }).then(function (r) {
            minfield1 = r[0][ids[field1]];

            if ((typeof minfield1 == typeof maxfield1) && (typeof minfield1 == 'number')) {
                var matchClause = {"$match": {}};
                matchClause['$match'][ids['_isUser']] = true;
                //matchClause['$match'][ids['_segments']] = new mongodb.ObjectID(segmentid);

                var results = range(minfield1, maxfield1, field1);

                var prefix = "range_"
                var projectClause = projectRange(results, field1, prefix);

                var groupClause = {'$group': {}};
                groupClause['$group']['_id'] = null;
                var length = results.length;

                for (var i = 0; i < length; i++) {
                    var fieldName = "range_" + i;
                    groupClause['$group'][fieldName] = {'$sum': '$' + fieldName};
                }

                var cursor = db.collection(collection).aggregate([
                    matchClause,
                    projectClause,
                    groupClause
                ], {
                    cursor: {batchSize: 20},
                    allowDiskUse: true
                }).toArray().then(function (r) {
                    var temp = r[0];
                    console.log(temp);
                    for (var i = 0; i < length; i++) {
                        var fileName = prefix + i;
                        results[i].count = temp[fileName];
                    }
                    callback(null, results);
                }).catch(function (e) {
                    callback(e);
                });


            } else {
                callback(new Error('Type data is wrong'));
            }
        });
    });
}

function range(min, max, field) {
    var results = [];
    if (field == 'age') {
        results[0] = {
            count: 0,
            key: {
                to: 18
            }
        };
        results[1] = {
            count: 0,
            key: {
                from: 18,
                to: 24
            }
        };
        results[2] = {
            count: 0,
            key: {
                from: 24,
                to: 34
            }
        };
        results[3] = {
            count: 0,
            key: {
                from: 34,
                to: 44
            }
        };
        results[4] = {
            count: 0,
            key: {
                from: 44,
                to: 54
            }
        };
        results[5] = {
            count: 0,
            key: {
                from: 54
            }
        };
    } else {
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

function projectRange(results, field1, prefix) {
    var projectClause = {'$project': {}};
    var length = results.length;

    projectClause['$project']['_id'] = 0;
    for (var i = 0; i < length; i++) {
        var temp = results[i];
        var fieldName = prefix + i;
        var element = {'$cond': []};
        var booleanExpression = {};
        if (temp.key.from != undefined) {
            booleanExpression['$gt'] = [];
            booleanExpression['$gt'].push('$' + field1);
            booleanExpression['$gt'].push(temp.key.from);
            if (i == 0) {
                booleanExpression['$gte'] = booleanExpression['$gt'];
                delete booleanExpression['$gt'];
            }
        }

        if (temp.key.to != undefined) {
            var bool2 = {};
            bool2['$lte'] = [];
            bool2['$lte'].push('$' + field1);
            bool2['$lte'].push(temp.key.to);

            if ((booleanExpression['$gt'] != undefined) || (booleanExpression['$gte'] != undefined)) {
                var bool1 = booleanExpression;
                booleanExpression = {"$and": []};
                booleanExpression["$and"].push(bool1);
                booleanExpression["$and"].push(bool2);
            } else {
                booleanExpression = bool2;
            }
        }

        element['$cond'].push(booleanExpression);
        element['$cond'].push(1);
        element['$cond'].push(0);
        projectClause['$project'][fieldName] = element;

    }
    return projectClause;
}