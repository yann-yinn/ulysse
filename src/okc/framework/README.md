
ROUTES ARRAY EXAMPLE
---------------------

To map a route to a php controller :

In config/routes.php :
```php
$routes['homepage'] = [
    'path' => '',
    'return' => getStaticContent('homepage/homepage.html'),
];
// how to merge setting from another file :
$routes += include '../src/okc/simulateur/config/routes.php';
```

VIEWS
---------------

Returning a view from a controller with variables:
```php
template('path/to/template.php', ['variable' => 'value'])
```

Printing in a secure way a variable in a template :
```php
<?php e($variable) ?>
```

CONTROLLER EXAMPLE
-------------------

```php
namespace yourvendor\yourpackage\controllers;

use \okc\framework\view;

class worldController {

  public function hello($name) {
    return new view('templates/page.php', [
      'page_title' => "Hello",
      'page_content' =>  'Hello' . htmlentities($name, ENT_QUOTES),
    ]);
  }
```

FORM CONTROLLER EXAMPLE
-----------------------

```php
class myForm extends form_controller {

  public $fields = [
    'caHt' => 0,
    'salaire' => 0,
    'frais' => 0,
  ];

  function validation() {
    foreach ($this->fields as $name => $datas) {
      if (!is_numeric(str_replace('e', 'FuckYouPhp', $this->fields[$name]))) {
        $this->errors[$name][] = "La valeur doit être numérique";
      }
    }
  }

}
```

HTACCESS EXAMPLE
----------------

To remove "index.php" from urls :

Options +FollowSymLinks
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^ index.php [L]