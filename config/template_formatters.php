<?php
/**
 * Template formatters
 * @param int $number
 * @param string $sigle
 * @param int $decimals
 * @param string $decPoint
 * @param string $thousandsSep
 * @return string
 */

function euros($number, $sigle = ' €', $decimals = 2, $decPoint = ',', $thousandsSep = ' ') // format a number as a price, with optionnal € sigle.
{
  $price = number_format($number, $decimals, $decPoint, $thousandsSep);
  $price = $price . $sigle;
  return $price;
}
