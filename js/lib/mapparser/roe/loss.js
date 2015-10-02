var Loss = function(data, index){
  var obj = {};

   obj.type = data.readUIntLE(index++, 0).toString(16);
  if( obj.type == 'ff') {
       obj.name = 'None';
       obj.index = index;
      return obj;
  }

  switch( obj.type){
      case 'ff': break; // not
      case '0': // 00 - Lose a specific town
           obj.name = 'Lose a specific town';
           obj.x = +data.readUIntLE(index++, 0).toString(10);
           obj.y = +data.readUIntLE(index++, 0).toString(10);
           obj.z = +data.readUIntLE(index++, 0).toString(10);
          break;
      case '1': // 01 - Lose a specific hero
           obj.name = 'Lose a specific hero';
           obj.x = +data.readUIntLE(index++, 0).toString(10);
           obj.y = +data.readUIntLE(index++, 0).toString(10);
           obj.z = +data.readUIntLE(index++, 0).toString(10);
          break;
      case '2': // 02 - Accumulate resources
           obj.name = 'Time expires';
           obj.resource = data.readUIntLE(index++, 0).toString(16);
          break;
      default: // ff - not
  }

  obj.index = index;
  return obj;
}

module.exports = Loss;
