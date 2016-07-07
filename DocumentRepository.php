<?php

namespace Mao\ArangoDb;

use Mao\ArangoDb\Internal\DocumentHandler;

/**
 * Reflects original DocumentHandler without unnecessary passing collectionId to each method
 *
 * @package Mao\ArangoDb
 */
class DocumentRepository
{

    /**
     * CollectionId
     *
     * @var string
     */
    protected $_collection;

    /**
     * Object handling mapping to original ArangoDB library
     *
     * @var DocumentHandler
     */
    protected $_handler;

    /**
     * ArangoDB Connection
     *
     * @var Connection
     */
    protected $_connection;

    protected $_documentClass = '\triagens\ArangoDb\Document';

    /**
     * DocumentRepository constructor.
     *
     * @param $connection
     * @throws \Exception
     */
    public function __construct($connection)
    {
        $this->_connection = $connection;
        $this->_handler    = new DocumentHandler($connection, $this->_documentClass);

        if (empty($this->_collection)) {
            throw new \Exception('Missing _collection value !!!');
        }
    }

    /**
     * Get a single document from a collection
     *
     * This will throw if the document cannot be fetched from the server.
     *
     * @throws \Exception
     *
     * @param mixed $documentId   - document identifier
     * @param array $options      - optional, array of options
     *                            <p>Options are :
     *                            <li>'_includeInternals' - true to include the internal attributes. Defaults to false</li>
     *                            <li>'includeInternals' - Deprecated, please use '_includeInternals'.</li>
     *                            <li>'_ignoreHiddenAttributes' - true to show hidden attributes. Defaults to false</li>
     *                            <li>'ignoreHiddenAttributes' - Deprecated, please use '_ignoreHiddenAttributes'.</li>
     *                            <li>'ifMatch' - boolean if given revision should match or not</li>
     *                            <li>'revision' - The document is returned if it matches/not matches revision.</li>
     *                            </p>
     *
     * @return Document - the document fetched from the server
     */
    public function getById($documentId, array $options = [])
    {
        return $this->_handler->getById($this->_collection, $documentId, $options);
    }


    /**
     * Check if a document exists
     *
     * This will call self::get() internally and checks if there
     * was an exception thrown which represents an 404 request.
     *
     * @param mixed $documentId - document identifier
     * @return boolean
     */
    public function has($documentId)
    {
        return $this->_handler->has($this->_collection, $documentId);
    }


    /**
     * Gets information about a single documents from a collection
     *
     * This will throw if the document cannot be fetched from the server
     *
     * @param mixed $documentId - document identifier.
     * @param null  $revision
     * @param null  $ifMatch
     * @return array - an array containing the complete header including the key httpCode.
     * @internal param $collectionId
     * @internal param ifMatch $boolean -  boolean if given revision should match or not.
     * @internal param revision $string - The document is returned if it matches/not matches revision.
     */
    public function getHead($documentId, $revision = null, $ifMatch = null)
    {
        return $this->_handler->getHead($this->_collection, $documentId, $revision, $ifMatch);
    }

    /**
     * save a document to a collection
     *
     * This will add the document to the collection and return the document's id
     *
     * This will throw if the document cannot be saved
     *
     * @param mixed      $document     - the document to be added, can be passed as a document or an array
     * @param bool|array $options      - optional, prior to v1.2.0 this was a boolean value for create. Since v1.0.0 it's an array of options.
     *                                 <p>Options are :<br>
     *                                 <li>'create' - create the collection if it does not yet exist.</li>
     *                                 <li>'waitForSync' -  if set to true, then all removal operations will instantly be synchronised to disk / If this is not specified, then the collection's default sync behavior will be applied.</li>
     *                                 </p>
     * @return mixed
     * @internal param mixed $collectionId - collection id as string or number
     * @since    1.0
     */
    public function save($document, $options = [])
    {
        return $this->_handler->save($this->_handler, $document, $options);
    }

    /**
     * Store a document to a collection
     *
     * This is an alias/shortcut to save() and replace(). Instead of having to determine which of the 3 functions to use,
     * simply pass the document to store() and it will figure out which one to call.
     *
     * This will throw if the document cannot be saved or replaced.
     *
     * @throws \Exception
     *
     * @param Document   $document     - the document to be added, can be passed as a document or an array
     * @param mixed      $collectionId - collection id as string or number
     * @param bool|array $options      - optional, prior to v1.2.0 this was a boolean value for create. Since v1.2.0 it's an array of options.
     *                                 <p>Options are :<br>
     *                                 <li>'create' - create the collection if it does not yet exist.</li>
     *                                 <li>'waitForSync' -  if set to true, then all removal operations will instantly be synchronised to disk / If this is not specified, then the collection's default sync behavior will be applied.</li>
     *                                 </p>
     *
     * @return mixed - id of document created
     * @since 1.0
     */
    public function store(Document $document, $collectionId = null, $options = [])
    {
        return $this->_handler->store($document, $collectionId, $options);
    }

    /**
     * Replace an existing document in a collection, identified by the document itself
     *
     * This will update the document on the server
     *
     * This will throw if the document cannot be updated
     *
     * If policy is set to error (locally or globally through the ConnectionOptions)
     * and the passed document has a _rev value set, the database will check
     * that the revision of the to-be-replaced document is the same as the one given.
     *
     * @throws \Exception
     *
     * @param Document $document - document to be updated
     * @param mixed    $options  - optional, array of options (see below) or the boolean value for $policy (for compatibility prior to version 1.1 of this method)
     *                           <p>Options are :
     *                           <li>'policy' - update policy to be used in case of conflict ('error', 'last' or NULL [use default])</li>
     *                           <li>'waitForSync' - can be used to force synchronisation of the document update operation to disk even in case that the waitForSync flag had been disabled for the entire collection</li>
     *                           </p>
     *
     * @return bool - always true, will throw if there is an error
     */
    public function replace(Document $document, $options = [])
    {
        return $this->_handler->replace($document, $options);
    }

    /**
     * Replace an existing document in a collection, identified by collection id and document id
     *
     * This will update the document on the server
     *
     * This will throw if the document cannot be Replaced
     *
     * If policy is set to error (locally or globally through the ConnectionOptions)
     * and the passed document has a _rev value set, the database will check
     * that the revision of the to-be-replaced document is the same as the one given.
     *
     *
     * @param mixed    $documentId   - document id as string or number
     * @param Document $document     - document to be updated
     * @param mixed    $options      - optional, array of options (see below) or the boolean value for $policy (for compatibility prior to version 1.1 of this method)
     *                               <p>Options are :
     *                               <li>'policy' - update policy to be used in case of conflict ('error', 'last' or NULL [use default])</li>
     *                               <li>'waitForSync' - can be used to force synchronisation of the document replacement operation to disk even in case that the waitForSync flag had been disabled for the entire collection</li>
     *                               </p>
     * @return bool
     * @internal param mixed $collectionId - collection id as string or number
     */
    public function replaceById($documentId, Document $document, $options = [])
    {
        return $this->_handler->replaceById($this->_collection, $documentId, $document, $options);
    }

    /**
     * Update an existing document in a collection, identified by the including _id and optionally _rev in the patch document.
     * Attention - The behavior of this method has changed since version 1.1
     *
     * This will update the document on the server
     *
     * This will throw if the document cannot be updated
     *
     * If policy is set to error (locally or globally through the ConnectionOptions)
     * and the passed document has a _rev value set, the database will check
     * that the revision of the document to-be-replaced is the same as the one given.
     *
     * @throws \Exception
     *
     * @param Document $document - The patch document that will update the document in question
     * @param mixed    $options  - optional, array of options (see below) or the boolean value for $policy (for compatibility prior to version 1.1 of this method)
     *                           <p>Options are :
     *                           <li>'policy' - update policy to be used in case of conflict ('error', 'last' or NULL [use default])</li>
     *                           <li>'keepNull' - can be used to instruct ArangoDB to delete existing attributes instead setting their values to null. Defaults to true (keep attributes when set to null)</li>
     *                           <li>'waitForSync' - can be used to force synchronisation of the document update operation to disk even in case that the waitForSync flag had been disabled for the entire collection</li>
     *                           </p>
     *
     * @return bool - always true, will throw if there is an error
     */
    public function update(Document $document, $options = [])
    {
        return $this->_handler->update($document, $options);
    }

    /**
     * Update an existing document in a collection, identified by collection id and document id
     * Attention - The behavior of this method has changed since version 1.1
     *
     * This will update the document on the server
     *
     * This will throw if the document cannot be updated
     *
     * If policy is set to error (locally or globally through the ConnectionOptions)
     * and the passed document has a _rev value set, the database will check
     * that the revision of the document to-be-updated is the same as the one given.
     *
     *
     * @param mixed    $documentId   - document id as string or number
     * @param Document $document     - patch document which contains the attributes and values to be updated
     * @param mixed    $options      - optional, array of options (see below) or the boolean value for $policy (for compatibility prior to version 1.1 of this method)
     *                               <p>Options are :
     *                               <li>'policy' - update policy to be used in case of conflict ('error', 'last' or NULL [use default])</li>
     *                               <li>'keepNull' - can be used to instruct ArangoDB to delete existing attributes instead setting their values to null. Defaults to true (keep attributes when set to null)</li>
     *                               <li>'waitForSync' - can be used to force synchronisation of the document update operation to disk even in case that the waitForSync flag had been disabled for the entire collection</li>
     *                               </p>
     * @return bool
     * @internal param string $collectionId - collection id as string or number
     */
    public function updateById($documentId, Document $document, $options = [])
    {
        return $this->_handler->updateById($this->_collection, $documentId, $document, $options);
    }

    /**
     * Remove a document from a collection, identified by the document itself
     *
     * @throws \Exception
     *
     * @param Document $document - document to be removed
     * @param mixed    $options  - optional, array of options (see below) or the boolean value for $policy (for compatibility prior to version 1.1 of this method)
     *                           <p>Options are :
     *                           <li>'policy' - update policy to be used in case of conflict ('error', 'last' or NULL [use default])</li>
     *                           <li>'waitForSync' - can be used to force synchronisation of the document removal operation to disk even in case that the waitForSync flag had been disabled for the entire collection</li>
     *                           </p>
     *
     * @return bool - always true, will throw if there is an error
     */
    public function remove(Document $document, $options = [])
    {
        return $this->_handler->remove($document, $options);
    }

    /**
     * Remove a document from a collection, identified by the collection id and document id
     *
     *
     * @param mixed  $documentId   - document id as string or number
     * @param  mixed $revision     - optional revision of the document to be deleted
     * @param mixed  $options      - optional, array of options (see below) or the boolean value for $policy (for compatibility prior to version 1.1 of this method)
     *                             <p>Options are :
     *                             <li>'policy' - update policy to be used in case of conflict ('error', 'last' or NULL [use default])</li>
     *                             <li>'waitForSync' - can be used to force synchronisation of the document removal operation to disk even in case that the waitForSync flag had been disabled for the entire collection</li>
     *                             </p>
     * @return bool
     * @internal param mixed $collectionId - collection id as string or number
     */
    public function removeById($documentId, $revision = null, $options = [])
    {
        return $this->_handler->removeById($this->_collection, $documentId, $revision, $options);
    }

}