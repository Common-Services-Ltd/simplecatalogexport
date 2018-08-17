<?php
/**
 * @author    debuss-a <alexandre@common-services.com>
 * @copyright Copyright (c) 2018 Common-Services
 * @license   CC BY-SA 4.0
 */

/**
 * @param string $class_name
 */
function simpleCatalogExportAutoLoader($class_name)
{
    $folders_path = array(
        _PS_MODULE_DIR_.'simplecatalogexport/classes/'
    );

    foreach ($folders_path as $dir) {
        $recursive_iterator_iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($recursive_iterator_iterator as $file) {
            /** @var SplFileObject $file */
            if (Tools::strtolower($file->getBasename('.php')) == Tools::strtolower($class_name)) {
                require_once $file->getRealPath();
                break;
            }
        }
    }
}

spl_autoload_register('simpleCatalogExportAutoLoader');
