<?php

/**
 * Created by PhpStorm.
 * User: ryan
 * Date: 28/10/16
 * Time: 16:35
 */
class XMLConfigFileReader implements FileReader
{
    /**
     * Read a configuration file in XML Format
     * @param $filename
     * @return ArrayObject
     */
    public static function readConfigFile($filename)
    {
        $xmlData = simplexml_load_file($filename) or die("Error: file cannot be read.");
        echo $xmlData;
        return self::parseXML($xmlData);

    }

    /**
     * Parse the XML string and return the XML data
     * @param String
     * @return ArrayObject
     */
    private static function parseXML($xmlData) {
        //convert the XMLElementObject to an Array.
        $arr = json_decode(json_encode($xmlData),true);
        return $arr;
    }

    /**
     * @param $str
     * @return ArrayObject
     */
    public static function readConfigString($str)
    {
        $xmlData = simplexml_load_string($str) or die("Error: cannot parse XML String");
        //todo look into associative arrays
        return self::parseXML($xmlData);
    }
}