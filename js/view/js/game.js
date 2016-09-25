$(document).ready(function(){
  var APPWIDTH = 800
    , APPHEIGHT = 600
    ;

    // setup base view
  Crafty.init(APPWIDTH, APPHEIGHT, document.getElementById('game'));


  Crafty.defineScene("loading", SCENELoading);
  Crafty.defineScene("devResource", SCENEDevResource);
  Crafty.defineScene("game", SCENEGame);

  Crafty.enterScene("loading");
});
