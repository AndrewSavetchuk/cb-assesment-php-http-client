<?php

$debug = 1;

ini_set('display_errors', $debug ?? 0);
ini_set('display_startup_errors', $debug ?? 0);
error_reporting($debug ? E_ERROR : 0);
