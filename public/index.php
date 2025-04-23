<?php
require_once "../vendor/autoload.php";
require_once "../app/models/Model.php";
require_once "../app/models/Translations.php";
require_once "../app/controllers/TranslationsController.php";

//env variables
$env = parse_ini_file('./.env');
define('DBNAME', $env['DBNAME']);
define('DBHOST', $env['DBHOST']);
define('DBUSER', $env['DBUSER']);

$_ENV['OPENAI_API_KEY'] = $env['OPENAI_API_KEY'];

use controllers\TranslationsController;

$uri = strtok($_SERVER["REQUEST_URI"], '?');

//get uri pieces
$uriArray = explode("/", $uri);

//get all or a single translation
if ($uriArray[1] === 'api' && $uriArray[2] === 'translations' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    //only id
    $id = isset($uriArray[3]) ? intval($uriArray[3]) : null;
    $translationsController = new TranslationsController();

    if ($id) {
        $translationsController->getTranslationsByID($id);
    } else {
        $translationsController->getAllTranslations();
    }
}

if ($uriArray[1] === 'api' && $uriArray[2] === 'generate-translation' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $translationsController = new TranslationsController();
    $translationsController->generateTranslation();
}

//save translation
if ($uriArray[1] === 'api' && $uriArray[2] === 'translations' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $translationsController = new TranslationsController();
    $translationsController->saveTranslations();
}

//delete translation
if ($uriArray[1] === 'api' && $uriArray[2] === 'translations' && $_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $translationsController = new TranslationsController();
    $id = isset($uriArray[3]) ? intval($uriArray[3]) : null;
    $translationsController->deleteTranslations($id);
}

//views
if ($uriArray[1] === 'translations' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $translationsController = new TranslationsController();
    $translationsController->translationsView();
}

if ($uriArray[1] === 'delete-translations' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $translationsController = new TranslationsController();
    $translationsController->translationsDeleteView();
}

if ($uriArray[1] === '' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $translationsController = new TranslationsController();
    $translationsController->translationsHomepageView();
}

exit();
?>