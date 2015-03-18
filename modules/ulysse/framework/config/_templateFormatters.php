<?php
/**
 * @file
 * Template formatters
 */

$config['templateFormatters']['link'] = function($text, $path, $queryString = '') {
  $href = buildUrlFromPath($path, $queryString);
  $classes = pathIsActive($path) ? 'active' : '';
  return sprintf('<a class="%s" href="%s">%s</a>', sanitizeValue($classes), sanitizeValue($href), sanitizeValue($text));
};

$config['templateFormatters']['euros'] = function ($number, $sigle = ' €', $decimals = 2, $decPoint = ',', $thousandsSep = ' ') {
  $price = number_format($number, $decimals, $decPoint, $thousandsSep);
  $price = $price . $sigle;
  return sanitizeValue($price);
};

$config['templateFormatters']['dateFull'] = function($timestamp) {
  return sanitizeValue(gmdate("d-m-Y H:i:s", $timestamp));
};
