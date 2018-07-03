<?php

require_once 'class/SmartArray.php';

$a = new SmartArray();
$a->put('test', 1);
echo $a->get('test');
