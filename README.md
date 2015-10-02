# h3m

## Parsers of h3m maps

### PHP parser class of Heroes of Might and Magic 3 map format

List features:

  - Players attributes
  - Special Victory/Loss conditions
  - Teams
  - Artefacts
  - Rumors
  - Display map (in progress)

Code example:

    require_once 'php/MapH3M.php';

    $file = new MapH3M('maps/Ascension.h3m');
    $file->displayBaseInfoMap();

#### Todo

  - Add Def parser.

### NodeJS parser

List features:

  - Players attributes
  - Special Victory/Loss conditions
  - Teams
  - Artefacts
  - Rumors
  - Display map (in progress)

Requirements:

  - Node 4.x

Run   node js/index.js   and look at the console and the json data has been saved into  js/data  folder.

## Server to interactive map (in progress)

File  js/server.js    run the server of node js which displays the canvas tag. Here should be display the map from json format.
