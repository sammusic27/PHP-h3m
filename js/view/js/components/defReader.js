function loadDef( name , callback ){
  $.ajax({
    url: 'def/'+name,
    type: 'GET',
    dataType: 'JSON',
    success: function(data){
      palette(data.data);
      spans(data.data);

      callback && callback();
    },
    error: function(err){
      console.error('ajax error', err);
    }
  });
}

function palette(data){
  var counter = 0;
  for(var i in data.header.h3def_color_indexed){
    var span = '<span class="cell" style="background:#'
      + colorAdd(data.header.h3def_color_indexed[i].r)
      + colorAdd(data.header.h3def_color_indexed[i].g)
      + colorAdd(data.header.h3def_color_indexed[i].b)
      +'">'+counter+'</span>';
      counter++;
    $('#palette').append(span);
  }
}

function spans(data){
  console.log(data);



  data.h3def_frame_header.forEach(function(header){
    var frame = header[0];
    // header.forEach(function(frame){
      // console.log(frame);
      $('#test').append('<div>Type: ' + frame.type + '</div>');
      var i = 0;

      var counter = 0;

      // if(frame.dataBase){
      //   frame.dataBase.forEach(function(pixel){
      //     var span = '<span class="cell" style="background:#'
      //       + colorAdd(data.header.h3def_color_indexed[pixel].r)
      //       + colorAdd(data.header.h3def_color_indexed[pixel].g)
      //       + colorAdd(data.header.h3def_color_indexed[pixel].b)
      //       +'">'+pixel + '('+counter+')'+'</span>';
      //     $('#test').append(span);
      //
      //     i++;
      //     if(i == frame.width){
      //       i = 0;
      //       $('#test').append('<div style="clear:both;"></div>');
      //     }
      //     counter++;
      //   });
      //   $('#test').append('<hr style="clear: both;">');
      // }

      var i = 0;
      var counter = 0;
      var width_help = 0;
      var height_help = 0;
      if(frame.data && frame.data.length) {
        frame.data.forEach(function (pixel, index) {
          pixel = +pixel;
          if(pixel < 200){
            var p1 = frame.data[index];
            var p2 = frame.data[index + 1];

            if(pixel < 10 && width_help + pixel + 1 >= frame.img_width){
              width_help += pixel + 1;
              var span = '<span class="cell cell-' + (pixel + 1) + '" style="background:#'
                + colorAdd(data.header.h3def_color_indexed[0].r)
                + colorAdd(data.header.h3def_color_indexed[0].g)
                + colorAdd(data.header.h3def_color_indexed[0].b)
                + '">' + pixel + '(' + counter + ')' + '</span>';

              $('#test').append(span);
              $('#test').append('<div style="clear:both;"></div>');
              width_help = 0;
            } else {

            if(p2 > 200){
              width_help += pixel + 1;
              var span = '<span class="cell cell-' + (pixel + 1) + '" style="background:#'
                + colorAdd(data.header.h3def_color_indexed[0].r)
                + colorAdd(data.header.h3def_color_indexed[0].g)
                + colorAdd(data.header.h3def_color_indexed[0].b)
                + '">' + pixel + '(' + counter + ')' + '</span>';

              $('#test').append(span);
            } else {
              width_help++;
              var span = '<span class="cell" style="background:#'
                + colorAdd(data.header.h3def_color_indexed[pixel].r)
                + colorAdd(data.header.h3def_color_indexed[pixel].g)
                + colorAdd(data.header.h3def_color_indexed[pixel].b)
                + '">' + pixel + '(' + counter + ')' + '</span>';

              $('#test').append(span);
            }

            if(width_help >= frame.img_width){
              $('#test').append('<div style="clear:both;"></div>');
              width_help = 0;
            }
            }
          }
        });
      }
      $('#test').append('<div style="clear:both;"></div><br><br>');
    // });
  });
}

function colorAdd(color){
  color = (color.length == 1) ? '0' + color : color;
  return color;
}
