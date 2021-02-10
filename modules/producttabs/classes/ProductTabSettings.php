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

class ProductTabSettings extends ObjectModel
{
    public $id_producttabs_settings;
    public $product_id;
    public $tab_type;

    public $tab_types = array(
        'vertical',
        'horizontal',
        'accordion',
    );

    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'producttabs_settings',
        'primary' => 'id_producttabs_settings',
        'multishop' => true,
        'fields' => array(
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'id_producttabs_settings' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),

            //shop fields
            'product_id' => array('type' => self::TYPE_INT, 'shop' => true, 'validate' => 'isUnsignedId'),
            'tab_type' => array('type' => self::TYPE_STRING, 'shop' => true, 'validate' => 'isString', 'size' => 50),
        ),
    );

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        Shop::addTableAssociation(self::$definition['table'], array('type' => 'shop'));
        parent::__construct($id, $id_lang, $id_shop);
    }


    public static function getIdByProductId($product_id)
    {
        Shop::addTableAssociation(self::$definition['table'], array('type' => 'shop'));

        return Db::getInstance()->getValue(
            'SELECT pts.' . self::$definition['primary'] . ' FROM '
            . _DB_PREFIX_ . self::$definition['table'] . ' AS pts '
            . Shop::addSqlAssociation(self::$definition['table'], 'pts')
            . 'WHERE pts.product_id = ' . (int)$product_id
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
            . '`' . self::$definition['primary'] . '` INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT, '
            . '`product_id` INT(11) NOT NULL, '
            . '`tab_type` CHAR(50) NOT NULL, '
            . '`date_add` TIMESTAMP, '
            . '`date_upd` TIMESTAMP '
            . ') ENGINE=' . _MYSQL_ENGINE_ . ' CHARACTER SET=UTF8;';

        $tables[] = 'CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . self::$definition['table'] . '_shop ('
            . '`' . self::$definition['primary'] . '` INT(11) NOT NULL, '
            . '`id_shop` INT(11) NOT NULL, '
            . '`product_id` INT(11) NOT NULL, '
            . '`tab_type` CHAR(50) NOT NULL, '
            . 'INDEX product_tab_shop (`' . self::$definition['primary'] . '`, `id_shop`, `product_id`, `tab_type`) '
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
