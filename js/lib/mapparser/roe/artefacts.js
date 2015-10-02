var Arts = function(data, index){
  var obj = {};

  var freeBytes = 16;

  obj.artefacts = '';
  for(var i = index; i < index + freeBytes; i++){
    obj.artefacts += data.readUIntLE(i, 1).toString(2);
  }

  obj.index = index + freeBytes;
  return obj;
}

module.exports = Arts;
