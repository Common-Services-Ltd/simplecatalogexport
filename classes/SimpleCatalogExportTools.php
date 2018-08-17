<?php
/**
 * @author    debuss-a <alexandre@common-services.com>
 * @copyright Copyright (c) 2018 Common-Services
 * @license   CC BY-SA 4.0
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Class SimpleCatalogExportTools
 */
class SimpleCatalogExportTools
{

    /**
     * Implementation of array_column, only available since PHP 5.5.0.
     *
     * @see http://php.net/manual/en/function.array-column.php
     * @param array $array
     * @param mixed $column_name
     * @return array
     */
    public static function arrayColumn($array, $column_name)
    {
        if (function_exists('array_column')) {
            return array_column($array, $column_name);
        }

        return array_map(
            array(__CLASS__, 'arrayColumnCallback'),
            $array,
            array_fill(0, count($array), $column_name)
        );
    }

    /**
     * @param array $element
     * @param mixed $column_name
     * @return mixed
     */
    private static function arrayColumnCallback($element, $column_name)
    {
        return $element[$column_name];
    }
}
