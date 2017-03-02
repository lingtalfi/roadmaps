<?php
//--------------------------------------------
// CRUD GENERATORS
//--------------------------------------------
use Crud\ResetOption\CrudFilesResetOption;
use Crud\ResetOption\GeneratorsPreferencesResetOption;
use Crud\ResetOption\LeftMenuPreferencesResetOption;
use Crud\Util\CrudFilesGenerator;
use Crud\Util\CrudFilesPreferencesGenerator;
use Crud\Util\LeftMenuPreferencesGenerator;

// define('APP_ROOT_DIR', __DIR__ . "/../public");
$options = [];
$options[] = new LeftMenuPreferencesResetOption('crud_leftmenu', 'empty the left menu preferences');
$options[] = new CrudFilesResetOption('crud_files', 'remove the crud files');
$options[] = new GeneratorsPreferencesResetOption('crud_files_prefs', 'empty the crud files preferences');
foreach ($options as $o) {
    $o->reset();
}

CrudFilesPreferencesGenerator::generate();
CrudFilesGenerator::generateCrudFormsFromPreferences();
CrudFilesGenerator::generateCrudListsFromPreferences();
LeftMenuPreferencesGenerator::create()->generate();
