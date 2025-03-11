<?php

/**
 * -------------------------------------------------------------
 * Global Functionality Loader
 * -------------------------------------------------------------
 * 
 * This file is responsible for including the default functionalities
 * of the application. It loads global methods that are inherited
 * throughout the project's architecture.
 * 
 * The files included here contain essential helper functions that
 * are used across the application, ensuring consistency and reducing
 * code duplication.
 * 
 * -------------------------------------------------------------
 * Standardization of Helper Files
 * -------------------------------------------------------------
 * 
 * All common helper files have been structured inside the `helpers/common/`
 * directory. This organization improves maintainability, making it easier
 * to locate and manage shared functionalities.
 * 
 * The following helper files are included:
 * 
 * - `env.php`       → Provides functions to retrieve environment variables.
 *                     It does not load environment variables directly but 
 *                     helps in managing them through the helper function `env()`.
 * - `crsftoken.php` → Provides functionality to retrieve the current CSRF token.
 *                     It doesn't directly handle the CSRF protection mechanisms,
 *                     but provides the token through the helper function `csrf_token()`.
 * - `debugger.php`  → Provides debugging functions such as `dump()` and `dd()`.
 *                     These functions help in visualizing variables and debugging
 *                     the application more efficiently.
 * 
 */

require_once __DIR__ . '/../helpers/common/env.php'; // Provides helper functions for environment variable retrieval
require_once __DIR__ . '/../helpers/common/crsftoken.php'; // Provides functionality to retrieve the current CSRF token
require_once __DIR__ . '/../helpers/common/debbuger.php'; // Provides debugging helper functions like dump() and dd()