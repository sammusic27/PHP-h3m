var express = require('express.io');
var app = express();
var path = require("path");
var bodyParser = require('body-parser');

var PORT = 7076;
app.http().io();

app.set('views', __dirname + '/');
app.use(express["static"](path.join(__dirname, '/../view/')));

require('./routes/routes')(app);

app.listen(PORT);
console.log('server with socket running on the http://localhost:' + PORT);
