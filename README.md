ULYSSE
------------

PHP Procedural MVC Framework.

FEATURES
------------

* MVC : routes -> controllers -> template
* template system, with overridable templates and switchable themes.
* Config files
* organize code by features in module
* Create or listen events.
* String translations
* PSR0 autoloader

REQUIREMENTS
-------------

* php >= 5.4
* Apache

INSTALLATION
-------------

* clone ulysse directory.
* copy example.application to {yourapp}
* go to "localhost/ulysse/{yourapp}/www/default/

Edit yoursite/config/_routes.php file to start create pages on your site.

CREATE ROUTES
--------------

"config/_routes.php" files maps an url to a php controller.
It uses php closures or simple strings.
A route may return a string or a closure :
In application/config/_routes.php :

```php
$config['routes']['homepage'] = [
    'path' => '',
    'callable' => 'hello i am the homepage',
];
// to render a template page.php inside a layout.php template
$config['routes']['homepage'] = [
    'path'   => '',
    'template' => 'layout.php',
    'callable' =>  function() {template('homepage.php');}
];
// you may uses classes :
$config['routes']['hello'] => [
    'path' => 'hello',
    'callable' => function() {
      $controller = new \myVendor\myModule\myController();
      $controller->hello();
    }
  ]
```

TEMPLATES
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

TEMPLATES OVERRIDABLE AND THEMES
---------------------------------

By default, templates will use simply path passed as an argument :
For "path/to/template.php", "'path/to/template.php', will be used to render the template.
But Ulysse will first look for an existing "application/themes/mytheme/path/to/template.php"
and will use it if found.

"mytheme" is the default enabled theme, unless you specify theme to use in the template function
or in your routes declaration.
