<?php
/**
 * @file
 * Template formatters
 */

$config['templateFormatters']['link'] = function($text, $path, $queryString = '') {
  $href = getRouteUrl($path, $queryString);
  $classes = pathIsActive($path) ? 'active' : '';
  return sprintf('<a class="%s" href="%s">%s</a>', escape($classes), escape($href), escape($text));
};

$config['templateFormatters']['euros'] = function ($number, $sigle = ' €', $decimals = 2, $decPoint = ',', $thousandsSep = ' ') {
  $price = number_format($number, $decimals, $decPoint, $thousandsSep);
  $price = $price . $sigle;
  return escape($price);
};

$config['templateFormatters']['dateFull'] = function($timestamp) {
  return escape(gmdate("d-m-Y H:i:s", $timestamp));
};
