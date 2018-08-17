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
 * Class SimpleCatalogExportTab
 */
class SimpleCatalogExportTab extends Tab
{

    /**
     * Create the tab and return its ID, or false on error.
     *
     * @param string $tab_name
     * @return bool|int
     */
    public static function create($tab_name)
    {
        $id_tab = Tab::getIdFromClassName(SimpleCatalogExport::TAB_CLASS_NAME);
        if ($id_tab) {
            return $id_tab;
        }

        try {
            $tab = new self();
            $tab_names = array();

            foreach (Language::getLanguages() as $language) {
                $tab_names[$language['id_lang']] = $tab_name;
            }

            $tab->name = $tab_names;
            $tab->class_name = SimpleCatalogExport::TAB_CLASS_NAME;
            $tab->module = 'simplecatalogexport';
            $tab->id_parent = Tab::getIdFromClassName('AdminCatalog');
            $tab->add();
        } catch (Exception $exception) {
            Tools::dieOrLog($exception->getMessage(), false);
            return false;
        }

        return $tab->id;
    }

    /**
     * @return bool
     */
    public static function remove()
    {
        $id_tab = Tab::getIdFromClassName(SimpleCatalogExport::TAB_CLASS_NAME);
        if (!$id_tab) {
            return true;
        }

        try {
            $tab = new self($id_tab);
            if (Validate::isLoadedObject($tab)) {
                return $tab->delete();
            }
        } catch (Exception $exception) {
            Tools::dieOrLog($exception->getMessage(), false);
        }

        return false;
    }

    /**
     * Create only 1 SimpleCatalogExport tab.
     *
     * @param bool $autodate
     * @param bool $null_values
     * @return bool|int
     */
    public function add($autodate = true, $null_values = false)
    {
        if (Tab::getIdFromClassName(SimpleCatalogExport::TAB_CLASS_NAME)) {
            return true;
        }

        return parent::add($autodate, $null_values);
    }
}
