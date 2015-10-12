$(document).ready(function(){
  var APPWIDTH = 800
    , APPHEIGHT = 600
    ;

    // setup base view
  Crafty.init(APPWIDTH, APPHEIGHT);

  loadMap('southern cross');
});

function loadMap( name , callback ){
  $.ajax({
    url: 'map/'+name,
    type: 'GET',
    dataType: 'JSON',
    success: function(data){

      createMiniMap(data.data);

      callback && callback();
    },
    error: function(err){
      console.error('map load/ajax error', err);
    }
  });
}

function createMiniMap( map ){
  /**
    show the minimap version with underground
    need to be changed because for minimap we will useing only static variables
    for example the view for minimap is: 200x200
    script shold be devide the map size to show mini map in this section
    or need to be changed by viewport section
  **/
  var
    KMap = 2
    , x = 0
    , y = 0
    ;
  var mapWidth = mapHeight = map.props.mapSize * KMap;
  if(map.props.isCaves){
    mapWidth += mapWidth;
  }

  var minimap = [];
  for(var i = 0; i < map.props.mapSize; i++)
  {
     for(var j = 0; j < map.props.mapSize; j++)
     {
       var color = '#000000';
       switch(map.map.map[i * map.props.mapSize + j].surface){
           case '0': color = '#503F0F'; break;
           case '1': color = '#DFCF8F'; break;
           case '2': color = '#004000'; break;
           case '3': color = '#B0C0C0'; break;
           case '4': color = '#4F806F'; break;
           case '5': color = '#807030'; break;
           case '6': color = '#008030'; break;
           case '7': color = '#4F4F4F'; break;
           case '8': color = '#0F5090'; break;
           case '9': color = '#000000'; break;
           default: color = '#000000';
       }
       var attr = {x: i * KMap + x, y: j * KMap + y, w: KMap, h: KMap};
       var entity = Crafty.e('2D, Canvas, Color')
         .attr(attr)
         .color(color);

       minimap.push(entity);
     }
  }

  if(map.props.isCaves){
    var minimapCaves = [];
    for(var i = 0; i < map.props.mapSize; i++)
    {
       for(var j = 0; j < map.props.mapSize; j++)
       {
         var color = '#000000';
         switch(map.map.mapUnderground[i * map.props.mapSize + j].surface){
             case '0': color = '#503F0F'; break;
             case '1': color = '#DFCF8F'; break;
             case '2': color = '#004000'; break;
             case '3': color = '#B0C0C0'; break;
             case '4': color = '#4F806F'; break;
             case '5': color = '#807030'; break;
             case '6': color = '#008030'; break;
             case '7': color = '#4F4F4F'; break;
             case '8': color = '#0F5090'; break;
             case '9': color = '#000000'; break;
             default: color = '#000000';
         }
         var attr = {x: i * KMap + mapWidth/2, y: j * KMap, w: KMap, h: KMap};
         var entity = Crafty.e('2D, Canvas, Color')
           .attr(attr)
           .color(color);

         minimapCaves.push(entity);
       }
    }
  }
}
