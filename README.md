ULYSSE
------------

PHP Procedural View / Controller micro-framework.

FEATURES
-------------

* Http routing (with request method detection support)
* Translations
* Config files by environment
* Modules
* Events / Listeners
* Templating

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
// to render variables in a template page.php
$config['routes']['hello.get.html'] = [
    'path'   => 'hello',
    'http method' => 'GET', // allowed http methods for
    'format' => 'html'
    'template' => 'path/to/page.php', // a template containing a $content variable.
    'controller' =>  function() {
       return ['content' => 'hello world']
    }
];
// output as json
$config['routes']['hello.get.json'] = [
    'path'   => 'hello',
    'http method' => 'GET',
    'format' => 'json',
    'controller' =>  function() {
       return ['content' => 'hello world']
    }
];
```

TEMPLATES
---------------

Ulysse contains some basic helpers to display html templates.

Use a template to render a page with variables :
```php
template('path/to/template.php', ['variable' => 'value'])
```

Printing in a secured way a variable in a template :
Never use print or echo to avoid code injection.
```php
<?php e($variable) ?>
```

Use a function to format a value. see _formatters.php files.
```php
<?php formatAs('euros', $prix) ?>
```

Create a link to a route; with an active class
```php
  <a class="<?php if (pathIsActive('')) e('active') ?>" href="<?php e(getRouteUrl('')) ?>">Homepage </a>
```

