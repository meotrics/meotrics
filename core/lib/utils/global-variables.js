'use strict';

const obj = {};

module.exports = {
    set: (key, value) => {
        obj[key] = value;
    },
    get: key => {
        return obj[key];
    }
}