var FileParser = require('./lib/fileParser');
var MapParser = require('./lib/mapParser');
var DefParser = require('./lib/defParser');
var fs = require('fs');

// map parser example
// var map = 'Southern Cross.h3m';
// var mapOutput = map.toLowerCase() + '.json';
// FileParser('../game/maps/' + map, function(data, fileSize){
//   var mapData = MapParser(data, fileSize);
//
//   fs.writeFile('./data/maps/' + mapOutput, JSON.stringify(mapData), function (err) {
//     if (err) throw err;
//     console.log('File "' + mapOutput + '" has been saved!');
//   });
// }, true);

// def parser example
var def = 'adag.def';
var defOutput = def.toLowerCase() + '.json';
FileParser('../game/defs/' + def, function(data, fileSize){
  var defData = DefParser(data, fileSize);

  fs.writeFile('./data/defs/' + defOutput, JSON.stringify(defData), function (err) {
    if (err) throw err;
    console.log('File "' + defOutput + '" has been saved!');
  });
});

// var def = 'AB01_.def';
// var defOutput = def.toLowerCase() + '.json';
// FileParser('../game/defs/' + def, function(data, fileSize){
//   var defData = DefParser(data, fileSize);
//
//   fs.writeFile('./data/defs/' + defOutput, JSON.stringify(defData), function (err) {
//     if (err) throw err;
//     console.log('File "' + defOutput + '" has been saved!');
//   });
// });

// def parser example
// var def2 = 'ADOPB1B.DEF';
// var defOutput2 = def2.toLowerCase() + '.json';
// FileParser('../game/defs/' + def2, function(data, fileSize){
//   var defData = DefParser(data, fileSize);
//
//   fs.writeFile('./data/defs/' + defOutput2, JSON.stringify(defData), function (err) {
//     if (err) throw err;
//     console.log('File "' + defOutput2 + '" has been saved!');
//   });
// });

// var def3 = 'AVA0001.def';
// var defOutput3 = def3.toLowerCase() + '.json';
// FileParser('../game/defs/' + def3, function(data, fileSize){
//   var defData = DefParser(data, fileSize);
//
//   fs.writeFile('./data/defs/' + defOutput3, JSON.stringify(defData), function (err) {
//     if (err) throw err;
//     console.log('File "' + defOutput3 + '" has been saved!');
//   });
// });
