<?php

require_once('config.php');

if (!isset($title))
    $title = isset($_SERVER["REDIRECT_STATUS"]) ? $_SERVER["REDIRECT_STATUS"] : "Ukjent feil";

switch($title){
    case 400:
        $description = "Forespørselen kunne ikke proseseres";
        break;

    case 401:
        $description = "Du har ikke tilgang til dette innholdet";
        break;

    case 403:
        $description = "Du har ikke tilstrekkelige tillatelser til å vise dette innholdet";
        break;

    case 404:
        $description = "Siden ble ikke funnet";
        break;

    case 500:
        $description = "Intern tjenerfeil";
        break;

    case 502:
        $description = "Feil på mellomledd";
        break;

    case 504:
        $description = "Tidsavbrudd på mellomledd";
        break;
}

echo $twig->render('error.twig', array('title' => $title, 'description' => $description));

