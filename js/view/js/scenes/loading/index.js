function SCENELoading(){
  Crafty.background("#000");
  Crafty.e("2D, Canvas, Text")
        .attr({ w: 100, h: 20, x: 150, y: 120 })
        .text("Loading (2 sec)")
        .textColor("#FFFFFF");

  setTimeout(function(){
    Crafty.enterScene("devResource");
  }, 2000);
}
