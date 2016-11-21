<?php
require_once "../vendor/autoload.php";
include "../reportmaker/TableMaker.php";
include "../reportmaker/FileReader.php";
include "../reportmaker/XMLConfigFileReader.php";
$tm = new TableMaker();
echo($tm->getTableFromXMLFile("testTemplate.xml"));

