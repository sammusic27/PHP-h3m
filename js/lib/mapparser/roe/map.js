var Map = function(data, mapObject, index){
  var obj = {};

  var count = mapObject.mapSize * mapObject.mapSize * 7;

  obj.map = [];
  for(var i = index; i < index + count; i = i + 7){
      obj.map.push({
        'surface': data.readUIntLE(i, 0).toString(16),
        'surface_type': data.readUIntLE(i + 1, 0).toString(16),
        'river': data.readUIntLE(i + 2, 0).toString(16),
        'river_type': data.readUIntLE(i + 3, 0).toString(16),
        'road': data.readUIntLE(i + 4, 0).toString(16),
        'road_type': data.readUIntLE(i + 5, 0).toString(16),
        'mirror': data.readUIntLE(i + 6, 0).toString(16)
      });
  }
  index += count;

  if(mapObject.isCaves){
    obj.mapUnderground = [];
      for(var i = index; i < index + count; i = i + 7){
        obj.mapUnderground.push({
          'surface': data.readUIntLE(i, 0).toString(16),
          'surface_type': data.readUIntLE(i + 1, 0).toString(16),
          'river': data.readUIntLE(i + 2, 0).toString(16),
          'river_type': data.readUIntLE(i + 3, 0).toString(16),
          'road': data.readUIntLE(i + 4, 0).toString(16),
          'road_type': data.readUIntLE(i + 5, 0).toString(16),
          'mirror': data.readUIntLE(i + 6, 0).toString(16)
        });
      }
      index += count;
  }

  obj.index = index;
  return obj;
}

module.exports = Map;
