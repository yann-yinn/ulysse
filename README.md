Deadly fucking simple procedural Content Managment Framework, containing 0% OOP shit.
Because unless you're Platoon, you know that real word is not about abstract objects.
Because printing a fucking "hello world" should not require 15Mo of php memory. Becauce nice ideas are the worst way to get Things Done.

DESCRIPTION
------------

Ulysse is a PHP procedural framework. It also provides a basic user friendly content managment module.
There is no learning curve, because Ulysse is simply PHP, using good pratices without OOP java-like shit.

REQUIREMENTS
-------------

* php >= 5.4
* Apache

INSTALLATION
-------------

* Copy "www/example.index.php" to "www/index.php" file (you may name it as you want or create several entry points, like index_fr.php)
* Copy "example.config" directory to "config"
* Rename "example.htaccess" to ".htaccess"

Edit pages.php file to start create pages on your site.

FEATURES
--------------

* templates by theme and templates overrides
* settings

CREATE PAGES
--------------

pages.php file map a framework path to a php callable.
A page may return a string or a closure :
In config/pages.php :

```php
$pages['homepage'] = [
    'path' => '',
    'content' => 'hello i am the homepage',
];
// to render a template page.php inside a layout.php template
$pages['homepage'] = [
    'path'   => '',
    'template' => 'layout.php',
    'content' =>  function() {template('homepage.php');}
];
// MVC style :
$pages['hello'] => [
    'path' => 'hello',
    'content' => function() {
      $controller = new \myVendor\myModule\myController();
      $controller->hello();
    }
  ]
return $pages;
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

Use a function to format a value
```php
<?php e($prix, 'euros') ?>
```