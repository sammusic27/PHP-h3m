var FileParser = require('./lib/parser');
var MapParser = require('./lib/mapParser');

var file = '../maps/Ascension.h3m';
FileParser(file, function(data){
  console.log(MapParser(data));
});

var file = '../maps/[SAM]ResourceBattle.h3m';
FileParser(file, function(data){
  console.log(MapParser(data));
});
