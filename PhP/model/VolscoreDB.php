<?php
require 'IVolscoreDb.php';

class VolscoreDB implements IVolscoreDb {

    public static function connexionDB()
    {
        require '.credentials.php';
        $PDO = new PDO('mysql:host=localhost;dbname=volscore', 'root', 'root');
        return $PDO;
    }
    
    public static function executeInsertQuery($query) : int
    {
        try
        {
            $dbh = self::connexionDB();
            $statement = $dbh->prepare($query); // Prepare query
            $statement->execute(); // Executer la query
            $res = $dbh->lastInsertId();
            $dbh = null;
            return $res;
        } catch (PDOException $e) {
            print 'Error!:' . $e->getMessage() . '<br/>';
            return null;
        }
    }
    public static function getTeams() : array
    {
        try
        {
            $dbh = self::connexionDB();
            $query = "SELECT * FROM teams";
            $statement = $dbh->prepare($query); // Prepare query
            $statement->execute(); // Executer la query
            $res = [];
            while ($row = $statement->fetch()) {
                $res[] = new Team($row);
            }
            $dbh = null;
            return $res;
        } catch (PDOException $e) {
            print 'Error!:' . $e->getMessage() . '<br/>';
            return null;
        }
    }
    
    public static function getGames() : array
    {
        try
        {
            $dbh = self::connexionDB();
            $query = 
                "SELECT games.id as number, type, level,category,league,receiving_id,r.name as receivingTeamName,visiting_id,v.name as visitingTeamName,location,venue,moment ".
                "FROM games INNER JOIN teams r ON games.receiving_id = r.id INNER JOIN teams v ON games.visiting_id = v.id";
            $statement = $dbh->prepare($query); // Prepare query
            $statement->execute(); // Executer la query
            $res = [];
            while ($row = $statement->fetch()) {
                $res[] = new Game($row);
            }
            $dbh = null;
            return $res;
        } catch (PDOException $e) {
            print 'Error!:' . $e->getMessage() . '<br/>';
            return null;
        }
    }
    
    public static function getTeam($number) : Team
    {
        try
        {
            $dbh = self::connexionDB();
            $query = "SELECT * FROM teams WHERE id = $number";
            $statement = $dbh->prepare($query); // Prepare query
            $statement->execute(); // Executer la query
            $queryResult = $statement->fetch(); // Affiche les résultats
            $dbh = null;
            return new Team($queryResult);
        } catch (PDOException $e) {
            print 'Error!:' . $e->getMessage() . '<br/>';
            return null;
        }
    }
    public static function getGame($number)
    {
        throw new Exception("Not implemented yet");
    }
    public static function getPlayers($teamid)
    {
        throw new Exception("Not implemented yet");
    }

    public static function getCaptain($teamid) : Member
    {
        try
        {
            $dbh = self::connexionDB();
            $query = "SELECT * FROM members WHERE team_id = $teamid AND role='C'";
            $statement = $dbh->prepare($query); // Prepare query
            $statement->execute(); // Executer la query
            $queryResult = $statement->fetch(); // Affiche les résultats
            $dbh = null;
            return new Member($queryResult);
        } catch (PDOException $e) {
            print 'Error!:' . $e->getMessage() . '<br/>';
            return null;
        }
    }

    public static function getLibero($teamid) : Member
    {
        try
        {
            $dbh = self::connexionDB();
            $query = "SELECT * FROM members WHERE team_id = $teamid AND libero=1";
            $statement = $dbh->prepare($query); // Prepare query
            $statement->execute(); // Executer la query
            $queryResult = $statement->fetch(); // Affiche les résultats
            $dbh = null;
            return new Member($queryResult);
        } catch (PDOException $e) {
            print 'Error!:' . $e->getMessage() . '<br/>';
            return null;
        }
    }

    public static function getBenchPlayers($gameid,$setid,$teamid)
    {
        throw new Exception("Not implemented yet");
    }

    public static function getSets($game) 
    {
        $dbh = self::connexionDB();
        $res = array();
      
        $query = "SELECT sets.id, number, start, end, game_id, ".
                 "(SELECT COUNT(points_on_serve.id) FROM points_on_serve WHERE team_id = receiving_id and set_id = sets.id) as recscore, ".
                 "(SELECT COUNT(points_on_serve.id) FROM points_on_serve WHERE team_id = visiting_id and set_id = sets.id) as visscore ".
                 "FROM games INNER JOIN sets ON games.id = sets.game_id ".
                 "WHERE game_id = $game->number ".
                 "ORDER BY sets.number";
      
        $statement = $dbh->prepare($query); // Prepare query
        $statement->execute(); // Executer la query
        $queryResult = $statement->fetch(); 
        while ($row = $statement->fetch()) {
            $newset = array(
                "game" => $row['game_id'],
                "number" => $row['number']
            );
            $newset['Id'] = $row['id'];
            if (!is_null($row['start'])) $newset['start'] = $row['start'];
            if (!is_null($row['end'])) $newset['end'] = $row['end'];
            if (!is_null($row['recscore'])) $newset['scoreReceiving'] = intval($row['recscore']);
            if (!is_null($row['visscore'])) $newset['scoreVisiting'] = intval($row['visscore']);
        
            array_push($res, new Set($newset));
        }
        $dbh = null;
        return $res;
      }
      
    public static function gameIsOver($game) : bool
    {
        $sets = VolscoreDB::getSets($game);
        $recwin = 0;
        $viswin = 0;
        foreach ($sets as $set) {
            if ($set->scoreReceiving > $set->scoreVisiting) $recwin++;
            if ($set->scoreReceiving < $set->scoreVisiting) $viswin++;
        }
        return ($recwin == 3 || $viswin == 3);
        // TODO handle 5th set score at 15
    }
      
    public static function addSet($game) 
    {
        $dbh = self::connexionDB();
        $sets = VolscoreDB::getSets($game);
        if (count($sets) >= 5) return -2;
        $query = "INSERT INTO sets (number,game_id) VALUES(". (count($sets)+1) .",". $game->number .");";
        return self::executeInsertQuery($query);
    }
      
}


?>
