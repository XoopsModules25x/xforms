<?php

$moduleDirName      = basename(dirname(dirname(__DIR__)));
$moduleDirNameUpper = mb_strtoupper($moduleDirName);

//Latest Version Check
define('CO_' . $moduleDirNameUpper . '_' . 'NEW_VERSION', 'New Version: ');
//Repository not found
define('CO_' . $moduleDirNameUpper . '_' . 'REPO_NOT_FOUND', 'Repository Not Found: ');
//Release not found
define('CO_' . $moduleDirNameUpper . '_' . 'NO_REL_FOUND', 'Released Version Not Found: ');
