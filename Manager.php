<?php

namespace Mao\ArangoDb;

use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * Documents repository manager
 *
 * @package Mao\ArangoDb\Repository
 */
class Manager
{
    protected $_connection;
    protected $_documentsRepository = [];

    /**
     * Manager constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->_connection = $connection;
    }

    /**
     * Returns Document Repository
     *
     * @param $documentRepository string
     * @return DocumentRepository
     */
    public function getRepository($documentRepository)
    {
        if (isset($this->_documentsRepository[$documentRepository])) {
            return $this->_documentsRepository[$documentRepository];
        }

        $className = ltrim($documentRepository, '\\');

        // Check for namespace alias
        if (strpos($className, ':') !== false) {
            list($namespaceAlias, $simpleClassName) = explode(':', $className, 2);
            if (is_string($namespaceAlias)) {
                global $kernel;
                $bundle    = $kernel->getContainer()->get('kernel')->getBundle($namespaceAlias);
                $path      = $bundle->getNamespace();
                $className = $path . '\\Repository\\' . $simpleClassName;
            } else {
                throw new Exception('Unrecognized bundle alias');
            }
        }

        $class = new $className($this->_connection);

        $this->_documentsRepository[$documentRepository] = $class;

        return $class;
    }

}