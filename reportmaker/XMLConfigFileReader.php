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
     */
    public static function readConfigFile($filename)
    {
        //TODO: Read file.
        $xmlStr = "";

        return parseXML($xmlStr);
    }


    /**
     * Parse the XML string and return the XML data
     * @param String
     * @return ArrayObject
     */
    private static function parseXML($xml) {
        $xmldata = simplexml_load_string($xml) or die("Error: cannot parse XML");
	//todo look into associative arrays
        //convert the XMLElementObject to an Array.
        $arr = json_decode(json_encode($xmldata),1);

        return $arr;
    }

    public static function readConfigString($str)
    {
        return parseXML($str);
    }
}
