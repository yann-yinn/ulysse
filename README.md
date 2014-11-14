PHP fucking deadly simple light procedural framework.

REQUIREMENTS
-------------

* php >= 5.4
* Apache

INSTALLATION
-------------

Go to "your_installation_path/www/index_dev.php"
Rename "example.config" directory to "config".
Edit routes.php file to start

ROUTES.PHP
--------------

Routes.php file map a framework path to a php callable.
A route may return a string or a closure :
In config/routes.php :

```php
$routes['homepage'] = [
    'path' => '',
    'return' => 'hello i am the homepage',
];
// to render template page.php inside a layout.php template
$routes['homepage'] = [
    'path'   => '',
    'template' => 'layout.php',
    'return' =>  template('homepage.php')
];
$routes['hello'] => [
    'path' => 'hello',
    'return' => function() {
      $controller = new \myVendor\myModule\myController();
      $controller->hello();
    }
  ]
return $routes;
```

TEMPLATE
---------------

Use a template to render a page with variables :
```php
template('path/to/template.php', ['variable' => 'value'])
```

Printing in a secured way a variable in a template :
Never use print or echo to avoid code injection.
```php
<?php e($variable) ?>
```