'use strict';

const fs = require('fs');
const list = [];

fs
    .readdirSync(__dirname)
    .filter(file => {
        return file !== 'index.js';
    })
    .forEach(folder => {
        list.push(require('./'+folder));
    });

module.exports = function(app) {
    list.forEach(func => {
        func(app);
    });
}
