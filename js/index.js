var FileParser = require('./lib/fileParser');
var MapParser = require('./lib/mapParser');
var DefParser = require('./lib/defParser');
var fs = require('fs');

// map parser example
// var file = 'Ascension.h3m';
// var fileOutput = 'ascension.json';
// FileParser('../game/maps/' + file, function(data){
//   var mapData = MapParser(data);
//
//   fs.writeFile('./data/' + fileOutput, JSON.stringify(mapData), function (err) {
//     if (err) throw err;
//     console.log('File "' + fileOutput + '" has been saved!');
//   });
// });

// def parser example
var file = 'CLRRVR.DEF';
var fileOutput = 'CLRRVR.def.json';
FileParser('../game/defs/' + file, function(data){
  var defData = DefParser(data);

  fs.writeFile('./data/defs/' + fileOutput, JSON.stringify(defData), function (err) {
    if (err) throw err;
    console.log('File "' + fileOutput + '" has been saved!');
  });
}, true);
