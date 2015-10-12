function SCENEGame(){
  Crafty.background("#000");
  Crafty.e("2D, Canvas, Text")
        .attr({ w: 100, h: 20, x: 150, y: 120 })
        .text("do game")
        .css({ "text-align": "center"})
        .textColor("#FFFFFF");
}
