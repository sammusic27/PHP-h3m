var FileParser = require('./lib/fileParser');
var MapParser = require('./lib/mapParser');
var DefParser = require('./lib/defParser');
var fs = require('fs');

// map parser example
// var map = 'Ascension.h3m';
// var mapOutput = 'ascension.json';
// FileParser('../game/maps/' + map, function(data, fileSize){
//   var mapData = MapParser(data, fileSize);
//
//   fs.writeFile('./data/' + mapOutput, JSON.stringify(mapData), function (err) {
//     if (err) throw err;
//     console.log('File "' + mapOutput + '" has been saved!');
//   });
// });

// def parser example
var def = 'ADOPB1B.DEF';
var defOutput = 'ADOPB1B.DEF.json';
FileParser('../game/defs/' + def, function(data, fileSize){
  var defData = DefParser(data, fileSize);

  fs.writeFile('./data/defs/' + defOutput, JSON.stringify(defData), function (err) {
    if (err) throw err;
    console.log('File "' + defOutput + '" has been saved!');
  });
}, true);
