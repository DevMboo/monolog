# Monolog PHP Framework 🎤

Welcome to **Monolog**, a custom PHP framework for building modern and dynamic web applications. This framework is designed to be simple, flexible, and efficient for both small and large projects.

---

## 🛠️ Framework Overview

**Monolog** is a PHP framework that streamlines the development process by providing easy-to-use CLI commands, project scaffolding, and environment configurations. It helps you quickly build out essential features like controllers, models, migrations, views, and more.

It was built using PHP 8.3.x and relies on **Composer** for dependency management.

---

## 🗂️ Project Structure

Here's a breakdown of the directory structure in the **Monolog** project:

- **`/src`**: Contains the main source files for the framework.
  - **`/app`**: Core application files.
    - **`/config`**: Configuration files (e.g., `stacking.php`).
    - **`/console/commands`**: Holds all the custom CLI commands.
      - `MakeComponent.php`
      - `MakeController.php`
      - `MakeLayout.php`
      - `MakeMigrate.php`
      - `MakeMiddleware.php`
      - `MakeMigration.php`
      - `MakeModel.php`
      - `MakeSeeder.php`
      - `MakeView.php`
      - `ServerCommand.php`
    - **`/controller`**: Contains controller files.
      - `Controller.php`
    - **`/helpers`**: Helper files for different purposes.
      - **`/common`**: Common helpers like `env.php`.
      - **`/database`**: Helpers related to database, e.g., `Migration.php`.
      - **`/environment`**: Environment helpers like `Env.php`.
    - **`/http`**: HTTP-related files such as requests and responses.
      - `Request.php`
      - `Response.php`
      - `Router.php`
    - **`/middlewares`**: Middleware files for API and Web.
      - **`/api`**: API-related middlewares.
        - `EnsureApplicationJWT.php`
      - **`/web`**: Web-related middlewares.
        - `EnsureApplicationActive.php`
        - `EnsureApplicationCrsfToken.php`
        - `EnsureAutenticateSessionUser.php`
    - **`/resources`**: Resource files like views and components.
      - **`Component.php`**
      - **`View.php`**
      - **`/views`**: Views of the application.
        - `public.html`
    - **`/database`**: Database-related files.
      - **`Database.php`**
      - **`/migrations`**: Migration files for the database schema.
        - `2025_03_04_01_52_03_create_migrations_table.php`
      - **`/seeders`**: Database seeding files.
    - **`/model`**: Contains model files.
      - `Model.php`
    - **`/router`**: Contains routing files.
      - `web.php`
    - **`/view`**: Stores view files for the application.
      - **`/public`**: Public assets like CSS, images, and JS.
        - **`/css`**
        - **`/ico`**
        - **`/images`**
        - **`/js`**
      - **`/resources`**: Stores additional resource files.
        - **`/views`**: Stores view files.
          - `app.html`
          - **`/components`**: Individual components like `banner.html`.
          - **`/pages`**: Static page views like `home.html`.

- **`/vendor`**: Composer dependencies and autoload files.
  - `autoload.php`
  - **`/composer`**: Composer-specific files.
    - `autoload_classmap.php`
    - `autoload_namespaces.php`
    - `autoload_psr4.php`
    - `autoload_real.php`
    - `autoload_static.php`
    - `ClassLoader.php`
    - `installed.json`
    - `installed.php`
    - `InstalledVersions.php`
    - `LICENSE`
    - `platform_check.php`

- **`/resources`**: Stores resources like layouts, static views, and the environment file (`.env`).

- **`/node_modules`**: Contains the installed NPM packages for front-end development, including Tailwind CSS.

---

## 📋 Features

- **Custom CLI commands**: Automate repetitive tasks such as creating controllers, models, migrations, views, etc.
- **Simple and clean structure**: Based on the MVC pattern to promote scalability and maintainability.
- **Environment configuration**: Using `.env` files to manage environment variables for different environments (e.g., development, production).
- **Tailwind CSS Integration**: Easily configure Tailwind for front-end styling.

---

## ⚙️ Installation & Setup

To get started with **Monolog**, follow these steps:

### 1. Install project CLI

```bash
composer require devmboo/monolog
```

### 2. Navigate to the project directory

```bash
cd your-project-directory
```

### 3. Install dependencies using Composer

Ensure that you have [Composer](https://getcomposer.org/) installed. If you don't have it, you can install it by following the instructions on the official website.

```bash
composer install
```

This will install all the required dependencies for the project.

### 4. Install front-end dependencies (Tailwind CSS)

Monolog uses Tailwind CSS for styling. To install the required NPM packages, run the following:

```bash
npm install
```

This will install Tailwind and other necessary front-end dependencies.

### 5. Configure the `.env` file

Rename the `.env.example` file to `.env` and fill in the necessary configuration details:

```
#### APPLICATION ####
APP_KEY= 
APP_LOCAL=homolog
APP_URL=http://localhost
APP_NAME=monolog
APP_PORT=8000

#### DATABASE ####
DB_DRIVER=YOUR_DRIVER
DB_HOST=YOUR_HOST
DB_NAME=YOUR_DB_NAME
DB_USER=YOUR_USER
DB_PASSWORD=YOUR_PASSWORD
DB_PORT=YOUR_PORT

#### SUPPORT MODE ####
STANDARD_MODE=false

#### ENCRYPTY KEY ####
ENCRYPT=

#### MAILER SETTINGS ####
MAIL_DRIVER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=your_email@example.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=no-reply@example.com
MAIL_FROM_NAME="Monolog System"
```

### 6. Build the front-end assets

Once NPM packages are installed, you can build the front-end assets by running:

```bash
npm run dev
```

This will compile your assets, including Tailwind CSS, and make them ready for use.

### 7. Start the development server

Use the following command to start the server:

```bash
php mono.php server
```

This command will launch the built-in PHP server using the port defined in your `.env` file. By default, it uses port `8000`.

### 8. Create layouts and other components

You can create layouts, controllers, models, migrations, and other components using the following commands:

- **Create a new layout**:

```bash
php mono.php make:layout <layout-name>
```

- **Create a new controller**:

```bash
php mono.php make:controller <controller-name>
```

- **Create a new model**:

```bash
php mono.php make:model <model-name>
```

- **Create a new migration**:

```bash
php mono.php make:migration <migration-name>
```

- **Create a new view**:

```bash
php mono.php make:view <view-name>
```

- **Create a new seeder**:

```bash
php mono.php make:seeder <seeder-name>
```

---

## 🚀 How to Code

1. **Controllers**: Store your controllers in the `/src/app/controllers` directory. You can generate them using the `make:controller` command.
2. **Models**: Create models in `/src/app/models`. Use the `make:model` command for automatic generation.
3. **Migrations**: Define your database structure using migrations. Generate them with `make:migration`.
4. **Views**: All views are stored in `/resources/views`. You can create views with `make:view` or create layouts that are used globally.
5. **Layouts**: Use layouts for structuring the HTML template of your application. These can be created using the `make:layout` command.

You can easily extend and modify any part of this framework to meet your project's needs.

---

## 🔧 Available CLI Commands

Here’s a list of the available commands:

| Command                          | Description                                           |
|----------------------------------|-------------------------------------------------------|
| `php mono.php server`            | Starts the built-in PHP server.                       |
| `php mono.php standard`            | Disables the application.                       |
| `php mono.php make:controller <name>` | Creates a new controller.                         |
| `php mono.php make:model <name>` | Creates a new model.                               |
| `php mono.php make:migration <name>` | Creates a new migration file.                     |
| `php mono.php make:middleware <name>` | Creates a new middleware file.                     |
| `php mono.php make:seeder <name>` | Creates a new seeder.                             |
| `php mono.php make:view <name>` | Creates a new view file.                           |
| `php mono.php make:layout <name>` | Creates a new layout template.                    |
| `php mono.php make:component <name>` | Creates a new component file.                    |
| `php mono.php make:mail <name>`  | Creates a new mail layout file template.            |
| `php mono.php make:seed`         | Executes all seeder classes to insert initial data into the database. |
| `php mono.php make:migrate`      | Executes all pending migrations in the database.    |

---

## 📄 License

This project is open source and available under the [MIT License](LICENSE).

---

### 🔑 **Documentation Summary** 
| Method | Description | 
| --- | --- | 
| `handle()` | Orchestrates the application's initialization, including environment loading, request capturing, and route dispatching. |

---

# Monolog - Basic CRUD Example

## Introduction
This document provides a basic implementation of a CRUD (Create, Read, Update, Delete) system for a `User` model using the `Model` class. The `User` model extends `Model`, leveraging its built-in database methods.

## Prerequisites
- PHP 8+
- PDO Extension enabled
- A properly configured database connection

## User Model Implementation
Create a `User` model that extends `Model` use cli php mono.php make:model <name>:

```php
class User extends Model {
    protected string $tbname = 'users';
}
```

## CRUD Operations

### 1. Create a User
```php
$user = new User();
$created = $user->create([
    'name' => 'John Doe',
    'email' => 'john.doe@example.com',
    'password' => password_hash('secret', PASSWORD_BCRYPT)
]);

if ($created) {
    echo "User created successfully!";
}
```

### 2. Retrieve All Users
```php
$user = new User();
$users = $user->all();
print_r($users);
```

### 3. Find a User by ID
```php
$user = new User();
$foundUser = $user->find(1);
print_r($foundUser);
```

### 4. Update a User
```php
$user = new User();
$updated = $user->update(1, [
    'name' => 'John Updated',
    'email' => 'john.updated@example.com'
]);

if ($updated) {
    echo "User updated successfully!";
}
```

### 5. Delete a User
```php
$user = new User();
$deleted = $user->delete(1);

if ($deleted) {
    echo "User deleted successfully!";
}
```

## Conclusion
This example demonstrates how to implement a basic CRUD system using the `Model` class. You can extend this functionality by adding additional methods and refining validations.

---

/**
 * Monolog PHP Framework.
 * Version 1.0 (2025).
 *
 * Monolog is a fresh take on project structure, inspired by Laravel and CodeIgniter 4.
 * It aims to provide a clean, efficient, and developer-friendly architecture.
 *
 * @see       https://github.com/devMboo/monolog The Monolog GitHub repository
 *
 * @author    Luan Chaves <lchavesdesousa>
 * @copyright 2025 Luan Chaves
 * @license   https://opensource.org/licenses/MIT MIT License
 * @note      This framework is distributed in the hope that it will be useful, 
 *            but WITHOUT ANY WARRANTY; without even the implied warranty of 
 *            MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

---

## 💬 Questions?

Feel free to open an issue or ask questions through our discussion board. Happy coding! 😊
