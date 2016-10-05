'use strict';

module.exports = {
    calculateRange: function (min, max, field = '') {
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
                var element = {
                    key: {
                        from: min + i * distance,
                        to: min + (i + 1) * distance
                    },
                    count: 0
                };

                results.push(element);
            }

            results[spaces-1].key.to = max;
        }
        return results;
    }
}