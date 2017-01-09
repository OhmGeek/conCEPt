<?php

class DB {

    public static function getDB()
    {
        return new PDO("mysql:host=mysql.dur.ac.uk;dbname=",'dcs8s04','when58');
    }

}

