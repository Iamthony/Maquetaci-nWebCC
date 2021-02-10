<?php
/**
* 2007-2018 Belvg
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
*  @author Belvg
*  @copyright  2007-2018 Belvg
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of Belvg
*/

class GlobalProductTab extends ObjectModel
{

    public $id_product_tab_global;
    public $active;
    public $title;
    public $content;
    public $position;
    public $id_shop_default;

    public $tab_content_type;

    public $tab_content_types = array(
        'content',
        'contact_form',
        'description',
        'product_details',
        'product_attachments',
    );

    public $date_add;
    public $date_upd;

    public $groupBox;

    public static $definition = array(
        'table' => 'product_tab_global',
        'primary' => 'id_product_tab_global',
        'multilang' => true,
        'multilang_shop' => true,
        'multishop' => true,
        'fields' => array(
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'id_product_tab_global' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'position' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'id_shop_default' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),

            //lang fields
            'title' => array(
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isString',
                'size' => 255,
                'required' => true
            ),
            'content' => array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isString'),
            'tab_content_type' => array(
                'type' => self::TYPE_STRING,
                'shop' => true,
                'validate' => 'isString', 'size' => 50
            ),
        ),
    );

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        Shop::addTableAssociation(self::$definition['table'], array('type' => 'shop'));
        Shop::addTableAssociation(self::$definition['table'] . '_lang', array('type' => 'fk_shop'));
        parent::__construct($id, $id_lang, $id_shop);
    }

    /**
     * Return ALL product tabs by product id
     *
     * @param $product_id
     * @param bool $active
     * @return array|false|mysqli_result|null|PDOStatement|resource
     * @throws PrestaShopDatabaseException
     */
    public static function getTabsByProductId($product_id, $active = true)
    {
        $context = Context::getContext();

        $product = new Product($product_id);
        $suppliers = self::getProductSuppliers($product_id);
        if (empty($suppliers)) {
            $suppliers = array(0);
        }
        $categories = Product::getProductCategories($product_id);
        if (empty($categories)) {
            $categories = array(0);
        }
        $manufacturer = $product->id_manufacturer;

        Shop::addTableAssociation(self::$definition['table'], array('type' => 'shop'));
        Shop::addTableAssociation(self::$definition['table'], array('type' => 'fk_shop'));

        $rows = Db::getInstance()->executeS(
            'SELECT pt.id_product_tab_global, pt.position, ptl.title, '
            . ' ptl.content, ptc.category_id, ptm.manufacturer_id, pts.supplier_id, '
            . self::$definition['table'] . '_shop.tab_content_type '
            . ' FROM ' . _DB_PREFIX_ . self::$definition['table'] . ' AS pt '
            . Shop::addSqlAssociation(self::$definition['table'], 'pt')
            . ' LEFT JOIN ' . _DB_PREFIX_ . self::$definition['table'] . '_lang AS ptl '
            . ' ON (ptl.' . self::$definition['primary'] . ' = pt.' . self::$definition['primary'] . ' '
            . ' AND ptl.`id_lang` = ' . (int)$context->language->id
            . ' AND ptl.`id_shop` = ' . (int)$context->shop->id . ')'

            . ' INNER JOIN ' . _DB_PREFIX_ . self::$definition['table'] . '_group AS ptg '
            . ' ON (ptg.' . self::$definition['primary'] . ' = pt.' . self::$definition['primary'] . ') '

            . ' LEFT JOIN ' . _DB_PREFIX_ . self::$definition['table'] . '_categories AS ptc '
            . ' ON (ptc.' . self::$definition['primary'] . ' = pt.' . self::$definition['primary'] . ') '

            . ' LEFT JOIN ' . _DB_PREFIX_ . self::$definition['table'] . '_manufacturers AS ptm '
            . ' ON (ptm.' . self::$definition['primary'] . ' = pt.' . self::$definition['primary'] . ') '

            . ' LEFT JOIN ' . _DB_PREFIX_ . self::$definition['table'] . '_suppliers AS pts '
            . ' ON (pts.' . self::$definition['primary'] . ' = pt.' . self::$definition['primary'] . ') '

            . ' WHERE (' . ($active ? 'pt.`active` = ' . (int)$active : '')
            . ' AND ptg.id_group IN (' . implode(',', Customer::getGroupsStatic($context->customer->id)) . ') )'

            . ' AND (ptc.`category_id` IN (' . implode(',', $categories). ') '
            . ' OR ptm.`manufacturer_id` = ' . (int)$manufacturer
            . ' OR pts.`supplier_id` IN (' . implode(',', $suppliers). ') ) '
            . ' ORDER BY pt.`position` ASC;'
        );

        $ids = array();
        foreach ($rows as $key => $row) {

            if (!isset($ids[$row[self::$definition['primary']]])) {
                $ids[$row[self::$definition['primary']]] = $row[self::$definition['primary']];
            } else {
                unset($rows[$key]);
                continue;
            }

            if (!is_null($row['category_id'])
                && !in_array($row['category_id'], $categories)) {
                unset($rows[$key]);
            }

            if (!is_null($row['supplier_id'])
                && !in_array($row['supplier_id'], $suppliers)) {
                unset($rows[$key]);
            }

            if (!is_null($row['manufacturer_id'])
                && $product->id_manufacturer != $row['manufacturer_id']) {
                unset($rows[$key]);
            }
        }

        return !empty($rows) ? $rows : array();
    }

    public static function getProductSuppliers($product_id)
    {
        $result = array();
        $context = Context::getContext();

        $suppliers = Db::getInstance()->executeS(
            'SELECT * FROM ' . _DB_PREFIX_ . 'product_supplier AS ps '
            . ' INNER JOIN ' . _DB_PREFIX_ . 'supplier_lang AS sl '
            . ' ON (sl.`id_supplier` = ps.`id_supplier` '
            . ' AND sl.id_lang = ' . (int)$context->language->id . ')'
            . Shop::addSqlAssociation('supplier', 'ps')
            . ' WHERE ps.`id_product` = ' . (int)$product_id
        );

        foreach ($suppliers as $supplier) {
            $result[] = $supplier['id_supplier'];
        }

        return $result;
    }

    public function add($auto_date = true, $null_values = false)
    {
        $context = Context::getContext();
        $this->id_shop_default = $context->shop->id;
        $return = parent::add($auto_date, $null_values);
        $this->updateGroup($this->groupBox);
        $this->addData();
        return $return;
    }

    public function update($null_values = false)
    {
        $context = Context::getContext();
        $this->id_shop_default = $context->shop->id;
        $this->updateGroup($this->groupBox);
        $return = parent::update($null_values);
        $this->addData();
        return $return;
    }

    public function delete()
    {
        self::deleteCategoriesByIdTab($this->id);
        self::deleteManufacturersByIdTab($this->id);
        self::deleteSuppliersByIdTab($this->id);
        $this->cleanGroups();
        return parent::delete();
    }

    public function addData()
    {

        if (Tools::isSubmit('submitAdd' . self::$definition['table'])) {
            self::deleteCategoriesByIdTab($this->id);
            if ($categories = Tools::getValue('categories')) {
                self::setCategories($this->id, $categories);
            }

            self::deleteManufacturersByIdTab($this->id);
            if ($manufacturer_id = Tools::getValue('manufacturer_id')) {
                self::setManufacturers($this->id, $manufacturer_id);
            }

            self::deleteSuppliersByIdTab($this->id);
            if ($supplier_id = Tools::getValue('supplier_id')) {
                self::setSuppliers($this->id, $supplier_id);
            }
        }
    }

    /**
     * Add Category groups
     *
     * @param $groups
     */
    public function addGroups($groups)
    {
        foreach ($groups as $group) {
            if ($group !== false) {
                Db::getInstance()->insert(
                    self::$definition['table'] . '_group',
                    array(self::$definition['primary'] => (int) $this->id, 'id_group' => (int) $group)
                );
            }
        }
    }

    /**
     * Get Category groups
     *
     * @return array|null
     */
    public function getGroups()
    {
        $cacheId = 'Category::getGroups_'.(int) $this->id;
        if (!Cache::isStored($cacheId)) {
            $sql = new DbQuery();
            $sql->select('ptgg.`id_group`');
            $sql->from(self::$definition['table'] . '_group', 'ptgg');
            $sql->where('ptgg.`' . self::$definition['primary'] . '` = '.(int) $this->id);
            $result = Db::getInstance()->executeS($sql);
            $groups = array();
            foreach ($result as $group) {
                $groups[] = $group['id_group'];
            }
            Cache::store($cacheId, $groups);

            return $groups;
        }

        return Cache::retrieve($cacheId);
    }

    /**
     * Add group if it does not exist
     *
     * @param int $idGroup Group ID
     *
     * @return bool|void
     */
    public function addGroupsIfNoExist($idGroup)
    {
        $groups = $this->getGroups();
        if (!in_array((int) $idGroup, $groups)) {
            return $this->addGroups(array((int)$idGroup));
        }

        return false;
    }

    /**
     * Update customer groups associated to the object
     *
     * @param array $list groups
     */
    public function updateGroup($list)
    {
        $this->cleanGroups();
        if (empty($list)) {
            $list = array(
                Configuration::get('PS_UNIDENTIFIED_GROUP'),
                Configuration::get('PS_GUEST_GROUP'),
                Configuration::get('PS_CUSTOMER_GROUP')
            );
        }
        $this->addGroups($list);
    }

    /**
     * Clean Category Groups
     *
     * @return bool Indicated whether the cleanup was successful
     */
    public function cleanGroups()
    {
        return Db::getInstance()->delete(
            self::$definition['table'] . '_group',
            self::$definition['primary'] . ' = '.(int) $this->id
        );
    }

    public static function install()
    {
        $product_tab = new self();
        if ($product_tab->createTable()) {
            return true;
        }

        return false;
    }

    public static function setCategories($product_tab_id, $categories)
    {
        if (is_array($categories)) {
            foreach ($categories as $category) {
                Db::getInstance()->insert(
                    self::$definition['table'] . '_categories',
                    array(
                        self::$definition['primary'] => (int)$product_tab_id,
                        'category_id' => (int)$category,
                    )
                );
            }
        }
    }

    public static function getCategoriesByIdTab($product_tab_id)
    {
        $categories = array();
        $rows = Db::getInstance()->executeS(
            'SELECT * FROM ' . _DB_PREFIX_ . self::$definition['table'] . '_categories '
            . ' WHERE ' . self::$definition['primary'] . ' = ' . (int)$product_tab_id
        );
        foreach ($rows as $row) {
            $categories[] = $row['category_id'];
        }

        return $categories;
    }

    public static function deleteCategoriesByIdTab($product_tab_id)
    {
        return Db::getInstance()->delete(
            self::$definition['table'] . '_categories',
            self::$definition['primary'] . ' = ' . (int)$product_tab_id
        );
    }

    public static function setManufacturers($product_tab_id, $manufacturers)
    {
        if (is_array($manufacturers)) {
            foreach ($manufacturers as $manufacturer) {
                Db::getInstance()->insert(
                    self::$definition['table'] . '_manufacturers',
                    array(
                        self::$definition['primary'] => (int)$product_tab_id,
                        'manufacturer_id' => (int)$manufacturer,
                    )
                );
            }
        }
    }

    public static function getManufacturersByIdTab($product_tab_id)
    {
        $manufacturers = array();
        $rows = Db::getInstance()->executeS(
            'SELECT * FROM ' . _DB_PREFIX_ . self::$definition['table'] . '_manufacturers '
            . ' WHERE ' . self::$definition['primary'] . ' = ' . (int)$product_tab_id
        );
        foreach ($rows as $row) {
            $manufacturers[] = $row['manufacturer_id'];
        }

        return $manufacturers;
    }

    public static function deleteManufacturersByIdTab($product_tab_id)
    {
        return Db::getInstance()->delete(
            self::$definition['table'] . '_manufacturers',
            self::$definition['primary'] . ' = ' . (int)$product_tab_id
        );
    }

    public static function setSuppliers($product_tab_id, $suppliers)
    {
        if (is_array($suppliers)) {
            foreach ($suppliers as $supplier) {
                Db::getInstance()->insert(
                    self::$definition['table'] . '_suppliers',
                    array(
                        self::$definition['primary'] => (int)$product_tab_id,
                        'supplier_id' => (int)$supplier,
                    )
                );
            }
        }
    }

    public static function getSuppliersByIdTab($product_tab_id)
    {
        $suppliers = array();
        $rows = Db::getInstance()->executeS(
            'SELECT * FROM ' . _DB_PREFIX_ . self::$definition['table'] . '_suppliers WHERE '
            . self::$definition['primary'] . ' = ' . (int)$product_tab_id
        );
        foreach ($rows as $row) {
            $suppliers[] = $row['supplier_id'];
        }

        return $suppliers;
    }

    public static function deleteSuppliersByIdTab($product_tab_id)
    {
        return Db::getInstance()->delete(
            self::$definition['table'] . '_suppliers',
            self::$definition['primary'] . ' = ' . (int)$product_tab_id
        );
    }

    protected function createTable()
    {
        $tables = array();

        $tables[] = 'CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . self::$definition['table'] . ' ('
            . '`' . self::$definition['primary'] . '` INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT, '
            . '`active` BOOL DEFAULT 1, '
            . '`position` INT(11) NOT NULL, '
            . '`id_shop_default` INT(11) NOT NULL, '
            . '`tab_content_type` CHAR(50) NOT NULL, '
            . '`date_add` TIMESTAMP, '
            . '`date_upd` TIMESTAMP '
            . ') ENGINE=' . _MYSQL_ENGINE_ . ' CHARACTER SET=UTF8;';

        $tables[] = 'CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . self::$definition['table'] . '_lang ('
            . '`' . self::$definition['primary'] . '` INT(11) NOT NULL, '
            . '`id_lang` INT(11) NOT NULL, '
            . '`id_shop` INT(11) NOT NULL, '
            . '`title` CHAR(255) NOT NULL, '
            . '`content` VARCHAR(10000) NOT NULL, '
            . ' INDEX ' . self::$definition['table']
            . '_lang (`' . self::$definition['primary'] . '`, `id_lang`, `id_shop`) '
            . ') ENGINE=' . _MYSQL_ENGINE_ . ' CHARACTER SET=UTF8;';

        $tables[] = 'CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . self::$definition['table'] . '_shop ('
            . '`' . self::$definition['primary'] . '` INT(11) NOT NULL, '
            . '`id_shop` INT(11) NOT NULL, '
            . '`tab_content_type` CHAR(50) NOT NULL, '
            . 'UNIQUE KEY ' . self::$definition['table']
            . '_shop (`' . self::$definition['primary'] . '`, `id_shop`) '
            . ') ENGINE=' . _MYSQL_ENGINE_ . ' CHARACTER SET=UTF8;';

        $tables[] = 'CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . self::$definition['table'] . '_group ('
            . '`' . self::$definition['primary'] . '` INT(11) NOT NULL, '
            . '`id_group` INT(11) NOT NULL, '
            . 'INDEX ' . self::$definition['table']
            . '_group (`' . self::$definition['primary'] . '`, `id_group`) '
            . ') ENGINE=' . _MYSQL_ENGINE_ . ' CHARACTER SET=UTF8;';

        $tables[] = 'CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . self::$definition['table'] . '_categories ('
            . '`' . self::$definition['primary'] . '` INT(11) NOT NULL, '
            . '`category_id` INT(11) NOT NULL, '
            . 'INDEX ' . self::$definition['table']
            . '_categories (`' . self::$definition['primary'] . '`, `category_id`) '
            . ') ENGINE=' . _MYSQL_ENGINE_ . ' CHARACTER SET=UTF8;';

        $tables[] = 'CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . self::$definition['table'] . '_manufacturers ('
            . '`' . self::$definition['primary'] . '` INT(11) NOT NULL, '
            . '`manufacturer_id` INT(11) NOT NULL, '
            . 'INDEX ' . self::$definition['table']
            . '_manufacturers (`' . self::$definition['primary'] . '`, `manufacturer_id`) '
            . ') ENGINE=' . _MYSQL_ENGINE_ . ' CHARACTER SET=UTF8;';

        $tables[] = 'CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . self::$definition['table'] . '_suppliers ('
            . '`' . self::$definition['primary'] . '` INT(11) NOT NULL, '
            . '`supplier_id` INT(11) NOT NULL, '
            . 'INDEX ' . self::$definition['table']
            . '_suppliers (`' . self::$definition['primary'] . '`, `supplier_id`) '
            . ') ENGINE=' . _MYSQL_ENGINE_ . ' CHARACTER SET=UTF8;';


        return $this->execute($tables);
    }

    public static function uninstall()
    {
        $product_tab = new self();
        if ($product_tab->dropTables()) {
            return true;
        }

        return false;
    }

    protected function dropTables()
    {
        $tables = array();

        $tables[] = 'DROP TABLE IF EXISTS ' . _DB_PREFIX_ . self::$definition['table'] . ';';
        $tables[] = 'DROP TABLE IF EXISTS ' . _DB_PREFIX_ . self::$definition['table'] . '_lang;';
        $tables[] = 'DROP TABLE IF EXISTS ' . _DB_PREFIX_ . self::$definition['table'] . '_shop;';
        $tables[] = 'DROP TABLE IF EXISTS ' . _DB_PREFIX_ . self::$definition['table'] . '_group;';
        $tables[] = 'DROP TABLE IF EXISTS ' . _DB_PREFIX_ . self::$definition['table'] . '_categories;';
        $tables[] = 'DROP TABLE IF EXISTS ' . _DB_PREFIX_ . self::$definition['table'] . '_manufacturers;';
        $tables[] = 'DROP TABLE IF EXISTS ' . _DB_PREFIX_ . self::$definition['table'] . '_suppliers;';

        return $this->execute($tables);
    }

    protected function query($sql)
    {
        try {
            return Db::getInstance()->execute($sql);
        } catch (Exception $e) {
            throw new PrestaShopException($e->getMessage());
        }
    }

    protected function execute($sqls)
    {
        foreach ($sqls as $sql) {
            if (!$this->query($sql)) {
                return false;
            }
        }

        return true;
    }
}
