$(document).ready(function(){
  var APPWIDTH = 800
    , APPHEIGHT = 600
    ;

    // setup base view
  Crafty.init(APPWIDTH, APPHEIGHT);

  loadMap('southern cross');

  loadDef('adopb1b', function(){
    // loadDef('clrrvr', function(){
    //
    // });
  });

});
