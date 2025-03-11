<?php

namespace Monolog\Model;

use Monolog\Model\Model;

/**
 * Model class for interacting with the User table in the database.
 * 
 * This model represents the structure and behavior of the User entity,
 * allowing CRUD operations and interactions with the related database table.
 *
 * Example:
 * - Fetching data related to User
 * - Inserting, updating, or deleting User records in the database.
 *
 * @package Monolog\Model
 */
class User extends Model {

    /**
     * The name of the database table associated with this model.
     *
     * @var string
     */
    protected string $tbname = "users";

}