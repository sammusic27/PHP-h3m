var Team = function(data, index){
  var obj = {};

   obj.type = +data.readUIntLE(index++, 0).toString(10);
  if( obj.type ){
       obj.red = data.readUIntLE(index++, 0).toString(10);
       obj.blue = data.readUIntLE(index++, 0).toString(10);
       obj.tan = data.readUIntLE(index++, 0).toString(10);
       obj.green = data.readUIntLE(index++, 0).toString(10);
       obj.orange = data.readUIntLE(index++, 0).toString(10);
       obj.purple = data.readUIntLE(index++, 0).toString(10);
       obj.teal = data.readUIntLE(index++, 0).toString(10);
       obj.pink = data.readUIntLE(index++, 0).toString(10);
  }

  obj.index = index;
  return obj;
}

module.exports = Team;
