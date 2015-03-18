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
Example routes, where GET is the http request method.
Any http method may be used instead.

/**
 * Override default homepage
 */
$config['routes']['']['GET'] = [
  'template'    => 'exampleModule/templates/page.php',
  'format'      => 'html',
  'controller'  =>  function() {
      return ['content' => "Ulysse works."];
    }
];

/**
 * Example route.
 *
 * visit localhost/{yoursite}/www/index.php/hello to view it.
 */
$config['routes']['hello']['GET'] = [
  'template'   => 'exampleModule/templates/page.php',
  'format'     => 'html',
  'controller' => function() {
      return ['content' => "Hello world"];
    },
];

/**
 * Example route.
 *
 * visit localhost/{yoursite}/www/index.php/hello/{yourname} to view it.
 */
$config['routes']['hello/name']['GET'] = [
  'arguments' => ['name'],
  'format'     => 'json',
  'controller' => function($name) {
      return ['message' => "Hello ! " . escape($name)];
    },
];

/**
 * Example route.
 *
 * visit localhost/{yoursite}/www/index.php/hello/{name}/{surname} to view it.
 */
$config['routes']['hello/name/surname']['GET'] = [
  'arguments' => ['name', 'surname'],
  'format'     => 'json',
  'controller' =>  'controllers::hello'
];

class controllers {

  static function hello($name, $surname) {
    return ['name' => escape($name), 'surname' => escape($surname)];
  }

}

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

