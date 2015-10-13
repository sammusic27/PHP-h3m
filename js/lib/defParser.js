var Header = require('./defparser/header');
var Sequence = require('./defparser/sequence');

var DefParser = function( data , fileSize ){
  var index;
  var def = {};

  // header
  def.header = Header(data, index);
  index = def.header._index;
  delete def.header._index;

  // sequence
  def.h3def_sequence = Sequence(data, index);
  index = def.h3def_sequence._index;
  delete def.h3def_sequence._index;

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

  var h3def_frame_header = [];
  console.log('offsets: ', def.h3def_sequence.offsets);

  // TODO: check length
  for(var i = 0; i < def.h3def_sequence.offsets.length; i++)
  {
    // index = h3def_sequence.offsets[i]; // test header
    h3def_frame_header.push(read_f3def_frame_header(i));
  }

  function read_f3def_frame_header( i ){
    var obj = {};
    obj.data_size = +data.readUIntLE(index, 4).toString(10);
    index += 4;
    obj.type = +data.readUIntLE(index, 4).toString(10);
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
    obj.data = [];

    switch(obj.type){
      case 1:
        obj.dataBase = readBytes(index, obj.data_size);
        obj.data = myFormatType(obj, def.h3def_sequence.offsets[i]);
        break;
      case 2:
      case 3:
        obj.dataBase = readBytes(index, obj.data_size);
        obj.data = myFormatType(obj, def.h3def_sequence.offsets[i]);
        break;
    }

    return obj;
  }

  function myFormatType(obj, globalOffset)
  {
    var output = [];
    var params = {};
    var readerCount = 0;
    var fromIndex = index;
    var length = 0;
    for(var i = 0; i < obj.height; i++){
      var offset = +data.readUIntLE(index++, 0).toString(10);
      var type = +data.readUIntLE(index++, 0).toString(10);

      offset = 255 * type + offset;
      if(type > 0){
        offset += type;
      }

      if(i > 0 && i < obj.height){
        params[i - 1].l = offset - params[i - 1].offset;
      }

      switch(obj.type){
        case 1:
          params[i] = {
            offset: offset,
            l: length,
            type: type,
            unknown1: +data.readUIntLE(index++, 0).toString(10),
            unknown2: +data.readUIntLE(index++, 0).toString(10),
          }
          readerCount += 4;
          break;
        case 2:
        case 3:
          params[i] = {
            offset: offset,
            l: length,
            type: type,
          }
          readerCount += 2;
          break;
      }
    }
    console.log('---------------------', obj.img_width, i, obj.img_height);
    for(var j = 0 ; j < i; j++){
      console.log(params[j].l);
    }
    console.log('---------------------');

    // ------------- frame ----

    for(var i = 0; i < obj.height; i++){
      var offsetPicX = +data.readUIntLE(fromIndex + params[i].offset, 0).toString(10);
      index++;

      // fill zero before
      if(offsetPicX != 255){
        for(var j = 0; j <= offsetPicX; j++){
          output.push(0);
        }
      }
      if(offsetPicX == 255) offsetPicX = 0; // small hack

      // get params
      var start = fromIndex + params[i].offset + 2;
      var finish = params[i].l - ;

      // console.log('---', offsetPicX, fromIndex + params[i].offset - 32, '---');

      // set data
      for(var j = start; j < length; j++){
        output.push(+data.readUIntLE(j, 0).toString(10));
        index++;
      }

      // fill zero after
      if(finish > 0){
        for(var j = 0; j < finish; j++){
          output.push(0);
        }
      }
    }
    // console.log(output.join(' '));
    // console.log('Length: ', obj.width * obj.height, output.length);

    return output;
  }

  function packBytes(from, length){
    //console.log(index, length, index + length);
    var output = [];
    var out = [];
    for(var i = index; i < index + length; i++){
      out.push(data.readUIntLE(i, 0).toString(16));
      var count = +data.readUIntLE(i, 0).toString(10);

      if(count >= 128){
        count = 256 - count;
        for(var j = 0 ; j <= count; j++){
          output.push(data.readUIntLE(i + 1, 0).toString(10));
        }
        i++;
      }else{
        var j = 0;
        for(j = 0 ; j <= count; j++){
          if(i + j + 1 < fileSize){
            output.push(data.readUIntLE(i + j + 1, 0).toString(10));
          }
          else{
            //console.log('ERR ',i + j + 1, fileSize);
          }
        }
        i = i + j;
      }
    }
    index = index + length;

    // console.log('out int: ', out.join(' '));
    // console.log('output int: ', output.join(' '));

    return output;
  }

  function readBytes(from, length){
    var output = [];
    for(var i = index; i < index + length; i++){
      output.push(+data.readUIntLE(i, 0).toString(10));
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
  function TESTpackBytes(){
    var input = ['FE','AA','02','80','00','2A','FD','AA','03','80','00','2A','22','F7','AA'];
    var output = [];
    for(var i = 0; i < input.length; i++){
      var count = HEX2DEC(input[i]);

      if(count >= 128){
        count = 256 - count;

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
