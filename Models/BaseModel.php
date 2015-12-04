<?php
/**
 * Created by PhpStorm.
 * User: Stefan
 * Date: 1-12-2015
 * Time: 16:05
 */
namespace CodeSnippet\Models;

abstract class BaseModel
{
    protected $db_id = null;

    /**
     * Create a new object with the supplied parameters.
     *
     * @param array $data Associative array consisting of the required creation parameters.
     */
    abstract protected function create($data);

    /**
     * Instantiates an existing object with the supplied parameters.
     *
     * @param array $data Associative array consisting of the required instantiation parameters.
     */
    abstract protected function instantiate($data);

    /**
     * Loads an existing object by id.
     *
     * @param int $id Database record id to load.
     */
    abstract protected function load($id);

    /**
     * Saves the object to the database.
     */
    abstract public function save();

    /**
     * Deletes the object from the database.
     */
    abstract public function delete();

    /**
     * Creates or instantiates the object depending on parameter type.
     *
     * @param int|array $data Database record id or associative array consisting of the required parameters.
     */
    public function __construct($data)
    {
        if (is_array($data)) {
            if (array_key_exists('id', $data)) {
                $this->instantiate($data);
            } else {
                $this->create($data);
            }
        } elseif (is_numeric($data)) {
            $this->load($data);
        } else {
            throw new \InvalidArgumentException('BaseModel requires either an integer or an array as argument.');
        }
    }

    /**
     * Gets the primary key.
     *
     * @return int
     */

    public function getId()
    {
        return $this->db_id;
    }
}