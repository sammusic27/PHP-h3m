var fs = require('fs');
var Q = require('q');
var path = require("path");

module.exports = function (fileName) {
  var pathToFile = path.join(__dirname, '../data/' + fileName);
  var deferred = Q.defer();
  fs.readFile(pathToFile, 'utf8', function (err, data) {
    if (err) {
      deferred.reject(err);
    } else {
      deferred.resolve({data: JSON.parse(data)});
    }
  });
  return deferred.promise;
};
