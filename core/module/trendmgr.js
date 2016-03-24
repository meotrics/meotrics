exports.TrendMgr = function (db, mongodb, async, converter, prefix, mtthrow, col){

    this.query = function (req, res) {
        var appid = req.params.appid;
        var collection = prefix + col;
        var results = [];
        async.waterfall([
            function (callback) {
                var trid = req.params.id;
                db.collection(collection).find({_id: new mongodb.ObjectID(trid)}, {_id: 0}).toArray()
                    .then(function (r) {
                        callback(null, r[0], converter);
                    }).catch(function (e) {
                    callback(e);
                });
            }, getQueryTrending
            , function (query, callback) {
                collection = prefix + appid;

                db.collection(collection).aggregate(query).toArray()
                    .then(function (array) {
                        results = array;
                        async.forEachOf(results, function (value, key, asyncCallback) {
                            converter.toOriginal(value.temp)
                                .then(function (r) {
                                    results[key].temp = r;
                                    asyncCallback(null);
                                }).catch(function (e) {
                                asyncCallback(e);
                            });
                        }, function (err) {
                            if (err) {
                                callback(err);
                            } else {
                                res.json(results);
                                callback(null);
                            }
                        });
                    }).catch(function (err) {
                    callback(err);
                });
            }
        ], mtthrow);
    };

    function getQueryTrending(object, converter, callback) {
        var query = [];
        converter.toID(object.object)
            .then(function (r) {
                object.object = r;

                // -- START MATCH CLAUSE
                query.push({$match: {_typeid: new mongodb.ObjectID(object.typeid)}});

                if (object._segment != undefined) {
                    query[0]['$match']['_segments'] = {
                        '$in': [object._segment]
                    };
                }

                if (object.startTime != undefined) {
                    query[0]['$match']['_ctime'] = {
                        $gte: object.startTime
                    };
                }

                if (object.endTime != undefined) {
                    if (query[0]['$match']['_ctime'] != undefined) {
                        query[0]['$match']['_ctime']['$lte'] = object.endTime;
                    } else {
                        query[0]['$match']['_ctime'] = {
                            $lte: object.endTime
                        };
                    }
                }
                // -- END OF MATCH CLAUSE

                // -- START GROUP FUNCTION
                object.order = object.order || 1;

                if (object.operation == 'count') {
                    query.push({$group: {_id: '$' + object.object, count: {'$sum': 1}, temp: {$first: "$$ROOT"}}});
                    query.push({$sort: {count: object.order}});

                    object.limit = object.limit || 10;
                    query.push({$limit: object.limit});

                    converter.toObject(query[0]['$match'])
                        .then(function (r) {
                            query[0]['$match'] = r;
                            callback(null, query);
                        }).catch(function (e) {
                        callback(e);
                    });
                } else {
                    converter.toID(object.param)
                        .then(function (r) {
                            object.param = r;

                            if (object.operation == 'avg') {
                                query.push({
                                    $group: {
                                        _id: '$' + object.object,
                                        result: {'$avg': '$' + object.param},
                                        temp: {$first: "$$ROOT"}
                                    }
                                });
                                query.push({$sort: {result: object.order}});
                            } else if (object.operation == 'sum') {
                                query.push({
                                    $group: {
                                        _id: '$' + object.object,
                                        result: {'$sum': '$' + object.param},
                                        temp: {$first: "$$ROOT"}
                                    }
                                });
                                query.push({$sort: {result: object.order}});
                            }

                            object.limit = object.limit || 10;
                            query.push({$limit: object.limit});

                            return converter.toObject(query[0]['$match']);
                        }).then(function (r) {
                        query[0]['$match'] = r;
                        callback(null, query);
                    }).catch(function (e) {
                        callback(e);
                    });
                }
            }).catch(function (e) {
            callback(e);
        });
    }
}