var fs = require('fs'),
  zlib = require('zlib');

var FileParser = function( file, callback, arch){
  var arch = arch || false;

  var stats = fs.statSync(file)
  var fileSizeInBytes = stats["size"];

  fs.readFile(file, function (err, data) {
    if(arch){
      return callback(data, fileSizeInBytes);
    }else{
      zlib.gunzip(data, function(err, buffer) {
        if (!err) {
          return callback(buffer);
        }
      });
    }
  });
}

module.exports = FileParser;
