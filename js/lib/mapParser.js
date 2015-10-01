// 3 types of map
// 0E 00 00 00 - RoE
// 15 00 00 00 - AB
// 1C 00 00 00 - SoD
// 33 00 00 00 - WoG

// var extend = require('extend');

var RoEPlayers = require('./mapparser/roe/players');
var RoEVictory = require('./mapparser/roe/victory');

var MapParser = function(data){
  var mapInfo = {};

  var index = 0;
  // get base data of map
  mapInfo.heroyOnTheMap = +data.readUIntLE(4, 0).toString(16);
  mapInfo.mapSize = +data.readUIntLE(5, 4).toString(10);
  mapInfo.isCaves = +data.readUIntLE(9, 0).toString(10);

  var nameLength = +data.readUIntLE(10, 0).toString(10)
    + +data.readUIntLE(11, 0).toString(10)
    + +data.readUIntLE(12, 0).toString(10)
    + +data.readUIntLE(13, 0).toString(10);
  mapInfo.name = data.toString('ascii', 14, 14 + nameLength);

  var descriptionLength = +data.readUIntLE(14 + nameLength, 0).toString(10)
    + +data.readUIntLE(15 + nameLength, 0).toString(10)
    + +data.readUIntLE(16 + nameLength, 0).toString(10)
    + +data.readUIntLE(17 + nameLength, 0).toString(10);
  mapInfo.description = data.toString('ascii', 18 + nameLength, 18 + nameLength + descriptionLength);

  mapInfo.difficalty = +data.readUIntLE(18 + nameLength + descriptionLength, 0).toString(10);

  index = 18 + nameLength + descriptionLength;


  switch(data.readUInt32BE(0)){
    case parseInt('0x0E000000'):
      mapInfo.type = 'RoE';
      mapInfo.players = RoEPlayers(data, index);
      mapInfo.victory = RoEVictory(data, index);
      break;
    case parseInt('0x15000000'):
      mapInfo.type = 'AB';
      // TODO: add more parsers
      break;
    case parseInt('0x1C000000'):
      mapInfo.type = 'SoD';
      // TODO: add more parsers
      break;
    case parseInt('0x33000000'):
      mapInfo.type = 'WoG';
      // TODO: add more parsers
      break;
  }

  return mapInfo;
}

module.exports = MapParser;
