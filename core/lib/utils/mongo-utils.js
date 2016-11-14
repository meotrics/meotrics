const regesc = require('escape-string-regexp');

module.exports = {
    translateOperator: (fieldName, oper, value) => {
        var query = {
            [fieldName]: {
            }
        };

        switch (oper) {
            case 'gt':
                query[fieldName]['$gt'] = value;
                break;
            case 'lt':
                query[fieldName]['$lt'] = value;
                break;
            case 'eq':
                query[fieldName]['$eq'] = value;
                break;
            case 'ne':
                query[fieldName]['$ne'] = value;
                break;
            case 'gte':
                query[fieldName]['$gte'] = value;
                break;
            case 'lte':
                query[fieldName]['$lte'] = value;
                break;
            case 'con':
                query[fieldName]['$regex'] = value;
                break;
            case 'ncon':
                query[fieldName]['$regex'] = "^((?!" + regesc(value) + ").)*$/";
                break;
            case 'sw':
                query[fieldName]['$regex'] = '^' + regesc(value);
                break;
            case 'ew':
                query[fieldName]['$regex'] = regesc(value) + '$';
                break;
            default:
                query = {};
                break;
        }

        return query;
    },
    generateProjection: (converters, returnFields, restrictFields = []) => {
        let projection = {};

        returnFields.forEach(field => {
            projection[converters[field]] = 1;
        });
        restrictFields.forEach(field => {
            projection[converters[field]] = 0;
        });

        return projection;
    }
}