// var extend = require('extend');

var RoEPlayers = require('./mapparser/roe/players');
var RoEVictory = require('./mapparser/roe/victory');
var RoELoss = require('./mapparser/roe/loss');
var RoETeams = require('./mapparser/roe/team');
var RoEFreeHeroes = require('./mapparser/roe/freeHeroes');
var RoEArtefacts = require('./mapparser/roe/artefacts');
var RoERumors = require('./mapparser/roe/rumors');
var RoEMap = require('./mapparser/roe/map');

var MapParser = function(data){
  // base object of map
  var mapInfo = {
    props: {}
  };

  var index = 0;
  // get base data of map
  // hero on the map 1 byte
  mapInfo.props.heroyOnTheMap = +data.readUIntLE(4, 0).toString(16);
  // map size 4 bytes
  mapInfo.props.mapSize = +data.readUIntLE(5, 4).toString(10);
  // is underground map exist 1 byte
  mapInfo.props.isCaves = +data.readUIntLE(9, 0).toString(10);

  // read name length and name of the map
  var nameLength = +data.readUIntLE(10, 0).toString(10)
    + +data.readUIntLE(11, 0).toString(10)
    + +data.readUIntLE(12, 0).toString(10)
    + +data.readUIntLE(13, 0).toString(10);
  mapInfo.props.name = data.toString('ascii', 14, 14 + nameLength);
  // read description length and description of the map
  var descriptionLength = +data.readUIntLE(14 + nameLength, 0).toString(10)
    + +data.readUIntLE(15 + nameLength, 0).toString(10)
    + +data.readUIntLE(16 + nameLength, 0).toString(10)
    + +data.readUIntLE(17 + nameLength, 0).toString(10);
  mapInfo.props.description = data.toString('ascii', 18 + nameLength, 18 + nameLength + descriptionLength);
  // map difficulty
  // 0 - easy, 1 - normal, 2 - hard, 3 - expert, 4 - impossible
  mapInfo.props.difficalty = +data.readUIntLE(18 + nameLength + descriptionLength, 0).toString(10);

  index = 18 + nameLength + descriptionLength;

  // list of map types
  // 0E 00 00 00 - RoE
  // 15 00 00 00 - AB
  // 1C 00 00 00 - SoD
  // 33 00 00 00 - WoG
  switch(data.readUInt32BE(0)){
    case parseInt('0x0E000000'):
      mapInfo.type = 'RoE';
      // get info about all players
      mapInfo.players = RoEPlayers(data, index);
      index = mapInfo.players.index;
      delete mapInfo.players.index;
      // get info about victory conditions
      mapInfo.victory = RoEVictory(data, index);
      index = mapInfo.victory.index;
      delete mapInfo.victory.index;
      // get info about loss conditions
      mapInfo.loss = RoELoss(data, index);
      index = mapInfo.loss.index;
      delete mapInfo.loss.index;
      // get info about teams
      mapInfo.teams = RoETeams(data, index);
      index = mapInfo.teams.index;
      delete mapInfo.teams.index;
      // get free heroes info
      mapInfo.freeHeroes = RoEFreeHeroes(data, index);
      index = mapInfo.freeHeroes.index;
      delete mapInfo.freeHeroes.index;
      // free 31 bytes ???
      index += 31;
      // get free heroes info
      // mapInfo.artefacts = RoEArtefacts(data, index);
      // index = mapInfo.artefacts.index;
      // delete mapInfo.artefacts.index;

      // get info about rumors
      mapInfo.rumors = RoERumors(data, index);
      index = mapInfo.rumors.index;
      delete mapInfo.rumors.index;
      // get map
      mapInfo.map = RoEMap(data, mapInfo, index);
      index = mapInfo.map.index;
      delete mapInfo.map.index;

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
