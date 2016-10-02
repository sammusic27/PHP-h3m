var Header = require('./defparser/header');
var Sequence = require('./defparser/sequence');

var index = 0;

var DefParser = function( data , fileSize ){


  console.log('======DATA LENGTH--->>>', data.length);

  var def = {};

  // header
  def.header = Header(data, index);
  index = def.header._index;
  delete def.header._index;

  // sequence
  def.h3def_sequence = [];
  for(var i = 0; i < def.header.sequences_count; i++){
    def.h3def_sequence.push(Sequence(data, index));
    index = def.h3def_sequence[def.h3def_sequence.length - 1]._index;
    delete def.h3def_sequence[def.h3def_sequence.length - 1]._index;
  }

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
  // console.log('========h3def_sequence: ', def.h3def_sequence);

  // TODO: check length
  for(var j = 0; j < def.h3def_sequence.length; j++){
    h3def_frame_header[j] = [];
    for(var i = 0; i < def.h3def_sequence[j].offsets.length; i++){
      h3def_frame_header[j].push(read_f3def_frame_header(def.h3def_sequence[j].offsets, i));
    }
  }

  console.log(index);
  console.log(h3def_frame_header[0][0].data);


  function read_f3def_frame_header(obj_sequence, i ){
    index = obj_sequence[i];
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



    var offset_small = data.readUIntLE(obj_sequence[i] + 32).toString(10);
    var start = obj_sequence[i]  + 32 + +offset_small;
    var end = obj_sequence.length - 1 == i ? data.length - obj.data_size : obj_sequence[i + 1];
    var buffer = [];

    index = end;



    // console.log(obj);
    // console.log(index);
    switch(obj.type){
      case 1:

        buffer = data.slice(start, end);
        // obj.data = readRLEFormat_new(index, obj);
        // obj.dataBase = readBytes(index, obj.data_size);
        // obj.data = myFormatType(obj, obj_sequence.offsets[i]);
        break;
      case 2:
      case 3:
        buffer = data.slice(start, end);
        // obj.data = readRLEFormat_new(index, obj);
        // obj.dataBase = readBytes(index, obj.data_size);
        // obj.data = myFormatType(obj, obj_sequence.offsets[i]);
        break;
    }

    console.log('Buffer length = ', buffer.length, start, end);

    for(var k = 0; k < buffer.length; k++){
      obj.data.push(buffer.readUIntLE(k, 1).toString(10));
    }


    return obj;
  }



  function readRLEFormat_new(index, obj){
    var frame = [];
    // console.log(obj);
    // console.log(obj.data_size + 32);
    var predel = (obj.data_size )/4;

    for(var i = 0; i < predel; i++){
      var v1 = +data.readUIntLE(index, 1).toString(10);
      var v2 = +data.readUIntLE(index + 1, 1).toString(10);
      var v3 = +data.readUIntLE(index + 2, 1).toString(10);
      var v4 = +data.readUIntLE(index + 3, 1).toString(10);
      frame.push([v1, v2, v3, v4]);

      // var v1 = +data.readUIntLE(index, 4).toString(10);
      // frame.push(v1);
      console.log(v1, v2, v3, v4);

      index += 4;
    }
    // console.log(frame, def.header.h3def_color_indexed);
    // console.log('frame length', frame.length, index);
    console.log(index);
    return frame;
  }



  function readBytes(from, length){
    var output = [];
    for(var i = index; i < index + length; i++){
      output.push(+data.readUIntLE(i, 0).toString(10));
    }
    return output;
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
          };
          readerCount += 4;
          break;
        case 2:
        case 3:
          params[i] = {
            offset: offset,
            l: length,
            type: type,
          };
          readerCount += 2;
          break;
      }
    }


    // console.log('---------------------', obj.img_width, i, obj.img_height);
    // for(var j = 0 ; j < i; j++){
    //   console.log(params[j].l);
    // }
    // console.log('---------------------');

    // ------------- frame ----

    // for(var i = 0; i < obj.height; i++){
    //   var offsetPicX = +data.readUIntLE(fromIndex + params[i].offset, 0).toString(10);
    //   index++;
    //
    //   // fill zero before
    //   if(offsetPicX != 255){
    //     for(var j = 0; j <= offsetPicX; j++){
    //       output.push(0);
    //     }
    //   }
    //   if(offsetPicX == 255) offsetPicX = 0; // small hack
    //
    //   // get params
    //   var start = fromIndex + params[i].offset + 2;
    //   var finish = params[i].l - 1;
    //
    //   // console.log('---', offsetPicX, fromIndex + params[i].offset - 32, '---');
    //
    //   // set data
    //   for(var j = start; j < length; j++){
    //     output.push(+data.readUIntLE(j, 0).toString(10));
    //     index++;
    //   }
    //
    //   // fill zero after
    //   if(finish > 0){
    //     for(var j = 0; j < finish; j++){
    //       output.push(0);
    //     }
    //   }
    // }
    // console.log(output.join(' '));
    // console.log('Length: ', obj.width * obj.height, output.length);

    return output;
  }

  // function packBytes(from, length){
  //   //console.log(index, length, index + length);
  //   var output = [];
  //   var out = [];
  //   for(var i = index; i < index + length; i++){
  //     out.push(data.readUIntLE(i, 0).toString(16));
  //     var count = +data.readUIntLE(i, 0).toString(10);
  //
  //     if(count >= 128){
  //       count = 256 - count;
  //       for(var j = 0 ; j <= count; j++){
  //         output.push(data.readUIntLE(i + 1, 0).toString(10));
  //       }
  //       i++;
  //     }else{
  //       var j = 0;
  //       for(j = 0 ; j <= count; j++){
  //         if(i + j + 1 < fileSize){
  //           output.push(data.readUIntLE(i + j + 1, 0).toString(10));
  //         }
  //         else{
  //           //console.log('ERR ',i + j + 1, fileSize);
  //         }
  //       }
  //       i = i + j;
  //     }
  //   }
  //   index = index + length;
  //
  //   // console.log('out int: ', out.join(' '));
  //   // console.log('output int: ', output.join(' '));
  //
  //   return output;
  // }



  // TESTpackBytes();
  // function HEX2DEC(number) {
  //   // Return error if number is not hexadecimal or contains more than ten characters (10 digits)
  //   if (!/^[0-9A-Fa-f]{1,10}$/.test(number)) return '#NUM!';
  //
  //   // Convert hexadecimal number to decimal
  //   var decimal = parseInt(number, 16);
  //
  //   // Return decimal number
  //   return (decimal >= 549755813888) ? decimal - 1099511627776 : decimal;
  // }
  // function TESTpackBytes(){
  //   var input = ['FE','AA','02','80','00','2A','FD','AA','03','80','00','2A','22','F7','AA'];
  //   var output = [];
  //   for(var i = 0; i < input.length; i++){
  //     var count = HEX2DEC(input[i]);
  //
  //     if(count >= 128){
  //       count = 256 - count;
  //
  //       for(var j = 0 ; j <= count; j++){
  //         output.push(input[i + 1]);
  //       }
  //       i++;
  //     }else{
  //       var j = 0;
  //       for(j = 0 ; j <= count; j++){
  //         output.push(input[i + j + 1]);
  //       }
  //       i = i + j;
  //     }
  //   }
  //   console.log(input.join(" "));
  //   console.log('---');
  //   console.log(output.join(" "));
  //   console.log('AA AA AA 80 00 2A AA AA AA AA 80 00 2A 22 AA AA AA AA AA AA AA AA AA AA');
  // }

  def.h3def_frame_header = h3def_frame_header;

  return def;
};

module.exports = DefParser;
