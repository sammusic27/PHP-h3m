
var Header = function( data , index){
  // struct h3def_color_indexed {
  // 	uint8_t r;
  // 	uint8_t g;
  // 	uint8_t b;
  // };
  //
  // struct h3def_header {
  // 	uint32_t type;
  // 	uint32_t width;
  // 	uint32_t height;
  // 	uint32_t sequences_count;
  // 	h3def_color_indexed palette[256];
  // };
  index = 16;
  var header = {
    type: data.readUIntLE(0, 4).toString(16)
    , width: data.readUIntLE(4, 4).toString(10)
    , height: data.readUIntLE(8, 4).toString(10)
    , sequences_count: data.readUIntLE(12, 4).toString(10)
    , h3def_color_indexed: get_h3def_color_indexed(256, 16)
  };

  function get_h3def_color_indexed(count, start_index){
    var h3def = {};
    var j = 0;
    for(var i = 0; i < count * 3; i = i + 3){
      h3def[j] = {
        r: data.readUIntLE(start_index + i, 0).toString(16),
        g: data.readUIntLE(start_index + i + 1, 0).toString(16),
        b: data.readUIntLE(start_index + i + 2, 0).toString(16)
      }
      j++;
      index += 3;
    }
    return h3def;
  }

  // here "index" should be 784
  if(index != 784) console.log("Error: index is not equal 784 => " + index);
  header._index = index;

  return header;
}

module.exports = Header;
