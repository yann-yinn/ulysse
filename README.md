ULYSSE
------------

PHP Procedural MVC Framework.

REQUIREMENTS
-------------

* php >= 5.4
* Apache

INSTALLATION
-------------

* clone ulysse directory.
* copy "example.app" to "app"
* go to "localhost/ulysse/app/www/ (or create a virtualhost pointing to this directory)

Edit app/config/_routes.php file to start create new pages.

CREATE ROUTES
--------------

"config/_routes.php" files maps an url to a controller.
In Ulysse, a controller always return an *array* .
This array will be used to replace variables in a html template
or to be converted to a json.
In application/config/_routes.php :

```php
// to render a template page.php inside a layout.php template
$config['routes']['hello'] = [
    'path'   => 'hello',
    'template' => 'page.php', // a template containing a $content variable.
    'controller' =>  function() {
       return ['content' => 'hello world']
    }
];
// output as json
$config['routes']['homepage'] = [
    'path'   => 'hello',
    'format' => 'json',
    'controller' =>  function() {
       return ['content' => 'hello world']
    }
];
// Templates imbrication :
$config['routes']['hello'] = [
    'path'   => 'hello',
    'template' => 'page.php', // a template containing a $content variable.
    'controller' =>  function() {
       return ['content' => template('hello.php', ['name' => 'John'])];
    }
];
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
<?php formatAs('euros', $prix) ?>
```

