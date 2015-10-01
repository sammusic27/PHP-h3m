var FileParser = require('./lib/parser');
var MapParser = require('./lib/mapParser');

var file = '../Ascension.h3m';
FileParser(file, function(data){
  console.log(MapParser(data));
});

var file = '../[SAM]ResourceBattle.h3m';
FileParser(file, function(data){
  console.log(MapParser(data));
});
