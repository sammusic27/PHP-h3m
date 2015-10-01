var fs = require('fs'),
  zlib = require('zlib');

module.exports = function FileParser( file , callback){
  fs.readFile(file, function (err, data) {
    zlib.gunzip(data, function(err, buffer) {
      if (!err) {
        return callback(buffer);
      }
    });
  });
}
