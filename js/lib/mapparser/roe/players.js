var extend = require('extend');

var RoEPlayers = function(data, index){
  var players = {};

  players.red = extend({}, details());
  players.blue = extend({}, details());
  players.tan = extend({}, details());
  players.green = extend({}, details());
  players.orange = extend({}, details());
  players.purple = extend({}, details());
  players.teal = extend({}, details());
  players.pink = extend({}, details());

  function details(){
    var obj = {};

    // can be a human ?
    obj.human = +data.readUIntLE(index++, 0).toString(10);
    // can be a computer ?
    obj.comp = +data.readUIntLE(index++, 0).toString(10);
    //
    obj.behavior = +data.readUIntLE(index++, 0).toString(10);
    // which town can get (binnary by all castles)
    obj.town = data.readUIntLE(index++, 1).toString(2);
    obj.town += data.readUIntLE(index++, 1).toString(2);

    // CHECK LATER <========
    obj.head_town_heroy_create = 0;
    if(obj.human && obj.behavior){
      obj.head_town_heroy_create = +data.readUIntLE(index++, 0).toString(10);
    }

    // castle exists
    obj.headTown = +data.readUIntLE(index++, 0).toString(10);
    // if castle exists get coordinates
    obj.headTownX = '';
    obj.headTownY = '';
    obj.headTownZ = '';
    if(obj.headTown){
        obj.headTownX = +data.readUIntLE(index++, 0).toString(10);
        obj.headTownY = +data.readUIntLE(index++, 0).toString(10);
        obj.headTownZ = +data.readUIntLE(index++, 0).toString(10);
    }
    // crate heroy after create
    obj.createHero = +data.readUIntLE(index++, 0).toString(10);

    obj.heroes = data.readUIntLE(index++, 0).toString(16);
    if(obj.heroes != 'ff'){
        obj.hero = {};
        obj.hero.hero_avatar = +data.readUIntLE(index++, 0).toString(16);
        obj.hero.name_length = +data.readUIntLE(index++, 0).toString(10);

        obj.hero.name = '';
        if(obj.hero.name_length){
            obj.hero.name_length += +data.readUIntLE(index++, 0).toString(10);
            obj.hero.name_length += +data.readUIntLE(index++, 0).toString(10);
            obj.hero.name_length += +data.readUIntLE(index++, 0).toString(10);

            obj.hero.name = data.toString('ascii', index, index + obj.hero.name_length);

            index += obj.hero.name_length;
        }
        if(obj.hero.name == ''){
            obj.hero.var1 = +data.readUIntLE(index++, 0).toString(10);
            obj.hero.var2 = +data.readUIntLE(index++, 0).toString(10);
            obj.hero.var3 = +data.readUIntLE(index++, 0).toString(10);
        }
    }

    return obj;
  }

  return players;
}

module.exports = RoEPlayers;
