<?php


namespace Nextend\SmartSlider3\Install;


use Nextend\Framework\Database\Database;
use Nextend\Framework\Notification\Notification;

class Tables {

    protected $tables = array(
        'nextend2_image_storage'             => "(
                `id`    INT(11)     NOT NULL AUTO_INCREMENT,
                `hash`  VARCHAR(32) NOT NULL,
                `image` TEXT        NOT NULL,
                `value` MEDIUMTEXT  NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `hash` (`hash`)
            )",
        'nextend2_section_storage'           => "(
                `id`           INT(11)     NOT NULL AUTO_INCREMENT,
                `application`  VARCHAR(20) NOT NULL,
                `section`      VARCHAR(128) NOT NULL,
                `referencekey` VARCHAR(128) DEFAULT '',
                `value`        MEDIUMTEXT  NOT NULL,
                `isSystem`       INT(11)     NOT NULL DEFAULT '0',
                `editable`     INT(11)     NOT NULL DEFAULT '1',
                PRIMARY KEY (`id`),
                KEY `application` (`application`, `section`(50), `referencekey`(50)),
                KEY `application_2` (`application`, `section`(50)),
                INDEX (`isSystem`),
                INDEX (`editable`)
            )
            AUTO_INCREMENT = 10000",
        'nextend2_smartslider3_generators'   => "(
                `id`     INT(11)      NOT NULL AUTO_INCREMENT,
                `group`  VARCHAR(254) NOT NULL,
                `type`   VARCHAR(254) NOT NULL,
                `params` TEXT         NOT NULL,
                PRIMARY KEY (`id`)
            )",
        'nextend2_smartslider3_sliders'      => "(
          `id`     INT(11)      NOT NULL AUTO_INCREMENT,
          `alias`  TEXT         NULL DEFAULT NULL,
          `title`  TEXT         NOT NULL,
          `type`   VARCHAR(30)  NOT NULL,
          `params` MEDIUMTEXT   NOT NULL,
          `slider_status` VARCHAR(50) NOT NULL DEFAULT 'published',
          `time`   DATETIME     NOT NULL,
          `thumbnail` TEXT      NOT NULL,
          `ordering` INT NOT NULL DEFAULT '0',
          INDEX (`slider_status`),
          INDEX (`time`),
          PRIMARY KEY (`id`)
        )",
        'nextend2_smartslider3_sliders_xref' => "(
          `group_id` int(11) NOT NULL,
          `slider_id` int(11) NOT NULL,
          `ordering` int(11) NOT NULL DEFAULT '0',
          PRIMARY KEY (`group_id`,`slider_id`),
          INDEX (`ordering`)
        )",
        'nextend2_smartslider3_slides'       => "(
          `id`           INT(11)      NOT NULL AUTO_INCREMENT,
          `title`        TEXT         NOT NULL,
          `slider`       INT(11)      NOT NULL,
          `publish_up`   DATETIME     NOT NULL default '1970-01-01 00:00:00',
          `publish_down` DATETIME     NOT NULL default '1970-01-01 00:00:00',
          `published`    TINYINT(1)   NOT NULL,
          `first`        INT(11)      NOT NULL,
          `slide`        LONGTEXT,
          `description`  TEXT         NOT NULL,
          `thumbnail`    TEXT         NOT NULL,
          `params`       TEXT         NOT NULL,
          `ordering`     INT(11)      NOT NULL,
          `generator_id` INT(11)      NOT NULL,
          PRIMARY KEY (`id`),
          INDEX (`published`),
          INDEX (`publish_up`),
          INDEX (`publish_down`),
          INDEX (`generator_id`),
          KEY `thumbnail` (`thumbnail`(100)),
          INDEX (`ordering`),
          INDEX (`slider`)
        )"
    );


    public function install() {
        foreach ($this->tables as $tableName => $structure) {
            $this->installTable($tableName, $structure);
        }

        self::dropIndex('#__nextend2_section_storage', 'system');

        $this->renameColumn('#__nextend2_section_storage', 'system', 'isSystem', 'INT(11) NOT NULL DEFAULT \'0\'');

        self::dropIndex('#__nextend2_section_storage', 'application');
        self::dropIndex('#__nextend2_section_storage', 'application_2');

        $this->fixColumn('#__nextend2_section_storage', 'section', 'VARCHAR(128)', 'NOT NULL');
        $this->fixColumn('#__nextend2_section_storage', 'referencekey', 'VARCHAR(128)', 'NOT NULL');

        $this->query("ALTER TABLE `#__nextend2_section_storage` ADD INDEX `application` (`application`, `section`(50), `referencekey`(50))");
        $this->query("ALTER TABLE `#__nextend2_section_storage` ADD INDEX `application_2` (`application`, `section`(50))");

        self::fixIndex('#__nextend2_section_storage', 'isSystem');
        self::fixIndex('#__nextend2_section_storage', 'editable');

        $this->fixColumn('#__nextend2_smartslider3_sliders', 'ordering', 'INT', 'NOT NULL DEFAULT \'0\'');

        self::dropIndex('#__nextend2_smartslider3_sliders', 'status');

        $this->renameColumn('#__nextend2_smartslider3_sliders', 'status', 'slider_status', 'VARCHAR(50) NOT NULL DEFAULT \'published\'');

        $this->fixColumn('#__nextend2_smartslider3_sliders', 'title', 'TEXT', 'NOT NULL');
        $this->fixColumn('#__nextend2_smartslider3_sliders', 'alias', 'TEXT', 'NULL DEFAULT NULL');
        $this->fixColumn('#__nextend2_smartslider3_sliders', 'thumbnail', 'TEXT', 'NOT NULL');

        $this->fixColumn('#__nextend2_smartslider3_slides', 'title', 'TEXT');
        $this->fixColumn('#__nextend2_smartslider3_slides', 'thumbnail', 'TEXT');

        $this->fixColumn('#__nextend2_smartslider3_slides', 'publish_up', 'DATETIME', 'NOT NULL DEFAULT \'1970-01-01 00:00:00\'');
        $this->fixColumn('#__nextend2_smartslider3_slides', 'publish_down', 'DATETIME', 'NOT NULL DEFAULT \'1970-01-01 00:00:00\'');

        self::fixIndex('#__nextend2_smartslider3_sliders', 'slider_status');
        self::fixIndex('#__nextend2_smartslider3_sliders', 'time');

        self::fixIndex('#__nextend2_smartslider3_sliders_xref', 'ordering');

        $this->query("DELETE FROM `#__nextend2_section_storage` WHERE `application` LIKE 'smartslider' AND `section` LIKE 'sliderChanged'");


        self::fixIndex('#__nextend2_smartslider3_slides', 'published');
        self::fixIndex('#__nextend2_smartslider3_slides', 'publish_up');
        self::fixIndex('#__nextend2_smartslider3_slides', 'publish_down');
        self::fixIndex('#__nextend2_smartslider3_slides', 'generator_id');


        self::dropIndex('#__nextend2_smartslider3_slides', 'thumbnail');
        self::fixIndex('#__nextend2_smartslider3_slides', 'thumbnail', 100);

        self::fixIndex('#__nextend2_smartslider3_slides', 'ordering');
        self::fixIndex('#__nextend2_smartslider3_slides', 'slider');

        if (Notification::hasErrors()) {
            Notification::displayPlainErrors();
            exit;
        }
    }

    private function installTable($tableName, $structure) {
        $query = 'CREATE TABLE IF NOT EXISTS `' . Database::getPrefix() . $tableName . '` ';

        $query .= $structure;
        $query .= ' ' . Database::getCharsetCollate();

        $this->query($query);
    }

    private function query($query) {

        Database::query(Database::parsePrefix($query));
    }

    private function hasColumn($table, $col) {
        return !!Database::queryRow(Database::parsePrefix("SHOW COLUMNS FROM `" . $table . "` LIKE '" . $col . "'"));
    }

    public static function repair() {

        self::fixPrimaryKey('#__nextend2_section_storage', 'id', true);

        self::fixPrimaryKey('#__nextend2_image_storage', 'id', true);

        self::fixPrimaryKey('#__nextend2_smartslider3_generators', 'id', true);

        self::fixPrimaryKey('#__nextend2_smartslider3_sliders', 'id', true);

        self::fixPrimaryKey('#__nextend2_smartslider3_slides', 'id', true);

        self::fixPrimaryKey('#__nextend2_smartslider3_sliders_xref', array(
            'slider_id',
            'group_id'
        ));
    }


    public function reindexOrders() {
        $query   = "SELECT
            sliders.*
        FROM
            `#__nextend2_smartslider3_sliders` AS sliders
        LEFT JOIN `#__nextend2_smartslider3_sliders_xref` AS xref
        ON
            xref.slider_id = sliders.id
        WHERE
            (
                xref.group_id IS NULL OR xref.group_id = 0
            )
        ORDER BY ordering";
        $sliders = Database::queryAll(Database::parsePrefix($query));
        foreach ($sliders as $idx => $slider) {
            $this->query("UPDATE `#__nextend2_smartslider3_sliders` SET `ordering` = '" . $idx . "'  WHERE `id` = " . $slider['id'] . " ");
        }

    }


    /**
     * @param string       $tableName
     * @param array|string $colNames
     * @param bool         $autoIncrement
     */
    private static function fixPrimaryKey($tableName, $colNames, $autoIncrement = false) {
        if (!is_array($colNames)) {
            $colNames = array($colNames);
        }
        $tableName = Database::parsePrefix($tableName);

        Database::query('DELETE FROM ' . $tableName . ' WHERE ' . $colNames[0] . ' = 0;');
        $hasIndex = Database::queryRow("SHOW INDEXES FROM " . $tableName . " WHERE Key_name = 'PRIMARY'");
        if (!$hasIndex) {
            Database::query('ALTER TABLE ' . $tableName . ' ADD PRIMARY KEY(' . implode(', ', $colNames) . ');');
        }

        if (count($colNames) == 1 && $autoIncrement) {
            Database::query('ALTER TABLE ' . $tableName . ' MODIFY `' . $colNames[0] . '` INT NOT NULL AUTO_INCREMENT;');
        }
    }

    private static function fixIndex($tableName, $colName, $limit = null) {
        $tableName = Database::parsePrefix($tableName);

        if (!self::hasIndex($tableName, $colName)) {
            Database::query("ALTER TABLE " . $tableName . " ADD INDEX `" . $colName . "` (`" . $colName . "`" . (isset($limit) ? '(' . $limit . ')' : '') . ")");
        }
    }

    private static function dropIndex($tableName, $colName) {
        $tableName = Database::parsePrefix($tableName);

        if (self::hasIndex($tableName, $colName)) {
            Database::query("ALTER TABLE " . $tableName . " DROP INDEX `" . $colName . "`");
        }

    }

    private static function hasIndex($tableName, $colName) {
        return Database::queryRow("SHOW INDEXES FROM " . $tableName . " WHERE Key_name = '" . $colName . "'");
    }

    private static function fixType($tableName, $colName, $type, $default = '') {
        $tableName = Database::parsePrefix($tableName);

        $column = Database::queryRow(Database::parsePrefix("SHOW COLUMNS FROM " . $tableName . " LIKE '" . $colName . "'"));

        if ($column['Type'] != $type) {
            Database::query("ALTER TABLE " . $tableName . " MODIFY `" . $colName . "` " . $type . " " . $default);
        }
    }

    //Create column if doesn't exists. If column exists, fix its type.
    private function fixColumn($tableName, $colName, $type, $default = '') {
        if (!$this->hasColumn($tableName, $colName)) {
            $this->query("ALTER TABLE " . $tableName . " ADD `" . $colName . "` " . $type . " " . $default);

        } else {
            self::fixType($tableName, $colName, $type, $default);
        }
    }

    private function renameColumn($tableName, $colFrom, $colTo, $typeAndDefault) {
        if (!$this->hasColumn($tableName, $colTo)) {
            if ($this->hasColumn($tableName, $colFrom)) {
                $this->query("ALTER TABLE " . $tableName . " CHANGE  `" . $colFrom . "`  `" . $colTo . "` " . $typeAndDefault);

            } else {
                $this->query("ALTER TABLE " . $tableName . " ADD `" . $colTo . "` " . $typeAndDefault);
            }
        }
    }
}