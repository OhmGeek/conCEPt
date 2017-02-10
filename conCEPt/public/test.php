<?php
require_once(__DIR__ . '/../vendor/autoload.php');

use Concept\Controller\PDFController;

header('Access-Control-Allow-Origin:*');

$test = new PDFController();