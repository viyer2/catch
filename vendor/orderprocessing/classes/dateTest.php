<?php

include 'DateFormatter.php' ;

$dt = new DateFormatter(); 

$dt->setDateString('Fri, 08 Mar 2019 12:13:29 +0000'); 

print $dt->returnISO(); 

?>
