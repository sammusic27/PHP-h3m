$(document).ready(function(){

  loadDef();

});

function loadDef( name ){
  $.ajax({
    url: 'def',
    type: 'GET',
    dataType: 'JSON',
    success: function(data){
      spans(data.data);
    },
    error: function(err){
      console.error('ajax error', err);
    }
  });
}

function spans(data){
  data.h3def_frame_header.forEach(function(frame){
    console.log(frame);
    var i = 0;
    // for(var j = 0; j < frame.data.length; j = j + 3){
    //   var span = '<span class="cell" style="background:#'+ frame.data[j] + frame.data[j + 1] + frame.data[j + 2] +'"></span>';
    //   $('#test').append(span);
    //   i++;
    //   if(i == frame.img_width){
    //       i = 0;
    //       $('#test').append('<div style="clear:both;"></div>');
    //     }
    // }
    frame.data.forEach(function(pixel){
      var span = '<span class="cell" style="background:#'+ data.header.h3def_color_indexed[pixel].r + data.header.h3def_color_indexed[pixel].g + data.header.h3def_color_indexed[pixel].b +'"></span>';
      $('#test').append(span);
      i++;
      if(i == frame.img_width){
        i = 0;
        $('#test').append('<div style="clear:both;"></div>');
      }
    });
    $('#test').append('<div style="clear:both;"></div><br><br>');
  });
}
