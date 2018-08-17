<?php
/**
 * @author    debuss-a <alexandre@common-services.com>
 * @copyright Copyright (c) 2018 Common-Services
 * @license   CC BY-SA 4.0
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once dirname(__FILE__).'/bootstrap/autoload.php';

/**
 * Class SimpleCatalogExport
 */
class SimpleCatalogExport extends Module
{

    const TAB_CLASS_NAME = 'AdminCatalogSimpleCatalogExport';

    /**
     * SimpleCatalogExport constructor.
     */
    public function __construct()
    {
        $this->name = 'simplecatalogexport';
        $this->tab = 'content_management';
        $this->version = '1.0.00';
        $this->author = 'debuss-a';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Simple Catalog Export');
        $this->description = $this->l('Export your catalog in CSV format.');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    /**
     * @return bool
     */
    public function install()
    {
        $categories = SimpleCatalogExportTools::arrayColumn(
            Category::getSimpleCategories($this->context->language->id),
            'id_category'
        );

        return parent::install() &&
            SimpleCatalogExportTab::create($this->displayName) &&
            Configuration::updateValue('SCE_CATEGORIES', serialize($categories)) &&
            Configuration::updateValue('SCE_LAST_EXPORT', serialize(array(
                'date' => 'N/A',
                'nb_products' => 0
            )));
    }

    /**
     * @return bool
     */
    public function uninstall()
    {
        return parent::uninstall() &&
            SimpleCatalogExportTab::remove() &&
            Configuration::deleteByName('SCE_CATEGORIES');
    }

    /**
     * @return string
     * @throws PrestaShopException
     */
    public function getContent()
    {
        $post_process = null;

        if (Tools::isSubmit('submit'.$this->name)) {
            $post_process = $this->postProcess();
        }

        $this->context->controller->addJS(array(
            'https://cdnjs.cloudflare.com/ajax/libs/riot/3.11.1/riot+compiler.min.js'
        ));

        $this->context->controller->addCSS($this->_path.'views/css/configuration.css');

        $helper = new HelperTreeCategories('categories-treeview');
        $helper
            ->setRootCategory(Category::getRootCategory()->id_category)
            ->setSelectedCategories(Tools::unSerialize(Configuration::get('SCE_CATEGORIES')))
            ->setUseCheckBox(true)
            ->setUseSearch(true)
            ->setInputName('categories');

        $this->context->smarty->assign(array(
            'module_dir' => $this->_path,
            'post_process' => $post_process,
            'categories_tree' => $helper->render(),
            'catalog_export_link' => $this->context->link->getAdminLink('AdminCatalogSimpleCatalogExport', true),
            'iso_lang' => Tools::strtolower(Language::getIsoById($this->context->language->id))
        ));

        return $this->display(__FILE__, 'views/templates/admin/configuration/configuration.tpl').
            $this->display(__FILE__, 'views/templates/admin/prestui/ps-tags.tpl');
    }

    /**
     * @return bool
     */
    public function postProcess()
    {
        return Configuration::updateValue(
            'SCE_CATEGORIES',
            serialize(Tools::getValue('categories', array()))
        );
    }
}
