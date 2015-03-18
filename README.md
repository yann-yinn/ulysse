ULYSSE
------------

PHP View / Controller light micro-framework.
This is dedicated to small projects who needs a few routes to
respond very fast.

CREATE ROUTES
--------------

"config/_routes.php" files maps an url to a controller.
In Ulysse, a controller always return an *array* .
This array will be used to replace variables in a html template
or to be converted to a json.
In application/config/_routes.php :

```php
// to render variables in a template page.php
// GET is the http method, you may use any valid http methods instead.
$config['routes']['hello']['GET'] = [
    'path'   => 'hello',
    'format' => 'html'
    'template' => 'path/to/page.php', // a template containing a $content variable.
    'controller' =>  function() {
       return ['content' => 'hello world']
    }
];

// output as json, where name is variable sent to the controller
$config['routes']['hello/name']['GET'] = [
    'arguments'   => ['name'],
    'format' => 'json',
    'controller' =>  function($name) {
       return ['content' => 'hello ' . sanitizeValue($name)]
    }
];
```

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

FEATURES OVERVIEW
-----------------

* Http routing (with request method detection support)
* Translations
* Config files and config overrides by environment
* Code organized by modules
* Events / Listeners
* Templating


TEMPLATES EXAMPLE
-----------------

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

