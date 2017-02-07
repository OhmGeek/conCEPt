<?php

/**
 * Created by PhpStorm.
 * User: ryan
 * Date: 12/01/17
 * Time: 12:19
 */
namespace Concept\Model;

use PDO;

class CISUserDataModel
{

    public function __construct()
    {
        $this->db = UserDataDB::getDB();
    }

    // returns an associated array containing all user details
    public function getAllData($username)
    {
        $statement = $this->db->prepare("SELECT *
                                         FROM UserDetails
                                         WHERE username = :user");

        $statement->bindValue(':user', $username, PDO::PARAM_STR);
        $statement->execute();

        $results = $statement->fetchAll(PDO::FETCH_ASSOC, true);

        return $results;
    }

    public function getSimilarUsernamesFromName($name)
    {
        // todo use concat with two db queries (nested)
        $statement = $this->db->prepare("SELECT *
                                         FROM UserDetails
                                         WHERE firstnames LIKE %{:NAME}% OR surname LIKE %{:NAME}");

        $statement->bindValue(':name', $name, PDO::PARAM_STR);
        $statement->execute();

        $results = $statement->fetchAll(PDO::FETCH_ASSOC, true);

        return $results;
    }
}