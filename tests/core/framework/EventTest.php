<?php

    namespace pachno\core\framework;

    if (!class_exists('\\pachno\\core\\framework\\Context')) require PACHNO_CORE_PATH . 'framework/Context.php';
    if (!class_exists('\\pachno\\core\\framework\\Event')) require PACHNO_CORE_PATH . 'framework/Event.php';
    if (!class_exists('\\pachno\\core\\framework\\Logging')) require PACHNO_CORE_PATH . 'framework/Logging.php';

    class EventTest extends \PHPUnit_Framework_TestCase
    {

        /**
         * @covers \pachno\core\framework\Event::__construct
         * @covers \pachno\core\framework\Event::createNew
         */
        public function testCreateNew()
        {
            $event = \pachno\core\framework\Event::createNew('modulename', 'identifier', 'subject', array('param1' => 1, 'param2' => 2), array('listitem1', 'listitem2'));

            $this->assertInstanceOf('\pachno\core\framework\Event', $event);

            return $event;
        }

        /**
         * @covers \pachno\core\framework\Event::getIdentifier
         * @depends testCreateNew
         */
        public function testGetIdentifier(\pachno\core\framework\Event $event)
        {
            $this->assertEquals('identifier', $event->getIdentifier());
        }

        /**
         * @covers \pachno\core\framework\Event::getModule
         * @depends testCreateNew
         */
        public function testGetModule(\pachno\core\framework\Event $event)
        {
            $this->assertEquals('modulename', $event->getModule());
        }

        /**
         * @covers \pachno\core\framework\Event::getSubject
         * @depends testCreateNew
         */
        public function testGetSubject(\pachno\core\framework\Event $event)
        {
            $this->assertEquals('subject', $event->getSubject());
        }

        /**
         * @covers \pachno\core\framework\Event::getParameters
         * @covers \pachno\core\framework\Event::getParameter
         * @depends testCreateNew
         */
        public function testParameters(\pachno\core\framework\Event $event)
        {
            $this->assertArrayHasKey('param1', $event->getParameters());
            $this->assertEquals(1, $event->getParameter('param1'));
            $this->assertArrayHasKey('param2', $event->getParameters());
            $this->assertEquals(2, $event->getParameter('param2'));
        }

        /**
         * @covers \pachno\core\framework\Event::getReturnList
         * @covers \pachno\core\framework\Event::addToReturnList
         * @covers \pachno\core\framework\Event::setReturnValue
         * @covers \pachno\core\framework\Event::getReturnValue
         * @depends testCreateNew
         */
        public function testReturnListAndReturnValue(\pachno\core\framework\Event $event)
        {
            $this->assertArrayHasKey(0, $event->getReturnList());
            $this->assertContains('listitem1', $event->getReturnList());
            $this->assertArrayHasKey(1, $event->getReturnList());
            $this->assertContains('listitem2', $event->getReturnList());

            $event->addToReturnList('listitem3');
            $this->assertContains('listitem3', $event->getReturnList());

            $event->setReturnValue('fubar');
            $this->assertEquals('fubar', $event->getReturnValue());

            $event->setReturnValue(null);
            $this->assertEquals(null, $event->getReturnValue());
        }

        /**
         * @covers \pachno\core\framework\Event::setProcessed
         * @covers \pachno\core\framework\Event::isProcessed
         * @depends testCreateNew
         */
        public function testProcessEvent(\pachno\core\framework\Event $event)
        {
            $event->setProcessed(true);
            $this->assertTrue($event->isProcessed());
            $event->setProcessed(false);
            $this->assertFalse($event->isProcessed());
        }

        public function listenerCallback(\pachno\core\framework\Event $event)
        {
            $this->wastriggered = true;
            return true;
        }

        public function listenerCallbackNonProcessingFirst(\pachno\core\framework\Event $event)
        {
            $this->wasprocessed[] = 1;
            return true;
        }

        public function listenerCallbackNonProcessingSecond(\pachno\core\framework\Event $event)
        {
            $this->wasprocessed[] = 2;
            $event->setProcessed();
            return true;
        }

        public function listenerCallbackProcessing(\pachno\core\framework\Event $event)
        {
            $this->wasprocessed[] = 3;
            return true;
        }

        /**
         * @covers \pachno\core\framework\Event::listen
         * @covers \pachno\core\framework\Event::isAnyoneListening
         * @covers \pachno\core\framework\Event::clearListeners
         * @depends testCreateNew
         */
        public function testListening(\pachno\core\framework\Event $event)
        {
            \pachno\core\framework\Event::listen('modulename', 'identifier', array($this, 'listenerCallback'));
            $this->assertTrue(\pachno\core\framework\Event::isAnyoneListening('modulename', 'identifier'));

            \pachno\core\framework\Event::clearListeners('modulename', 'identifier');
            $this->assertFalse(\pachno\core\framework\Event::isAnyoneListening('modulename', 'identifier'));

            \pachno\core\framework\Event::listen('modulename', 'identifier', array($this, 'listenerCallbackNonProcessingFirst'));
            \pachno\core\framework\Event::listen('modulename', 'identifier', array($this, 'listenerCallbackNonProcessingSecond'));
            \pachno\core\framework\Event::listen('modulename', 'identifier', array($this, 'listenerCallbackProcessing'));
            $this->assertTrue(\pachno\core\framework\Event::isAnyoneListening('modulename', 'identifier'));

            return $event;
        }

        /**
         * @covers \pachno\core\framework\Event::listen
         * @covers \pachno\core\framework\Event::trigger
         * @covers \pachno\core\framework\Event::triggerUntilProcessed
         * @depends testListening
         */
        public function testTriggeringAndProcessing(\pachno\core\framework\Event $event)
        {
            $this->wastriggered = false;
            \pachno\core\framework\Event::clearListeners('modulename', 'identifier');
            \pachno\core\framework\Event::listen('modulename', 'identifier', array($this, 'listenerCallback'));

            $event->trigger();
            $this->assertAttributeEquals(true, 'wastriggered', $this);

            \pachno\core\framework\Event::clearListeners('modulename', 'identifier');
            \pachno\core\framework\Event::listen('modulename', 'identifier', array($this, 'listenerCallbackNonProcessingFirst'));
            \pachno\core\framework\Event::listen('modulename', 'identifier', array($this, 'listenerCallbackNonProcessingSecond'));
            \pachno\core\framework\Event::listen('modulename', 'identifier', array($this, 'listenerCallbackProcessing'));

            $this->wasprocessed = array();
            $event->triggerUntilProcessed();

            $this->assertAttributeNotEmpty('wasprocessed', $this);
            $this->assertAttributeContains(1, 'wasprocessed', $this);
            $this->assertAttributeContains(2, 'wasprocessed', $this);
            $this->assertAttributeNotContains(3, 'wasprocessed', $this);
        }

    }
