<?php

require_once('includes/autoload.php');
$savings = new Savings();
$runSavingsCron = $savings->cronSavings();
echo $runSavingsCron;