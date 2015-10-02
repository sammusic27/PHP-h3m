var Rumors = function(data, index){
  var obj = {};

  obj.all = [];
  obj.count = +data.readUIntLE(index++, 0).toString(10);
  obj.count += +data.readUIntLE(index++, 0).toString(10);
  obj.count += +data.readUIntLE(index++, 0).toString(10);
  obj.count += +data.readUIntLE(index++, 0).toString(10);

console.log(obj.count);

  if( obj.count ){
      for(var i = obj.count; i < index + obj.count; i++){
          var rumor = [];
          var name_i = 0;
          var name_length = 0;

          name_length = +data.readUIntLE(index++, 0).toString(10);
          name_length += +data.readUIntLE(index++, 0).toString(10);
          name_length += +data.readUIntLE(index++, 0).toString(10);
          name_length += +data.readUIntLE(index++, 0).toString(10);

          rumor.name = data.toString('ascii', index, index + name_length);

          // for(name_i = rumor.name_length; name_i < $this->index + rumor.name_length; name_i++)
          // {
          //     rumor.name .= data.readUIntLE(name_i, 0).toString(10);
          // }
          index += name_length;

          var desc_length = 0;
          desc_length = +data.readUIntLE(index++, 0).toString(10);
          desc_length += +data.readUIntLE(index++, 0).toString(10);
          desc_length += +data.readUIntLE(index++, 0).toString(10);
          desc_length += +data.readUIntLE(index++, 0).toString(10);

          rumor.desc = data.toString('ascii', index, index + desc_length);

          // for(name_i = rumor.desc_length; name_i < index + rumor.desc_length; name_i++)
          // {
          //     rumor.desc .= $this->filecontent[name_i];
          // }
          index += desc_length;

          obj.all.push(rumor);
      }
  }

  obj.index = index;
  return obj;
}

module.exports = Rumors;
