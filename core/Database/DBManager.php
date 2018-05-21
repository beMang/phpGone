<?php

namespace phpGone\Database;

class DBManager
{
    protected $pdoInstances = [];
    static protected $selfInstance = null;

    public function __construct()
    {
        //$this->addDatabase(); Initialize default db in config
    }

    public static function getInstance()
    {
        if (is_null(DBManager::$selfInstance)) {
            DBManager::$selfInstance = new DBManager();
            return DBManager::$selfInstance;
        } else {
            return DBManager::$selfInstance;
        }
    }

    public function addDatabase($name, $hostAndDb = 'mysql:host=localhost;dbname=test', $user = 'root', $passwd = '')
    {
        if (is_string($name) && !empty($name)) {
            if ($this->dataBaseExist($name) == false) {
                $pdoInstance = new \PDO(
                    $hostAndDb,
                    $user,
                    $passwd,
                    [\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"]
                );
                $pdoInstance->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                $this->defineClasses($name, $pdoInstance);
                $this->pdoInstances [$name] = $pdoInstance;
                return true;
            } else {
                throw new \RuntimeException('La base de donnée existe déjà.');
            }
        } else {
            throw new \InvalidArgumentException('L\'identifiant de la db doit être une chaine de caractères non-vides');
        }
    }

    public function getDatabase($name)
    {
        if (is_string($name)) {
            if (isset($this->pdoInstances[$name])) {
                return $this->pdoInstances[$name];
            } else {
                throw new \RuntimeException('La base de donnée est inexistante');
            }
        } else {
            throw new \InvalidArgumentException('L\'identifiant doit être une chaine de caractères');
        }
    }

    public function dataBaseExist($name)
    {
        if (is_string($name)) {
            if (isset($this->pdoInstances[$name])) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    private function defineClasses($name, \PDO $pdoInstance)
    {
        $tables = $pdoInstance->query('SHOW TABLES')->fetchAll();
        foreach ($tables as $table) {
            $columns = $pdoInstance->query('SHOW COLUMNS FROM ' . $table[0])->fetchAll();
            $classcode = 'class ' . $name . '_' . $table[0] .' { ';
            foreach ($columns as $column) {
                $classcode .= 'public $' . $column['Field'] . ';';
            }
            $classcode .= "} ";
            eval($classcode);
        }
    }

    public function sql($sqlQuery, $params = false, $db = 'base')
    {
        $db = $this->getDatabase($db);
        $query = $db->prepare($sqlQuery);
        try {
            if ($params == false) {
                $query->execute();
            } else {
                $query->execute($params);
            }
        } catch (\PDOException $e) {
            throw new \Exception('Error sql');
        }
        $query->setFetchMode(\PDO::FETCH_OBJ);
        return $query->fetchAll();
    }
}
