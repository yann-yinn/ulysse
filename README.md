ULYSSE
------------

PHP Procedural Content Managment Framework, with 0% OOP shit.
Because unless you're Platon, you know that real word is not about abstract objects.

FEATURES
------------

* routeur
* Config files
* Events listeners
* String translations
* MVC

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

MAIN FEATURES
--------------

* 0% OOP shit
* Templates by theme and templates overrides
* String translations
* Settings in files
* Organize code in modules
* autoload for vendors PHP libraries
* content staging : online, draft or trash
* Edit links for content are displayed on the front-end content it-self
* fucking fast


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