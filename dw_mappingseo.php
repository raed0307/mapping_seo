<?php

/**
 * 2007-2021 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2021 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

use Symfony\Component\Validator\Constraints\Length;

if (!defined('_PS_VERSION_')) {
    exit;
}
/**
 * Add meta titles for products ,meta description and alt images
 * 
 */
class Dw_mappingseo extends Module
{

    public function __construct()
    {
        $this->name = 'dw_mappingseo';
        $this->tab = 'seo';
        $this->version = '1.0.2';
        $this->author = 'Raed Dakhli';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Mapping SEO');
        $this->description = $this->l('mapping balise meta title et balise alt image');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        Configuration::updateValue('simpleProductMetaTitlePattern', false);
        Configuration::updateValue('ProductWithCombinationsMetaTitlepattern', false);
        Configuration::updateValue('ProductMetaDescriptionPattern', false);
        Configuration::updateValue('AltImagePatern', false);

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('backOfficeHeader');
    }

    public function uninstall()
    {
        Configuration::deleteByName('simpleProductMetaTitlePattern');
        Configuration::deleteByName('ProductWithCombinationsMetaTitlepattern');
        Configuration::deleteByName('ProductMetaDescriptionPattern');
        Configuration::deleteByName('AltImagePatern');

        return parent::uninstall();
    }
    /**
     * Function to get all products in store
     * params : all the products in the store
     * return all products in the store with only the var's we need to set meta's
     * var's : id, name, price, default category, categories, images, combination, product description 
     */
    public function getProducts($products)
    {
        $id_lang = (int)Context::getContext()->language->id;
        $allProducts = array();

        foreach ($products as $product) {
            // load product object
            $idProduct = $product['id_product'];
            $productComb = new Product($idProduct, $this->context->language->id);
            $idDefaultCategory = $product['id_category_default'];

            // get the product allProducts data
            // create array allProducts with key = idProduct
            $allProducts[$idProduct] = [
                "idProduct" => $idProduct,
                "name" => $product['name'],
                "price" => $product['price'],
                "reference" => $product['reference'],
                "idDefaultCategory" => $product['id_category_default'],
                "defaultCategory" => Category::getCategoryInformations(array($idDefaultCategory)),
                "categories" => Product::getProductCategoriesFull($idProduct),
                "image" => Image::getImages($id_lang, $idProduct, $idProductAttribute = null),
                "productFeatures" => Product::getFrontFeaturesStatic($this->context->language->id, $idProduct),
                "defaultAttribute" => Product::getDefaultAttribute($idProduct),
                "productAttributes" => Product::getAttributesInformationsByProduct($idProduct),
                "combinations" => $productComb->getAttributeCombinations($this->context->language->id),
                "resume" => strip_tags($product['description_short']),
                "productDescription" => strip_tags($product['description']),
            ];
        }
        return $allProducts;
    }

    /**
     * Function to get tags from field's
     * params : the tag's putted in the fiels, all the products
     * return meta after switching the tag's with thein values 
     */
    public function getTag($tags, $product)
    {
        $meta = '';
        foreach ($tags as $tag) {
            $id_attribute = '';
            if (substr($tag, 0, 10) === "attributes") {
                $id_attribute = $tag;
            }

            switch ($tag) {

                case 'name':
                    $meta = $meta . $product['name'];
                    break;

                case 'reference':
                    $meta = $meta . $product['reference'];
                    break;

                case 'default_categorie':
                    $meta = $meta . $product['defaultCategory'][$product['idDefaultCategory']]['name'];
                    break;

                case 'first_categorie':
                    $categories_count = count(array_values($product['categories']));
                    if ($categories_count > 1) {
                        $categorie = array_values($product['categories'])[0];
                        $meta = $meta . $categorie['name'] . ' ';
                    } else {
                        $meta = $meta;
                    }
                    break;
                case 'second_categorie':
                    $categories_count = count(array_values($product['categories']));
                    if ($categories_count > 2) {
                        $categorie = array_values($product['categories'])[1];
                        $meta = $meta . $categorie['name'] . ' ';
                    } else {
                        $meta = $meta;
                    }
                    break;
                case 'third_categorie':
                    $categories_count = count(array_values($product['categories']));
                    if ($categories_count > 3) {
                        $categorie = array_values($product['categories'])[2];
                        $meta = $meta . $categorie['name'] . ' ';
                    } else {
                        $meta = $meta;
                    }
                    break;
                case 'fourth_categorie':
                    $categories_count = count(array_values($product['categories']));
                    if ($categories_count > 4) {
                        $categorie = array_values($product['categories'])[3];
                        $meta = $meta . $categorie['name'] . ' ';
                    } else {
                        $meta = $meta;
                    }
                    break;
                case 'fifth_categorie':
                    $categories_count = count(array_values($product['categories']));
                    if ($categories_count > 5) {
                        $categorie = array_values($product['categories'])[4];
                        $meta = $meta . $categorie['name'] . ' ';
                    } else {
                        $meta = $meta;
                    }
                    break;

                case 'features':
                    if (!$product['productFeatures']) {
                        $meta = $meta;
                    } else {
                        foreach ($product['productFeatures'] as $product_feature) {
                            $meta = $meta . $product_feature['name'] . ' : ' . $product_feature['value'] . ' ';
                        }
                    }
                    break;

                case 'price':
                    $meta = $meta . $product['price'];
                    break;

                case 'image_caption':
                    $meta = $meta . $product['image'][0]['legend'];
                    break;

                case $id_attribute:
                    if ($id_attribute) {
                        preg_match_all('!\d+!', $id_attribute, $matches);
                        $id_groupe = $matches[0][0];
                        $combinations = $product['combinations'];
                        $i = 0;
                        foreach ($combinations as $product_attribute) {
                            if ($id_groupe === $product_attribute['id_attribute_group']) {
                                $i++;
                                $groupe_name = $product_attribute['group_name'];
                                if ($i < 2) {
                                    $meta = $meta . $groupe_name . ' : ' . $product_attribute['attribute_name'] . ' ';
                                } else {
                                    $meta = $meta . ' , ' . $product_attribute['attribute_name'] . ' ';
                                }
                            }
                        }
                    }
                    break;

                case 'attributes references':
                    if (!$product['combinations']) {
                        $meta = $meta;
                    } else {
                        $combinations = $product['combinations'];
                        foreach ($combinations as $product_attribute) {
                            $meta = $meta . $product_attribute['reference'] . ' ';
                        }
                    }
                    break;
                case 'resume':
                    $meta = $meta . $product['resume'] . ' ';
                    break;
                case 'product_description':
                    $meta = $meta . $product['productDescription'] . ' ';
                    break;
                default:
                    $meta = $meta .  $tag;
                    break;
            }
        }
        return $meta;
    }
    /**
     * Function to set meta title for simple products only
     * params get all products
     * return : change the meta_title from table product_lang for simple products
     */
    public function setMetaTitleSimpleProduct($products)
    {
        $filename = "../modules/dw_mappingseo/mapping_seo.log";
        file_get_contents($filename);
        file_put_contents($filename, PHP_EOL . PHP_EOL . PHP_EOL . "============================================== UPDATE META TITLE SIMPLE PRODUCTS " . date('d M Y h:i:s A') . " ============================================================" . PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL, FILE_APPEND | LOCK_EX);

        $simpleProductMetaTitlePattern = strval(Tools::getValue('simpleProductMetaTitlePattern'));
        $simpleProductMetaTitles = preg_split("/{{|}}/", $simpleProductMetaTitlePattern);
        $simpleProductMetaTitle = '';
        foreach ($products as $product) {
            $idProduct = $product['idProduct'];
            if (!$product['combinations']) {

                file_put_contents($filename, 'ID PRODUCT ' . $idProduct . ' NAME PRODUCT ' . $product['name'] . '.' . PHP_EOL, FILE_APPEND | LOCK_EX);

                $simpleProductMetaTitle = $this->getTag($simpleProductMetaTitles, $product);
                $simpleProductMetaTitle = $simpleProductMetaTitle . ' |Diabolo';

                file_put_contents($filename, 'ID PRODUCT ' . $idProduct . ' META TITLE ' . $simpleProductMetaTitle . '.' . PHP_EOL, FILE_APPEND | LOCK_EX);

                $sql_meta_title_simple_product = ' UPDATE `' . _DB_PREFIX_ . 'product_lang` SET `meta_title` = "' .  $simpleProductMetaTitle . '"  WHERE `id_product` = "' .  $idProduct . '"';
                Db::getInstance()->execute($sql_meta_title_simple_product);

                $simpleProductMetaTitle = '';
            }
        }
    }
    /**
     * Function to set meta title for products with combinations only
     *  params get all products
     * return : change the meta_title from table product_lang for combination products
     */
    public function setMetaTitleCombinationProduct($products)
    {
        $filename = "../modules/dw_mappingseo/mapping_seo.log";
        file_get_contents($filename);
        file_put_contents($filename, PHP_EOL . PHP_EOL . PHP_EOL . "============================================== UPDATE META TITLE COMBINATION PRODUCTS " . date('d M Y h:i:s A') . " ============================================================" . PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL, FILE_APPEND | LOCK_EX);

        $ProductWithCombinationsMetaTitlePattern = strval(Tools::getValue('ProductWithCombinationsMetaTitlePattern'));
        $CombinationsProductMetaTitles = preg_split("/{{|}}/", $ProductWithCombinationsMetaTitlePattern);
        $CombinationProductMetaTitle = '';

        foreach ($products as $product) {
            $idProduct = $product['idProduct'];
            if ($product['combinations']) {

                file_put_contents($filename, 'ID PRODUCT ' . $idProduct . ' NAME PRODUCT ' . $product['name'] . '.' . " ============================================================" . PHP_EOL, FILE_APPEND | LOCK_EX);

                $CombinationProductMetaTitle = $this->getTag($CombinationsProductMetaTitles, $product);
                $CombinationProductMetaTitle = $CombinationProductMetaTitle . ' |Diabolo';

                file_put_contents($filename, 'ID PRODUCT ' . $idProduct . ' META TITLE ' . $CombinationProductMetaTitle . '.' . PHP_EOL, FILE_APPEND | LOCK_EX);

                $sql_meta_title_Combination_product = ' UPDATE `' . _DB_PREFIX_ . 'product_lang` SET `meta_title` = "' .  $CombinationProductMetaTitle . '"  WHERE `id_product` = "' .  $idProduct . '"';
                Db::getInstance()->execute($sql_meta_title_Combination_product);

                $CombinationProductMetaTitle = '';
            }
        }
    }
    /**
     * Function to set all the meta descriptions
     * params get all products
     * return : change the meta_description from table product_lang for all products
     */
    public function setMetaDescription($products)
    {
        $filename = "../modules/dw_mappingseo/mapping_seo.log";
        file_get_contents($filename);
        file_put_contents($filename, PHP_EOL . PHP_EOL . PHP_EOL . "============================================== UPDATE META TITLE COMBINATION PRODUCTS " . date('d M Y h:i:s A') . " ============================================================" . PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL, FILE_APPEND | LOCK_EX);

        $ProductMetaDescriptionPattern = strval(Tools::getValue('ProductMetaDescriptionPattern'));
        $ProductDescriptionMetaTitles = preg_split("/{{|}}/", $ProductMetaDescriptionPattern);
        $ProductDescriptionMetaTitle = '';

        foreach ($products as $product) {
            $idProduct = $product['idProduct'];
            file_put_contents($filename, 'ID PRODUCT ' . $idProduct . ' NAME PRODUCT ' . $product['name'] . '.' . " ============================================================" . PHP_EOL, FILE_APPEND | LOCK_EX);

            $ProductDescriptionMetaTitle = $this->getTag($ProductDescriptionMetaTitles, $product);

            file_put_contents($filename, 'ID PRODUCT ' . $idProduct . ' META DESCRIPTION ' . $ProductDescriptionMetaTitle . '.' . PHP_EOL, FILE_APPEND | LOCK_EX);
            // dump('le produit : ' . $idProduct . ' : ' . $ProductDescriptionMetaTitle . '<br>');

            $sql_meta_Description = ' UPDATE `' . _DB_PREFIX_ . 'product_lang` SET `meta_description` = "' .  $ProductDescriptionMetaTitle . '"  WHERE `id_product` = "' .  $idProduct . '"';
            Db::getInstance()->execute($sql_meta_Description);

            $ProductDescriptionMetaTitle = '';
        }
    }
    /**
     * Function to set all Alt images
     * params get all products
     * return : change the legend from table image_lang for all images
     */
    public function setAltImage($products)
    {
        $filename = "../modules/dw_mappingseo/mapping_seo.log";
        file_get_contents($filename);
        file_put_contents($filename, PHP_EOL . PHP_EOL . PHP_EOL . "============================================== UPDATE ALT IMAGES " . date('d M Y h:i:s A') . " ============================================================" . PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL, FILE_APPEND | LOCK_EX);


        $AltImagePattern = strval(Tools::getValue('AltImagePattern'));
        $AltImageTitles = preg_split("/{{|}}/", $AltImagePattern);
        $AltImageTitle = '';

        foreach ($products as $product) {

            $images = $product['image'];
            foreach ($images as $image) {

                $idImage = $image['id_image'];
                $AltImageTitle = $this->getTag($AltImageTitles, $product);

                file_put_contents($filename, 'ID IMAGE ' . $idImage . ' ALT IMAGE ' . $AltImageTitle . '.' . PHP_EOL, FILE_APPEND | LOCK_EX);

                $sql_alt_image = ' UPDATE `' . _DB_PREFIX_ . 'image_lang` SET `legend` = "' .  $AltImageTitle . '"  WHERE `id_image` = "' .  $idImage . '"';
                Db::getInstance()->execute($sql_alt_image);

                $AltImageTitle = '';
            }
        }
    }



    /**
     * Load the configuration form
     */
    public function getContent()
    {
        $output = null;
        /**
         * If values have been submitted in the form, process.
         */
        if (Tools::isSubmit('submit2dw_mappingseo')) {
            $simpleProductMetaTitlePattern = strval(Tools::getValue('simpleProductMetaTitlePattern'));
            $ProductWithCombinationsMetaTitlePattern = strval(Tools::getValue('ProductWithCombinationsMetaTitlePattern'));
            $ProductMetaDescriptionPattern = strval(Tools::getValue('ProductMetaDescriptionPattern'));
            $AltImagePattern = strval(Tools::getValue('AltImagePattern'));

            Configuration::updateValue('simpleProductMetaTitlePattern', $simpleProductMetaTitlePattern);
            Configuration::updateValue('ProductWithCombinationsMetaTitlePattern', $ProductWithCombinationsMetaTitlePattern);
            Configuration::updateValue('ProductMetaDescriptionPattern', $ProductMetaDescriptionPattern);
            Configuration::updateValue('AltImagePattern', $AltImagePattern);
            $output .= $this->displayConfirmation($this->l('Settings updated'));
        }
        //get all products in the var $products
        $id_lang = (int)Context::getContext()->language->id;
        $start = 0;
        $limit = 100;
        $order_by = 'id_product';
        $order_way = 'DESC';
        $id_category = false;
        $only_active = true;
        $context = null;
        $all_products = Product::getProducts(
            $id_lang,
            $start,
            $limit,
            $order_by,
            $order_way,
            $id_category,
            $only_active,
            $context
        );
        $products = $this->getProducts($all_products);
        if (Tools::isSubmit('SubmitMeta')) {

            $metaTitleSimpleProduct = $this->setMetaTitleSimpleProduct($products);
            $metaTitleCombinationsProduct = $this->setMetaTitleCombinationProduct($products);
            $metaDescription = $this->setMetaDescription($products);
            $metaAltImage = $this->setAltImage($products);
            return ($metaAltImage && $metaTitleSimpleProduct && $metaTitleCombinationsProduct && $metaDescription) . $this->displayConfirmation($this->l('Meta successfully updated')) . $this->displayForm();;
        }

        $this->context->smarty->assign(
            'products',
            $products
        );
        return $output . $this->displayForm();
    }


    public function displayForm()
    {
        // Get default Language
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        // Init Fields form array
        $fields_form = array();
        $fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Mapping SEO ettings'),
            ),
            'input' => array(

                array(
                    'type' => 'text',
                    'label' => $this->l('Simple Product Meta Title pattern'),
                    'name' => 'simpleProductMetaTitlePattern',
                    'desc' => 'Tags : {{name}} - {{reference}} - {{default_categorie}} - {{first_categorie}} - {{second_categorie}} - {{third_categorie}} - {{fourth_categorie}} - {{fifth_categorie}} - {{features}} - {{price}} - {{image_caption}}',
                    'size' => 20,
                    'suffix' => '|Diabolo',
                    'required' => false
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Product with combinations Meta Title pattern'),
                    'name' => 'ProductWithCombinationsMetaTitlePattern',
                    'desc' => 'Tags : {{name}} - {{reference}} - {{default_categorie}} - {{first_categorie}} - {{second_categorie}} - {{third_categorie}} - {{fourth_categorie}} - {{fifth_categorie}} - {{features}} - {{price}} - {{image_caption}} - {{attributes($id)}} - {{attributes references}}',
                    'size' => 20,
                    'suffix' => '|Diabolo',
                    'required' => false
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Product Meta Description pattern'),
                    'name' => 'ProductMetaDescriptionPattern',
                    'desc' => 'Tags : {{name}} - {{reference}} - {{resume}} - {{product_description}} - {{default_categorie}} - {{first_categorie}} - {{second_categorie}} - {{third_categorie}} - {{fourth_categorie}} - {{fifth_categorie}} - {{features}} - {{price}} - {{image_caption}} - {{attributes($id)}} - {{attributes references}}',
                    'size' => 20,
                    'suffix' => '|Diabolo',
                    'required' => false
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Alt image pattern'),
                    'name' => 'AltImagePattern',
                    'desc' => 'Tags : {{name}} - {{reference}} - {{default_categorie}} - {{first_categorie}} - {{second_categorie}} - {{third_categorie}} - {{fourth_categorie}} - {{fifth_categorie}} - {{features}} - {{price}} - {{image_caption}} - {{attributes($id)}} - {{attributes references}}',
                    'size' => 20,
                    'required' => false
                ),
            ),

            'submit' => array(
                'title' => $this->l('Save')
            ),
            'buttons' => array(
                'save-and-stay' => array(
                    'title' => $this->l('Update Meta'),
                    'name' => 'SubmitMeta',
                    'type' => 'submit',
                    'class' => 'btn btn-default pull-right',
                    'icon' => 'process-icon-save',
                ),
            )
        );
        $helper = new HelperForm();
        // Module, Token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex
            . '&configure='
            . $this->name;
        // Language
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;
        // title and Submit
        $helper->title = $this->displayName;
        $helper->submit_action = 'submit' . $this->name;
        $helper->submit_action = 'submit2' . $this->name;
        // Load current value
        $helper->fields_value['simpleProductMetaTitlePattern'] = Configuration::get('simpleProductMetaTitlePattern');
        $helper->fields_value['ProductWithCombinationsMetaTitlePattern'] = Configuration::get('ProductWithCombinationsMetaTitlePattern');
        $helper->fields_value['ProductMetaDescriptionPattern'] = Configuration::get('ProductMetaDescriptionPattern');
        $helper->fields_value['AltImagePattern'] = Configuration::get('AltImagePattern');
        return $helper->generateForm($fields_form);
    }

    /**
     * Add the CSS & JavaScript files you want to be loaded in the BO.
     */
    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name) {
            $this->context->controller->addJS($this->_path . 'views/js/back.js');
            $this->context->controller->addCSS($this->_path . 'views/css/back.css');
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path . '/views/js/front.js');
        $this->context->controller->addCSS($this->_path . '/views/css/front.css');
    }
}
