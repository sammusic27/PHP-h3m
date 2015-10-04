var FileParser = require('./lib/fileParser');
var MapParser = require('./lib/mapParser');
var fs = require('fs');

var file = 'Ascension.h3m';
var fileOutput = 'ascension.json';
FileParser('../game/maps/' + file, function(data){
  var mapData = MapParser(data);

  fs.writeFile('./data/' + fileOutput, JSON.stringify(mapData), function (err) {
    if (err) throw err;
    console.log('File "' + fileOutput + '" has been saved!');
  });
});
//
// var file = '../maps/[SAM]ResourceBattle.h3m';
// FileParser(file, function(data){
//   console.log(MapParser(data));
// });
