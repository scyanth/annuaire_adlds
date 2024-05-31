<?php

// gestion des erreurs
//error_reporting(E_ALL);
//ini_set("display_errors",1);

// chargement des librairies
require "vendor/autoload.php";
use vlucas\phpdotenv;

// chargement des variables d'environnement
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();