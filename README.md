# h3m

## Parsers of h3m maps

### PHP parser class of Heroes of Might and Magic 3 map format

List features:

  - Players attributes
  - Special Victory/Loss conditions
  - Teams
  - Artefacts
  - Rumors
  - Display minimap (in progress)

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
  - Display minimap

Requirements:

  - Node 4.x

Run   node js/index.js   and look at the console and the json data has been saved into  js/data  folder.

## Server interactive map (now shows the minimap only)

File  js/server/server.js  run the server of node js which displays the canvas tag. Here should be display the map from json format.
