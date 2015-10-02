var FreeHeroes = function(data, index){
  var obj = {};

  var freeBytes = 16;

  obj.freeHeroes = '';
  for(var i = index; i < index + freeBytes; i++){
    obj.freeHeroes += data.readUIntLE(i, 1).toString(2);
  }

  obj.index = index + freeBytes;
  return obj;
}

module.exports = FreeHeroes;
