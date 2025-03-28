# Router

## Project Description
Router is a lightweight PHP library for handling routing in web applications. It allows defining and managing URL routes, handling parameters, and assigning appropriate controllers to requests.

## Requirements
- PHP 8.1+
- Composer
- PHPUnit 10.5

## Installation

To install the project, clone the repository and install dependencies:

```sh
git clone https://github.com/Tomazo16/router.git
cd router
composer install
```

If you want to use this repository as a dependency in your project, add it manually to `composer.json`:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/Tomazo16/router.git"
        }
    ],
    "require": {
        "tomazo16/router": "dev-main"
    }
}
```

Then run:

```sh
composer update
```

## Usage

Example of basic configuration and usage:

1. Create configuraton file 'router' in folder /config
```php
return [
    'routeLoader' => [
        'class' => 'Tomazo\Router\RouteLoader\ControllerRouteLoader',
        'attr' => [
            'namespace' => 'App\Controller',
            'controllersPath' => __DIR__ . '/../../src/Controller'
        ]
    ] ,
   'routeResolver' => [
        'class' => 'Tomazo\Router\RouteResolver\SimpleRouteResolver',
        'attr' => []
    ] ,
];
```

You can create your own RouteLoader and RouteResolver.

2. Initialize Router

```php
require 'vendor/autoload.php';

use Tomazo\Router\Factory\RouterFactory;
use Tomazo\Router\Router;

//load router configutation
$routerConfig =  require __DIR__ . '/config/router.php';

// Initialize the Router
$router = RouterFactory::createRouter($routerConfig);

// Get all resolved routes
$routes = $router->getRoutes();

// Match a path to an action
try {
    $result = $router->getActionMethod('/home');
    echo $result;
} catch (\InvalidArgumentException $e) {
    echo $e->getMessage();
}

// Retrieve all route paths
$routePaths = $router->getRoutePaths();
print_r($routePaths);

// Generate a URL for a named route
try {
    $url = $router->generateUrl('user.profile', ['id' => 42]);
    echo $url;
} catch (\InvalidArgumentException $e) {
    echo $e->getMessage();
}
```

## Directory Structure

```
/
├── bin/        # Binary files
├── config/     # Configuration files
├── src/        # Source code
├── tests/      # Unit tests
├── .gitignore  # Git ignore file
├── composer.json # Composer configuration file
├── phpunit.xml # PHPUnit configuration
└── README.md   # Project documentation
```

## Testing

The project includes unit tests that can be run using PHPUnit:

```sh
vendor/bin/phpunit
```

## Contribution

If you want to contribute to this project:

1. Clone the repository
   ```sh
   git clone https://github.com/Tomazo16/router.git
   ```
2. Create a new branch
   ```sh
   git checkout -b feature-function-name
   ```
3. Make changes and commit them
   ```sh
   git commit -m "Added a new feature"
   ```
4. Push changes and open a pull request
   ```sh
   git push origin feature-function-name
   ```

## License

This project is available under the MIT license. See the `LICENSE` file for details.

---
Thank you for using our router! 🚀

