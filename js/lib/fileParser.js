var fs = require('fs'),
  zlib = require('zlib');

var FileParser = function( file, callback, archive){
  var archive = archive || false;

  var stats = fs.statSync(file)
  var fileSizeInBytes = stats["size"];

  fs.readFile(file, function (err, data) {
    if(archive){
      zlib.gunzip(data, function(err, buffer) {
        if (!err) {
          return callback(buffer);
        }
      });
    }else{
      return callback(data, fileSizeInBytes);
    }
  });
}

module.exports = FileParser;
