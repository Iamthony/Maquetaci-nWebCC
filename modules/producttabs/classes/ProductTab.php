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

class ProductTab extends ObjectModel
{

    public $id_product_tab;
    public $active;
    public $title;
    public $content;
    public $product_id;
    public $position;

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
        'table' => 'product_tab',
        'primary' => 'id_product_tab',
        'multilang' => true,
        'multilang_shop' => true,
        'multishop' => true,
        'fields' => array(
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'id_product_tab' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'position' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),

            //lang fields
            'title' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isString', 'size' => 255),
            'content' => array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isString'),

            //shop fields
            'product_id' => array('type' => self::TYPE_INT, 'shop' => true, 'validate' => 'isUnsignedId'),
            'tab_content_type' => array(
                'type' => self::TYPE_STRING,
                'shop' => true,
                'validate' => 'isString',
                'size' => 50
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

        return Db::getInstance()->executeS(
            'SELECT * FROM ' . _DB_PREFIX_ . self::$definition['table'] . ' AS pt '
            . ' LEFT JOIN ' . _DB_PREFIX_ . self::$definition['table'] . '_lang AS ptl '
            . ' ON (ptl.' . self::$definition['primary'] . ' = pt.' . self::$definition['primary'] . ' '
                . Shop::addSqlRestrictionOnLang('ptl') . ')'
            . (isset($context->customer) ?
                ' INNER JOIN ' . _DB_PREFIX_ . self::$definition['table'] . '_group AS ptg '
            . ' ON (ptg.' . self::$definition['primary'] . ' = pt.' . self::$definition['primary']
            . ' AND ptg.id_group IN (' . implode(',', Customer::getGroupsStatic($context->customer->id)) . ') ) '
             : '')
            . 'WHERE ' . ($active ? 'pt.active = ' . (int)$active . ' AND ' : '')
            . ' pt.product_id = ' . (int)$product_id
            . ' AND ptl.id_lang = ' . pSQL($context->language->id)
            . ' ORDER BY pt.position ASC;'
        );
    }

    public static function getIdByProductId($product_id)
    {
        $context = Context::getContext();

        return Db::getInstance()->getValue(
            'SELECT pt.' . self::$definition['primary']
            . ' FROM ' . _DB_PREFIX_ . self::$definition['table'] . ' AS pt '
            . ' LEFT JOIN ' . _DB_PREFIX_ . self::$definition['table'] . '_lang AS ptl '
            . ' ON (ptl.' . self::$definition['primary'] . ' = pt.' . self::$definition['primary'] . ' '
            . Shop::addSqlRestrictionOnLang('ptl') . ') '
            . ' WHERE pt.product_id = ' . (int)$product_id
            . ' AND ptl.id_lang = ' . pSQL($context->language->id)
        );
    }

    public function add($auto_date = true, $null_values = false)
    {
        $return = parent::add($auto_date, $null_values);
        if (Tools::getValue('groupBox')) {
            $this->updateGroup(Tools::getValue('groupBox'));
        }
        return $return;
    }

    public function update($null_values = false)
    {
        if (Tools::getValue('groupBox')) {
            $this->updateGroup(Tools::getValue('groupBox'));
        }
        $return = parent::update($null_values);
        return $return;
    }

    public function delete()
    {
        $this->cleanGroups();
        $id_product_tab_settings = ProductTabSettings::getIdByProductId(Tools::getValue('id_product'));
        $product_tab_settings = new ProductTabSettings($id_product_tab_settings);
        $product_tab_settings->delete();
        return parent::delete();
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
            $sql->select('ptg.`id_group`');
            $sql->from(self::$definition['table'] . '_group', 'ptg');
            $sql->where('ptg.`' . self::$definition['primary'] . '` = '.(int) $this->id);
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

    protected function createTable()
    {
        $tables = array();

        $tables[] = 'CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . self::$definition['table'] . ' ('
            . '`id_product_tab` INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT, '
            . '`active` BOOL DEFAULT 1, '
            . '`product_id` INT(11) NOT NULL, '
            . '`position` INT(11) NOT NULL, '
            . '`tab_content_type` CHAR(50) NOT NULL, '
            . '`date_add` TIMESTAMP, '
            . '`date_upd` TIMESTAMP '
            . ') ENGINE=' . _MYSQL_ENGINE_ . ' CHARACTER SET=UTF8;';

        $tables[] = 'CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . self::$definition['table'] . '_lang ('
            . '`id_product_tab` INT(11) NOT NULL, '
            . '`id_lang` INT(11) NOT NULL, '
            . '`id_shop` INT(11) NOT NULL, '
            . '`title` CHAR(255) NOT NULL, '
            . '`content` VARCHAR(10000) NOT NULL, '
            . 'INDEX product_tab_lang (`id_product_tab`, `id_lang`, `id_shop`) '
            . ') ENGINE=' . _MYSQL_ENGINE_ . ' CHARACTER SET=UTF8;';

        $tables[] = 'CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . self::$definition['table'] . '_shop ('
            . '`id_product_tab` INT(11) NOT NULL, '
            . '`id_shop` INT(11) NOT NULL, '
            . '`product_id` INT(11) NOT NULL, '
            . '`tab_content_type` CHAR(50) NOT NULL, '
            . 'INDEX product_tab_shop (`id_product_tab`, `id_shop`, `product_id`, `tab_content_type`) '
            . ') ENGINE=' . _MYSQL_ENGINE_ . ' CHARACTER SET=UTF8;';

        $tables[] = 'CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . self::$definition['table'] . '_group ('
            . '`id_product_tab` INT(11) NOT NULL, '
            . '`id_group` INT(11) NOT NULL, '
            . 'INDEX product_tab_shop (`id_product_tab`, `id_group`) '
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
        $tables[] = 'DROP TABLE IF EXISTS ' . _DB_PREFIX_ . self::$definition['table'] . '_shop;';

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
