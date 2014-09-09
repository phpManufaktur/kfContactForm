<?php

/**
 * ContactForm
 *
 * @author Team phpManufaktur <team@phpmanufaktur.de>
 * @link https://kit2.phpmanufaktur.de/ContactForm
 * @copyright 2014 Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @license MIT License (MIT) http://www.opensource.org/licenses/MIT
 */

namespace phpManufaktur\ContactForm\Data\Form;

use Silex\Application;

class Definition
{
    protected $app = null;
    protected static $table_name = null;

    /**
     * Constructor
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        self::$table_name = FRAMEWORK_TABLE_PREFIX.'contact_form_definition';
    }

    /**
     * Create the table
     *
     * @throws \Exception
     */
    public function createTable()
    {
        $table = self::$table_name;

        $SQL = <<<EOD
    CREATE TABLE IF NOT EXISTS `$table` (
        `form_id` INT(11) NOT NULL AUTO_INCREMENT,
        `form_name` VARCHAR(64) NOT NULL DEFAULT '',
        `form_description` TEXT NOT NULL,
        `form_status` ENUM('ACTIVE', 'LOCKED', 'DELETED') NOT NULL DEFAULT 'LOCKED',
        `data` TEXT NOT NULL,
        `timestamp` TIMESTAMP,
        PRIMARY KEY (`form_id`)
        )
    COMMENT='The definition table for forms created by ContactForm'
    ENGINE=InnoDB
    AUTO_INCREMENT=1
    DEFAULT CHARSET=utf8
    COLLATE='utf8_general_ci'
EOD;
        try {
            $this->app['db']->query($SQL);
            $this->app['monolog']->addInfo('Created table '.$table, array(__METHOD__, __LINE__));
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

    /**
     * Drop the table
     */
    public function dropTable()
    {
        $this->app['db.utils']->dropTable(self::$table_name);
    }

    /**
     * Get the values for the form status
     *
     * @return Ambigous <boolean, array>
     */
    public function getStatusTypes()
    {
        return $this->app['db.utils']->getEnumValues(self::$table_name, 'form_status');
    }

    /**
     * Insert a new definition record
     *
     * @param array $data
     * @throws \Exception
     * @return integer form_id
     */
    public function insert($data)
    {
        try {
            $insert = array();
            foreach ($data as $key => $value) {
                $insert[$key] = (is_string($value)) ? $this->app['utils']->sanitizeText($value) : $value;
            }
            if (isset($insert['form_id'])) {
                unset($insert['form_id']);
            }
            $this->app['db']->insert(self::$table_name, $insert);
            return $this->app['db']->lastInsertId();
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

    /**
     * Update an existing definition record
     *
     * @param integer $form_id
     * @param array $data
     * @throws \Exception
     */
    public function update($form_id, $data)
    {
        try {
            $check = array('form_id', 'timestamp');
            foreach ($check as $key) {
                if (isset($data[$key])) {
                    unset($data[$key]);
                }
            }
            $update = array();
            foreach ($data as $key => $value) {
                if (is_null($value)) {
                    continue;
                }
                $update[$key] = is_string($value) ? $this->app['utils']->sanitizeText($value) : $value;
            }
            if (!empty($update)) {
                $this->app['db']->update(self::$table_name, $update, array('form_id' => $form_id));
            }
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }
}
