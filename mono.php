<?php

/**
 * Main script for executing CLI commands.
 * 
 * This script allows the automated creation of controllers, models, migrations,
 * and seeders via the terminal.
 */

require_once __DIR__ . '/vendor/autoload.php';

use Monolog\App\Console\Commands\ServerCommand;
use Monolog\App\Console\Commands\ServerStandardCommand;

use Monolog\App\Console\Commands\MakeComponent;
use Monolog\App\Console\Commands\MakeController;
use Monolog\App\Console\Commands\MakeLayout;
use Monolog\App\Console\Commands\MakeMail;
use Monolog\App\Console\Commands\MakeModel;
use Monolog\App\Console\Commands\MakeMiddleware;
use Monolog\App\Console\Commands\MakeMigrate;
use Monolog\App\Console\Commands\MakeMigration;
use Monolog\App\Console\Commands\MakeSeed;
use Monolog\App\Console\Commands\MakeSeeder;
use Monolog\App\Console\Commands\MakeView;

// Get the command passed via the terminal
$command = $argv[1] ?? null;

if (strpos($command, 'make:') === 0) {
    // Identify the called command and execute the corresponding class
    switch ($command) {
        case 'make:controller':
            /**
             * Command: make:controller
             * Function: Creates a new controller in the project.
             */
            $makeController = new MakeController($argv);
            $makeController->execute();
            break;

        case 'make:model':
            /**
             * Command: make:model
             * Function: Generates a new model for the project.
             */
            $makeModel = new MakeModel($argv);
            $makeModel->execute();
            break;

        case 'make:migration':
            /**
             * Command: make:migration
             * Function: Creates a migration file for database structuring.
             */
            $makeMigration = new MakeMigration($argv);
            $makeMigration->execute();
            break;

        case 'make:seed':
            /**
             * Command: make:seed
             * Function: Executes all seeder classes in the seeders directory by calling their `run` method
             * to insert initial data into the database without the need to manually specify each class.
             */
            $makeSeed = new MakeSeed($argv);
            $makeSeed->execute();
            break;

        case 'make:seeder':
            /**
             * Command: make:seeder
             * Function: Generates a Seeder file for inserting initial data into the database.
             */
            $makeSeeder = new MakeSeeder($argv);
            $makeSeeder->execute();
            break;

        case 'make:migrate':
            /**
             * Command: make:migrate
             * Function: Executes all pending migrations in the database.
             */
            $makeMigrate = new MakeMigrate($argv);
            $makeMigrate->execute();
            break;
        
        case 'make:middleware':
                /**
                 * Command: make:middleware
                 * Function: Generates file middleware.
                 */
                $makeMiddleware = new MakeMiddleware($argv);
                $makeMiddleware->execute();
                break;

        case 'make:view':
            /**
             * Command: make:view
             * Function: Executes create file pages.
             */
            $makeView = new MakeView($argv);
            $makeView->execute();
            break;

        case 'make:layout':
            /**
             * Command: make:layout
             * Function: Creates a new layout file for the application.
             */
            $makeLayout = new MakeLayout($argv);
            $makeLayout->execute();
            break;
        
        case 'make:mail':
            /**
             * Command: make:mail
             * Function: Creates a new layout file template email for the application.
             */
            $makeMail = new MakeMail($argv);
            $makeMail->execute();
            break;

        case 'make:component':
            /**
             * Command: make:component
             * Function: Executes create file component.
             */
            $makeComponent = new MakeComponent($argv);
            $makeComponent->execute();
            break;

        default:
            // Invalid command
            echo "Command not found.\n";
            break;
    }
} elseif ($command === 'server') {
    // Command: server
    // Function: Starts the PHP server using the environment configurations
    $serverCommand = new ServerCommand($argv);
    $serverCommand->execute();
} elseif ($command === 'standard') {
    // Command: standard
    // Function: Disables the application by setting STANDARD_MODE to true
    // This mode can be used for maintenance or restricted access scenarios.
    $serverStandard = new ServerStandardCommand($argv);
    $serverStandard->execute();
}
else {
    // If the command does not start with "make:" or is "server", display an error
    echo "Invalid command.\n";
}
