var extend = require('extend');

var Victory = function(data, index){
  var obj = {};

  // 1    Special Victory Condition:
  obj.type = data.readUIntLE(index++, 0).toString(16);
  if(obj.type == 'ff') {
      obj.name = 'None';
      obj.index = index;
      return obj;
  }

  obj.usial_end = data.readUIntLE(index++, 0).toString(16);
  obj.comp_has = data.readUIntLE(index++, 0).toString(16);

  switch(obj.type){
      case 'ff': break; // not
      case '0': // 00 - Acquire a specific artifact
          obj.name = 'Acquire a specific artifact';
          obj.art = data.readUIntLE(index++, 0).toString(16);
          break;
      case '1': // 01 - Accumulate creatures
          obj.name = 'Accumulate creatures';
          obj.unit = data.readUIntLE(index++, 0).toString(16);
          obj.unit_count = data.readUIntLE(index++, 0).toString(16);
          break;
      case '2': // 02 - Accumulate resources
          obj.name = 'Accumulate resources';
          obj.resource = data.readUIntLE(index++, 0).toString(16);
          // 0 - Wood     4 - Crystal
          // 1 - Mercury  5 - Gems
          // 2 - Ore      6 - Gold
          // 3 - Sulfur
          obj.resource_count = data.readUIntLE(index++, 0).toString(16);
          break;
      case '3': // 03 - Upgrade a specific town
          obj.name = 'Upgrade a specific town';
          obj.x = +data.readUIntLE(index++, 0).toString(10);
          obj.y = +data.readUIntLE(index++, 0).toString(10);
          obj.z = +data.readUIntLE(index++, 0).toString(10);
          obj.hall_lvl = +data.readUIntLE(index++, 0).toString(10);
          // Hall Level:   0-Town, 1-City,    2-Capitol
          obj.castle_lvl = +data.readUIntLE(index++, 0).toString(10);
          // Castle Level: 0-Fort, 1-Citadel, 2-Castle
          break;
      case '4': // 04 - Build the grail structure
          obj.name = 'Build the grail structure';
          obj.x = +data.readUIntLE(index++, 0).toString(10);
          obj.y = +data.readUIntLE(index++, 0).toString(10);
          obj.z = +data.readUIntLE(index++, 0).toString(10);
          break;
      case '5': // 05 - Defeat a specific Hero
          obj.name = 'Defeat a specific Hero';
          obj.x = +data.readUIntLE(index++, 0).toString(10);
          obj.y = +data.readUIntLE(index++, 0).toString(10);
          obj.z = +data.readUIntLE(index++, 0).toString(10);
          break;
      case '6': // 06 - Capture a specific town
          obj.name = 'Capture a specific town';
          obj.x = +data.readUIntLE(index++, 0).toString(10);
          obj.y = +data.readUIntLE(index++, 0).toString(10);
          obj.z = +data.readUIntLE(index++, 0).toString(10);
          break;
      case '7': // 07 - Defeat a specific monster
          obj.name = 'Defeat a specific monster';
          obj.x = +data.readUIntLE(index++, 0).toString(10);
          obj.y = +data.readUIntLE(index++, 0).toString(10);
          obj.z = +data.readUIntLE(index++, 0).toString(10);
          break;
      case '8': // 08 - Flag all creature dwelling
          obj.name = 'Flag all creature dwelling';
          break;
      case '9': // 09 - Flag all mines
          obj.name = 'Flag all mines';
          break;
      case 'a': // 0A - Transport a specific artifact
          obj.name = 'Transport a specific artifact';
          obj.art = data.readUIntLE(index++, 0).toString(16);
          obj.x = +data.readUIntLE(index++, 0).toString(10);
          obj.y = +data.readUIntLE(index++, 0).toString(10);
          obj.z = +data.readUIntLE(index++, 0).toString(10);
          break;
      default: // ff - not
  }

  obj.index = index;
  return obj;
}

module.exports = Victory;
