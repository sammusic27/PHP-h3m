
var Sequence = function( data , index){
  // struct h3def_sequence {
  //     uint32_t type;
  //     uint32_t length;
  //     uint32_t unknown1;
  //     uint32_t unknown2;
  //     char *names[length];
  //     uint32_t offsets[length];
  // };
  var h3def_sequence = {};
  h3def_sequence.type = data.readUIntLE(index, 4).toString(16);
  index += 4;
  h3def_sequence.length = +data.readUIntLE(index, 4).toString(10);
  index += 4;
  h3def_sequence.unknown1 = data.readUIntLE(index, 4).toString(16);
  index += 4;
  h3def_sequence.unknown2 = data.readUIntLE(index, 4).toString(16);
  index += 4;

  function check_name(name, defaultNameLength){
    var n = name.split('.');
    if(n.length > 1 && n[1].length > 3){
      defaultNameLength = defaultNameLength - n[1].length - 3;
      n[1] = n[1].slice(0, 3);
      return n.join('.');
    }
    return name;
  }

  h3def_sequence.names = [];
  var defaultNameLength = 13;
  for(var i = 0; i < h3def_sequence.length; i++){
    h3def_sequence.names.push(data.toString(undefined, index, index + defaultNameLength - 1));
    h3def_sequence.names[i] = check_name(h3def_sequence.names[i], defaultNameLength);
    index += defaultNameLength;
  }

  h3def_sequence.offsets = [];
  for(var i = 0; i < h3def_sequence.length; i++){
    h3def_sequence.offsets.push(+data.readUIntLE(index, 4).toString(10));
    index += 4;
  }
  h3def_sequence._index = index;

  return h3def_sequence;
};



module.exports = Sequence;
