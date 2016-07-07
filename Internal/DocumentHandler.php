<?php

namespace Mao\ArangoDb\Internal;


use Mao\ArangoDb\Connection;
use Mao\ArangoDb\Document;

class DocumentHandler extends \triagens\ArangoDb\DocumentHandler
{

    protected $_documentClass = '\triagens\ArangoDb\Document';

    /**
     * Construct a new handler
     *
     * @param Connection $connection    - connection to be used
     * @param string     $documentClass - document class to use
     */
    public function __construct(Connection $connection, $documentClass)
    {
        // Check for namespace alias
        if (strpos($documentClass, ':') !== false) {
            list($namespaceAlias, $simpleClassName) = explode(':', $documentClass, 2);
            if (is_string($namespaceAlias)) {
                global $kernel;
                $bundle        = $kernel->getContainer()->get('kernel')->getBundle($namespaceAlias);
                $path          = $bundle->getNamespace();
                $documentClass = $path . '\\Document\\' . $simpleClassName;
            } else {
                throw new \Exception('Unrecognized documentClass alias');
            }
        }
        $this->_documentClass = $documentClass;

        parent::__construct($connection);
    }


    /**
     * Intermediate function to call the createFromArray function from the right context
     *
     * @param $data
     * @param $options
     *
     * @return Document
     */
    protected function createFromArrayWithContext($data, $options)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return ($this->_documentClass)::createFromArray($data, $options);
    }

}