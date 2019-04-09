<?php

    namespace pachno\core\framework;

    if (!class_exists('\\pachno\\core\\framework\\Context')) require PACHNO_CORE_PATH . 'framework/Context.php';
    if (!class_exists('\\pachno\\core\\framework\\Settings')) require PACHNO_CORE_PATH . 'framework/Settings.php';
    if (!class_exists('\\pachno\\core\\framework\\exceptions\\ConfigurationException')) require PACHNO_CORE_PATH . 'framework/exceptions/ConfigurationException.php';

    class ContextTest extends \PHPUnit_Framework_TestCase
    {

        protected static $installed_file_exists = false;

        public static function setUpBeforeClass()
        {
            $installed_file = PACHNO_PATH . 'installed';

            self::$installed_file_exists = file_exists($installed_file);
            rename($installed_file, $installed_file . '.tmp');
        }

        /**
         * @covers \pachno\core\framework\Context::checkInstallMode
         *
         * @expectedException \pachno\core\framework\exceptions\ConfigurationException
         * @expectedExceptionCode \pachno\core\framework\exceptions\ConfigurationException::NO_VERSION_INFO
         */
        public function testInstallModeThrowsExceptionWhenNoVersionInfoPresent()
        {
            $installed_file = PACHNO_PATH . 'installed';

            file_put_contents($installed_file, "");
            \pachno\core\framework\Context::checkInstallMode();
        }

        /**
         * @covers \pachno\core\framework\Context::checkInstallMode
         *
         * @expectedException \pachno\core\framework\exceptions\ConfigurationException
         * @expectedExceptionCode \pachno\core\framework\exceptions\ConfigurationException::UPGRADE_REQUIRED
         */
        public function testInstallModeThrowsExceptionWhenIncorrectVersion()
        {
            $installed_file = PACHNO_PATH . 'installed';

            file_put_contents($installed_file, "1.0, installed today");
            \pachno\core\framework\Context::checkInstallMode();
        }

        /**
         * @covers \pachno\core\framework\Context::isInstallmode
         * @covers \pachno\core\framework\Context::checkInstallMode
         */
        public function testInstallMode()
        {
            $installed_file = PACHNO_PATH . 'installed';

            if (file_exists($installed_file)) unlink($installed_file);
            \pachno\core\framework\Context::checkInstallMode();
            $this->assertTrue(\pachno\core\framework\Context::isInstallmode());

            file_put_contents($installed_file, \pachno\core\framework\Settings::getMajorVer() . "." . \pachno\core\framework\Settings::getMinorVer() . "." . \pachno\core\framework\Settings::getRevision() .", installed today");
            \pachno\core\framework\Context::checkInstallMode();
            $this->assertFalse(\pachno\core\framework\Context::isInstallmode());
        }

        public static function tearDownAfterClass()
        {
            $installed_file = PACHNO_PATH . 'installed';
            
            (self::$installed_file_exists) ? rename($installed_file . '.tmp', $installed_file) : unlink($installed_file);
        }

    }
