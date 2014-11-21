<?php
namespace atoms;

function setHttpLocationHeader($sanitized_url) {
  header("Location: $sanitized_url");
}

/**
 * Sanitize a variable, to display it, encoding html.
 * @param string $value
 * @return string html encoded value
 */
function sanitizeString($value)
{
  return htmlspecialchars($value, ENT_QUOTES, 'utf-8');
}
