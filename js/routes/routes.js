
var Routes = function( app ){
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
