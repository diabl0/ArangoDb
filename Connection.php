<?php

namespace Mao\ArangoDb;

use Mao\ArangoDb\Manager;

/**
 * Connection handler for ArangoDB
 *
 * @package Mao\ArangoDb
 */
class Connection extends \triagens\ArangoDb\Connection
{

    /**
     * @var Manager
     */
    protected $_manager;

    /**
     * @return Manager
     */
    public function getManager()
    {
        if (empty($this->_manager)) {
            $this->_manager = new Manager($this);
        }

        return $this->_manager;
    }
}