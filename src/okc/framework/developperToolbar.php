<?php

print "
<style>
#okc-developper-toolbar {
  margin-top : 50px;
  padding : 20px;
  border-top : solid silver 1px;
}

#okc-developper-toolbar h2 {

}

</style>

";



print '<section id = "okc-developper-toolbar">';

print '<h2> Stats </h2>';
$time_end = microtime(TRUE);
$time = $time_end - getContextVariable('time_start');
print '<ul>';
print '<li>';
print 'Time usage : ' . round($time * 1000) . 'ms';
print '</li>';
$memory_usage = memory_get_usage();
print '<li> Memory usage : ' . round($memory_usage/1048576,2) . 'Mo </li>';
print '</ul>';
print '</div>';

print '<h2>Context </h2>';

echo '<pre>';
print_r(getSiteContext());
echo '</pre>';


print '<h2> Logs </h2>';
print '<ul>';

$logs = getAllLogs();
if (!empty($logs)) {
  foreach ($logs as $log) {
    print '<li>';
    print $log['detail'];
    print '</li>';
  }
}
print '</ul>';


print '</section>';

