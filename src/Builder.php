<?php

namespace Database\FastDB;

use Database\FastDB\Query;

/**
 * Builder
 */
class Builder
{
        
    /**
     * server
     *
     * @var mixed
     */
    private static $server;

    /**
     * username
     *
     * @var mixed
     */
    private static $username;

    /**
     * password
     *
     * @var mixed
     */
    private static $password;

    /**
     * dbanme
     *
     * @var mixed
     */
    private static $dbname;

    /**
     * generateQueryCommand
     *
     * @var mixed
     */
    private static $generateQueryCommand;

    /**
     * fullCommand
     *
     * @var mixed
     */
    private static $fullCommand = [];

    /**
     * connect
     *
     * @var mixed
     */
    private static $connect;

    /**
     * perkCommand
     *
     * @var mixed
     */
    private static $perkCommand = [];

    /**
     * flags
     *
     * @var mixed
     */
    private static $flags;

    /**
     * fullFlag
     *
     * @var mixed
     */
    private static $fullFlag;

    /**
     * __construct
     *
     * @return void
     */
    private function __construct() {}
    
    /**
     * connect
     *
     * @param  mixed $server
     * @param  mixed $username
     * @param  mixed $password
     * @param  mixed $dbname
     * @return void
     */
    static public function connect($server, $username, $password = null, $dbname)
    {

        $query = new Query($server, $username, $password, $dbname);

        self::$connect = $query;

        return new self();

    }
    
    /**
     * table
     *
     * @param  mixed $table
     * @return void
     */
    static public function table($table)
    {

        self::$perkCommand['table'] = '`'.$table.'`';

        return new self();

    }
    
    /**
     * show
     *
     * @return void
     */
    static public function show()
    {

        $fields = self::$perkCommand['fields'] ?? ['ALL'];

        self::$fullCommand = 'SHOW `'.implode(',', $fields).'` FROM TABLE '.self::$perkCommand['table'];

        return new self();

    }
    
    /**
     * write
     *
     * @return void
     */
    static public function write()
    {

        $fields = self::$perkCommand['fields'] ?? [];
        $values = self::$perkCommand['values'] ?? [];

        self::$fullCommand = 'WRITE DATA TO THE '.self::$perkCommand['table'].' ('.implode(',', $fields).') VALUES ('.implode(',', $values).')';

        return new self();

    }
    
    /**
     * update
     *
     * @return void
     */
    static public function update()
    {

        $fields = self::$perkCommand['fields'] ?? [];
        $values = self::$perkCommand['values'] ?? [];

        self::$fullCommand = 'UPDATE DATA IN TABLE '.self::$perkCommand['table'].' ('.implode(',', $fields).') VALUES ('.implode(',', $values).')';

        return new self();

    }
    
    /**
     * delete
     *
     * @return void
     */
    static public function delete()
    {

        self::$fullCommand = 'DELETE DATA FROM TABLE '.self::$perkCommand['table'];

        return new self();

    }
    
    /**
     * count
     *
     * @return void
     */
    static public function count()
    {

        self::$fullCommand = 'COUNT RECORDS IN TABLE '.self::$perkCommand['table'];

        return new self();

    }

    /**
     * flags
     *
     * @param  mixed $flags
     * @return void
     */
    static public function flags(callable $flags)
    {

        self::$flags = [];

        call_user_func($flags);

        $flagsArray = [];

        (isset(self::$flags['where'])) ? $flagsArray['where'] = 'WHERE('.implode(' AND ', self::$flags['where']).')' : null;
        (isset(self::$flags['limit'])) ? $flagsArray['limit'] = 'LIMIT('.self::$flags['limit'].')' : null;
        (isset(self::$flags['sort'])) ? $flagsArray['sort'] = 'SORT('.self::$flags['sort'].')' : null;

        $flagsString = null;

        if(count($flagsArray) > 0)
        {
            foreach($flagsArray as $kFlag => $vFlag)
            {
                $flagsString .= $vFlag.' ';
            }
        }

        self::$fullFlag = ' FLAGS{'.$flagsString.'}';

        return new self();

    }
    
    /**
     * fields
     *
     * @param  mixed $fields
     * @return void
     */
    static public function fields(...$fields)
    {

        self::$perkCommand['fields'] = $fields;

        return new self();

    }
    
    /**
     * values
     *
     * @param  mixed $values
     * @return void
     */
    static public function values(...$values)
    {

        self::$perkCommand['values'] = $values;

        return new self();

    }
    
    /**
     * where
     *
     * @param  mixed $where
     * @return void
     */
    static public function where($where)
    {

        self::$flags['where'][] = $where;

        return new self();

    }
    
    /**
     * limit
     *
     * @param  mixed $from
     * @param  mixed $before
     * @return void
     */
    static public function limit($from = 1, $before = 1)
    {

        $limit = [$from, $before];

        self::$flags['limit'] = implode(',', $limit);

        return new self();

    }
    
    /**
     * sort
     *
     * @param  mixed $field
     * @param  mixed $sort
     * @return void
     */
    static public function sort($field, $sort)
    {

        $sort = [$field, $sort];

        self::$flags['sort'] = implode('=', $sort);

        return new self();

    }
    
    /**
     * query
     *
     * @return void
     */
    static public function query()
    {

        return self::$connect->query(self::$fullCommand.self::$fullFlag);

    }

}