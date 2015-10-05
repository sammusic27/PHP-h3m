
var DefParser = function( data ){
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

  var index = 16;
  var def = {};
  def.header = {
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

// console.log(def.header);
  // here "index" should be 784
  if(index != 784) console.log("Error: index is not equal 784 => " + index);

  // struct h3def_sequence {
  //     uint32_t type;
  //     uint32_t length;
  //     uint32_t unknown1;
  //     uint32_t unknown2;
  //     char *names[length];
  //     uint32_t offsets[length];
  // };

  h3def_sequence = {};
  h3def_sequence.type = data.readUIntLE(index, 4).toString(16);
  index += 4;
  h3def_sequence.length = +data.readUIntLE(index, 4).toString(10);
  index += 4;
  h3def_sequence.unknown1 = data.readUIntLE(index, 4).toString(16);
  index += 4;
  h3def_sequence.unknown2 = data.readUIntLE(index, 4).toString(16);
  index += 4;

  h3def_sequence.names = [];
  var defaultNameLength = 13;
  for(var i = 0; i < h3def_sequence.length; i++){
    h3def_sequence.names.push(data.toString(undefined, index, index + defaultNameLength - 1));
    index += defaultNameLength;
  }

  h3def_sequence.offsets = [];
  for(var i = 0; i < h3def_sequence.length; i++){
    h3def_sequence.offsets.push(+data.readUIntLE(index, 4).toString(10));
    index += 4;
  }

  def.h3def_sequence = h3def_sequence;

  // struct h3def_frame_header {
  //      uint32_t data_size;
  //      uint32_t type;
  //      uint32_t width;
  //      uint32_t height;
  //      uint32_t img_width;
  //      uint32_t img_height;
  //      uint32_t x;
  //      uint32_t y;
  //  };

  // console.log(index);

  h3def_frame_header = [];
  // console.log(h3def_sequence.offsets);
  for(var i = 0; i < h3def_sequence.length; i++)
  {
    index = h3def_sequence.offsets[0]; // test header
    h3def_frame_header.push(read_f3def_frame_header());
  }
  // console.log(h3def_frame_header[0].width);
  // console.log(h3def_frame_header[0].height);
  // console.log(h3def_frame_header[0].data.length);

  function read_f3def_frame_header(  ){
    var obj = {};
    obj.data_size = +data.readUIntLE(index, 4).toString(10);
    index += 4;
    obj.type = data.readUIntLE(index, 4).toString(16);
    index += 4;
    obj.width = +data.readUIntLE(index, 4).toString(10);
    index += 4;
    obj.height = +data.readUIntLE(index, 4).toString(10);
    index += 4;
    obj.img_width = +data.readUIntLE(index, 4).toString(10);
    index += 4;
    obj.img_height = +data.readUIntLE(index, 4).toString(10);
    index += 4;
    obj.x = +data.readUIntLE(index, 4).toString(10);
    index += 4;
    obj.y = +data.readUIntLE(index, 4).toString(10);
    index += 4;
    obj.data = packBytes(index, obj.data_size);

    return obj;
  }

  function packBytes(from, length){
    var output = [];
    for(var i = index; i < index + length; i++){
      var count = data.readUIntLE(i, 0).toString(10);

      if(count >= 128){
        var count = 256 - count;
        for(var j = 0 ; j <= count; j++){
          output.push(data.readUIntLE(i + 1, 0).toString(10));
        }
        i++;
      }else{
        for(var j = 0 ; j <= count; j++){
          output.push(data.readUIntLE(i + j + 1, 0).toString(10));
        }
        i = i + j;
      }
    }
    return output;
  }

  //TESTpackBytes();

  function HEX2DEC(number) {
    // Return error if number is not hexadecimal or contains more than ten characters (10 digits)
    if (!/^[0-9A-Fa-f]{1,10}$/.test(number)) return '#NUM!';

    // Convert hexadecimal number to decimal
    var decimal = parseInt(number, 16);

    // Return decimal number
    return (decimal >= 549755813888) ? decimal - 1099511627776 : decimal;
  }

  function TESTpackBytes(from, length){
    var input = ['FE','AA','02','80','00','2A','FD','AA','03','80','00','2A','22','F7','AA'];
    var output = [];
    for(var i = 0; i < input.length; i++){
      var count = HEX2DEC(input[i]);
      if(count >= 128){
        var count = 256 - count;

        for(var j = 0 ; j <= count; j++){
          output.push(input[i + 1]);
        }
        i++;
      }else{
        var j = 0;
        for(j = 0 ; j <= count; j++){
          output.push(input[i + j + 1]);
        }
        i = i + j;
      }
    }
    console.log(input.join(" "));
    console.log('---');
    console.log(output.join(" "));
    console.log('AA AA AA 80 00 2A AA AA AA AA 80 00 2A 22 AA AA AA AA AA AA AA AA AA AA');
  }

  def.h3def_frame_header = h3def_frame_header;

  return def;
}

module.exports = DefParser;
