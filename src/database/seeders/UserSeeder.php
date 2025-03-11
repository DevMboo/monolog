<?php

namespace Monolog\App\Database\Seeders;

use Monolog\Model\User;

/**
 * Seeder class for seeding data for the UserSeeder model.
 */
class UserSeeder {

    /**
     * @var User $model Instance of the User model.
     * 
     * This property holds an instance of the `User` model, which is used
     * for performing CRUD operations and interacting with the `users` table
     * in the database. The `$model` object is initialized in the constructor.
     */
    protected User $model;

    /**
     * Constructor method.
     * 
     * Initializes the `$model` property with an instance of the `User` model.
     * This allows the class to interact with the `User` model and perform
     * actions like creating, reading, updating, or deleting user records.
     */
    public function __construct() {
        $this->model = new User();
    }
    
    /**
     * Run the database seed operation.
     *
     * This method is executed when the seeder is run using mono.php command `php mono.php make:seed`.
     * It populates the database with test or default data.
     *
     * Example:
     * - Insert sample records into the UserSeeder table.
     * 
     * @return void
     */
    public function run() {
        // Your seeding code here
        // $this->model->create([
        //     'name' => 'Test',
        //     'email' => 'test@test.com',
        //     'password' => '12345678'
        // ]);
    }
}