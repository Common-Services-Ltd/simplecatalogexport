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
 * Class AdminCatalogSimpleCatalogExport
 */
class AdminCatalogSimpleCatalogExportController extends ModuleAdminController
{

    /**
     * AdminCatalogSimpleCatalogExportController constructor.
     */
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'simplecatalogexport';
        $this->className = __CLASS__;
        $this->context = Context::getContext();
        $this->display = 'view';
        $this->allow_export = true;

        try {
            parent::__construct();
        } catch (PrestaShopException $prestashop_exception) {
            Tools::dieOrLog($prestashop_exception->getMessage(), false);
        }
    }

    /**
     * @return bool|void
     * @throws LogicException
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     * @throws RuntimeException
     */
    public function postProcess()
    {
        $export_success = null;
        $download_success = null;

        if (Tools::isSubmit('submitExportCatalog')) {
            $since_last_export = Tools::getValue('since_last_export');
            $since_date = Tools::getValue('since_date');

            if ($since_last_export) {
                $last_export = Tools::unSerialize(Configuration::get('SCE_LAST_EXPORT'));
                $since_date = isset($last_export['date']) && Validate::isDate($last_export['date']) ?
                    $last_export['date'] : date('Y-m-d', strtotime('10 YEARS AGO'));
            }

            $export_success = $this->exportCatalog($since_date);
        }

        if (Tools::isSubmit('downloadcatalog')) {
            $download_success = $this->downloadCatalog();
        }

        $this->context->smarty->assign(array(
            'export_success' => $export_success,
            'download_success' => $download_success
        ));
    }

    /**
     * @param string $since_date
     * @return int
     * @throws LogicException
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     * @throws RuntimeException
     */
    private function exportCatalog($since_date)
    {
        // Base headers
        // Features and attributes will be added on the fly
        $headers = array(
            'reference',
            'ean13',
            'upc',
            'name',
            'description_short',
            'description',
            'manufacturer_name',
            'supplier_name',
            'supplier_reference',
            'quantity',
            'price',
            'tax_rate',
            'ecotax',
            'width',
            'height',
            'depth',
            'weight'
        );
        $catalog = array();
        $history = array();

        $categories = Tools::unSerialize(Configuration::get('SCE_CATEGORIES'));
        $id_products = array_column(Db::getInstance()->executeS(
            'SELECT p.`id_product`
            FROM `'._DB_PREFIX_.'product` p
            LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.id_product = p.id_product)
            WHERE cp.`id_category` IN ('.implode(', ', array_map('intval', $categories)).')
            AND p.`date_add` >= "'.pSQL($since_date).'"'
        ), 'id_product');

        foreach ($id_products as $id_product) {
            if (isset($history[$id_product])) {
                continue;
            }
            $history[$id_product] = true;

            $product = new Product($id_product, true, $this->context->language->id);
            if (!Validate::isLoadedObject($product)) {
                // Log error
                continue;
            }

            $to_export = array();
            foreach ($headers as $header) {
                $to_export[$header] = property_exists($product, $header) ? $product->{$header} : null;
            }

            // Features
            foreach ($product->getFeatures() as $product_feature) {
                $feature = new Feature($product_feature['id_feature'], $this->context->language->id);
                $feature_value = new FeatureValue($product_feature['id_feature_value'], $this->context->language->id);

                $header = 'feat_'.Tools::strtolower($feature->name);
                $headers[] = $header;
                $to_export[$header] = $feature_value->value;
            }

            // Attributes
            if ($product->hasAttributes()) {
                $attributes = $product->getAttributeCombinations($this->context->language->id);
                $product_attributes = array();

                foreach ($attributes as $attribute) {
                    if (!isset($product_attributes[$attribute['id_product_attribute']])) {
                        $product_attributes[$attribute['id_product_attribute']] = array();
                    }

                    $product_attributes[$attribute['id_product_attribute']][] = $attribute;
                }

                foreach ($product_attributes as $attribute) {
                    $new_to_export = $to_export;

                    $new_to_export['reference'] = $attribute[0]['reference'];
                    $new_to_export['ean13'] = $attribute[0]['ean13'];
                    $new_to_export['upc'] = $attribute[0]['upc'];
                    $new_to_export['quantity'] = $attribute[0]['quantity'];
                    $new_to_export['ecotax'] = $attribute[0]['ecotax'];
                    $new_to_export['price'] = $product->getPriceWithoutReduct(
                        false,
                        $attribute[0]['id_product_attribute']
                    );

                    foreach ($attribute as $attr) {
                        $header = 'attr_'.Tools::strtolower($attr['group_name']);
                        $new_to_export[$header] = $attr['attribute_name'];

                        $headers[] = $header;
                    }

                    $catalog[] = $new_to_export;
                }
            } else {
                $catalog[] = $to_export;
            }
        }

        $headers = array_unique($headers);

        try {
            $file = new SplFileObject(_PS_MODULE_DIR_.'simplecatalogexport/export/catalog.csv', 'w+');
        } catch (Exception $exception) {
            Tools::dieOrLog($exception->getMessage(), false);
            return -1;
        }

        if ($file->isWritable()) {
            if (!chmod($file->getRealPath(), 0644)) {
                return -2;
            }
        }

        $file->fputcsv($headers);
        $count = 0;

        foreach ($catalog as $line) {
            $csv = array();

            foreach ($headers as $header) {
                $csv[] = isset($line[$header]) ? $line[$header] : null;
            }

            $file->fputcsv($csv);
            $count += 1;
        }

        Configuration::updateValue('SCE_LAST_EXPORT', serialize(array(
            'date' => date('Y-m-d'),
            'nb_products' => $count
        )));

        return count($catalog);
    }

    /**
     * @param bool $isNewTheme
     */
    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        $this->addJS(array(
            'https://cdnjs.cloudflare.com/ajax/libs/riot/3.11.1/riot+compiler.min.js'
        ));

        $this->addJqueryUI('ui.datepicker');
    }

    /**
     * @return string
     * @throws LogicException
     * @throws RuntimeException
     */
    public function renderView()
    {
        $template = $this->createTemplate('catalog.tpl');
        $prestui = $this->context->smarty->createTemplate(
            _PS_MODULE_DIR_.$this->module->name.'/views/templates/admin/prestui/ps-tags.tpl',
            $this->context->smarty
        );

        $this->context->smarty->assign(array(
            'module_configuration_link' => $this->context->link->getAdminLink('AdminModules', true).
                '&module_name=simplecatalogexport&configure=simplecatalogexport',
            'download_catalog_link' => $this->context->link->getAdminLink('AdminCatalogSimpleCatalogExport', true).
                '&downloadcatalog',
            'last_export' => Tools::unSerialize(Configuration::get('SCE_LAST_EXPORT'))
        ));

        return $template->fetch().$prestui->fetch();
    }

    /**
     * @throws LogicException
     * @throws RuntimeException
     */
    private function downloadCatalog()
    {
        $path = _PS_MODULE_DIR_.'/simplecatalogexport/export/catalog.csv';

        if (!file_exists($path)) {
            return;
        }

        $file = new SplFileObject($path, 'rb');

        header('Content-type: text/csv');
        header('Content-Type: application/force-download; charset=UTF-8');
        header('Cache-Control: no-store, no-cache');
        header('Content-disposition: attachment; filename="catalog_'.date('Y-m-d_His').'.csv"');
        header('Content-Length: '.$file->getSize());

        $file->fpassthru();

        exit;
    }
}
