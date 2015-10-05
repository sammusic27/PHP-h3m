var dataLoader = require('../lib/loader.js');

var Routes = function( app ){

  // get test map
  app.get('/map', function(req, res) {
    dataLoader('ascension.json')
      .fail(function (err) {
          res.status(500).send(err);
      })
      .then(function (data) {
          res.status(200).send(data);
      });
  });

  // get test def
  app.get('/def', function(req, res) {
    dataLoader('defs/CLRRVR.def.json')
      .fail(function (err) {
          res.status(500).send(err);
      })
      .then(function (data) {
          res.status(200).send(data);
      });
  });

  // test routes for future things
  app.get('/test', function(req, res) {
    req.io.route('customers:create');
  });

  app.io.route('customers', {
      create: function(req) {
          req.io.respond({hello: 'from io route'})
      },
      update: function(req) {
          // update your customer
      },
      remove: function(req) {
          // remove your customer
      },
  });

  app.io.route('drawClick', function(req) {
      req.io.broadcast('draw', req.data)
  });
}

module.exports = Routes;
