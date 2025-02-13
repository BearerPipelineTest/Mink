<?php

namespace Behat\Mink\Tests;

use Behat\Mink\Driver\DriverInterface;
use Behat\Mink\Session;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SessionTest extends TestCase
{
    /**
     * @var DriverInterface&MockObject
     */
    private $driver;
    private $selectorsHandler;

    /**
     * Session.
     *
     * @var Session
     */
    private $session;

    /**
     * @before
     */
    protected function prepareSession()
    {
        $this->driver = $this->getMockBuilder('Behat\Mink\Driver\DriverInterface')->getMock();
        $this->selectorsHandler = $this->getMockBuilder('Behat\Mink\Selector\SelectorsHandler')->getMock();
        $this->session = new Session($this->driver, $this->selectorsHandler);
    }

    public function testGetDriver()
    {
        $this->assertSame($this->driver, $this->session->getDriver());
    }

    public function testGetPage()
    {
        $this->assertInstanceOf('Behat\Mink\Element\DocumentElement', $this->session->getPage());
    }

    public function testGetSelectorsHandler()
    {
        $this->assertSame($this->selectorsHandler, $this->session->getSelectorsHandler());
    }

    public function testInstantiateWithoutOptionalDeps()
    {
        $session = new Session($this->driver);

        $this->assertInstanceOf('Behat\Mink\Selector\SelectorsHandler', $session->getSelectorsHandler());
    }

    public function testIsStarted()
    {
        $this->driver->expects($this->once())
            ->method('isStarted')
            ->will($this->returnValue(true));

        $this->assertTrue($this->session->isStarted());
    }

    public function testStart()
    {
        $this->driver->expects($this->once())
            ->method('start');

        $this->session->start();
    }

    public function testStop()
    {
        $this->driver->expects($this->once())
            ->method('stop');

        $this->session->stop();
    }

    public function testRestart()
    {
        $this->driver->expects($this->once())
            ->method('stop');
        $this->driver->expects($this->once())
            ->method('start');

        $this->session->restart();
    }

    public function testVisitWithoutRunningSession()
    {
        $this->driver
            ->expects($this->once())
            ->method('isStarted')
            ->willReturn(false);

        $this->driver
            ->expects($this->once())
            ->method('start');

        $this->driver
            ->expects($this->once())
            ->method('visit')
            ->with($url = 'some_url');

        $this->session->visit($url);
    }

    public function testVisitWithRunningSession()
    {
        $this->driver
            ->expects($this->once())
            ->method('isStarted')
            ->willReturn(true);

        $this->driver
            ->expects($this->never())
            ->method('start');

        $this->driver
            ->expects($this->once())
            ->method('visit')
            ->with($url = 'some_url');

        $this->session->visit($url);
    }

    public function testReset()
    {
        $this->driver
            ->expects($this->once())
            ->method('reset');

        $this->session->reset();
    }

    public function testSetBasicAuth()
    {
        $this->driver->expects($this->once())
            ->method('setBasicAuth')
            ->with('user', 'pass');

        $this->session->setBasicAuth('user', 'pass');
    }

    public function testSetRequestHeader()
    {
        $this->driver->expects($this->once())
            ->method('setRequestHeader')
            ->with('name', 'value');

        $this->session->setRequestHeader('name', 'value');
    }

    public function testGetResponseHeaders()
    {
        $this->driver
            ->expects($this->once())
            ->method('getResponseHeaders')
            ->will($this->returnValue($ret = array(2, 3, 4)));

        $this->assertEquals($ret, $this->session->getResponseHeaders());
    }

    /**
     * @dataProvider provideResponseHeader
     */
    public function testGetResponseHeader($expected, $name, array $headers)
    {
        $this->driver->expects($this->once())
            ->method('getResponseHeaders')
            ->willReturn($headers);

        $this->assertSame($expected, $this->session->getResponseHeader($name));
    }

    public function provideResponseHeader()
    {
        return array(
            array('test', 'Mink', array('Mink' => 'test')),
            array('test', 'mink', array('Mink' => 'test')),
            array('test', 'Mink', array('mink' => 'test')),
            array('test', 'Mink', array('Mink' => array('test', 'test2'))),
            array(null, 'other', array('Mink' => 'test')),
        );
    }

    public function testSetCookie()
    {
        $this->driver->expects($this->once())
            ->method('setCookie')
            ->with('name', 'value');

        $this->session->setCookie('name', 'value');
    }

    public function testGetCookie()
    {
        $this->driver->expects($this->once())
            ->method('getCookie')
            ->with('name')
            ->will($this->returnValue('value'));

        $this->assertEquals('value', $this->session->getCookie('name'));
    }

    public function testGetStatusCode()
    {
        $this->driver
            ->expects($this->once())
            ->method('getStatusCode')
            ->will($this->returnValue($ret = 404));

        $this->assertEquals($ret, $this->session->getStatusCode());
    }

    public function testGetCurrentUrl()
    {
        $this->driver
            ->expects($this->once())
            ->method('getCurrentUrl')
            ->will($this->returnValue($ret = 'http://some.url'));

        $this->assertEquals($ret, $this->session->getCurrentUrl());
    }

    public function testGetScreenshot()
    {
        $this->driver->expects($this->once())
            ->method('getScreenshot')
            ->will($this->returnValue('screenshot'));

        $this->assertEquals('screenshot', $this->session->getScreenshot());
    }

    public function testGetWindowNames()
    {
        $this->driver->expects($this->once())
            ->method('getWindowNames')
            ->will($this->returnValue($names = array('window 1', 'window 2')));

        $this->assertEquals($names, $this->session->getWindowNames());
    }

    public function testGetWindowName()
    {
        $this->driver->expects($this->once())
            ->method('getWindowName')
            ->will($this->returnValue('name'));

        $this->assertEquals('name', $this->session->getWindowName());
    }

    public function testReload()
    {
        $this->driver->expects($this->once())
            ->method('reload');

        $this->session->reload();
    }

    public function testBack()
    {
        $this->driver->expects($this->once())
            ->method('back');

        $this->session->back();
    }

    public function testForward()
    {
        $this->driver->expects($this->once())
            ->method('forward');

        $this->session->forward();
    }

    public function testSwitchToWindow()
    {
        $this->driver->expects($this->once())
            ->method('switchToWindow')
            ->with('test');

        $this->session->switchToWindow('test');
    }

    public function testSwitchToIFrame()
    {
        $this->driver->expects($this->once())
            ->method('switchToIFrame')
            ->with('test');

        $this->session->switchToIFrame('test');
    }

    public function testExecuteScript()
    {
        $this->driver
            ->expects($this->once())
            ->method('executeScript')
            ->with($arg = 'JS');

        $this->session->executeScript($arg);
    }

    public function testEvaluateScript()
    {
        $this->driver
            ->expects($this->once())
            ->method('evaluateScript')
            ->with($arg = 'JS func')
            ->will($this->returnValue($ret = '23'));

        $this->assertEquals($ret, $this->session->evaluateScript($arg));
    }

    public function testWait()
    {
        $this->driver
            ->expects($this->once())
            ->method('wait')
            ->with(1000, 'function () {}');

        $this->session->wait(1000, 'function () {}');
    }

    public function testResizeWindow()
    {
        $this->driver->expects($this->once())
            ->method('resizeWindow')
            ->with(800, 600, 'test');

        $this->session->resizeWindow(800, 600, 'test');
    }

    public function testMaximizeWindow()
    {
        $this->driver->expects($this->once())
            ->method('maximizeWindow')
            ->with('test');

        $this->session->maximizeWindow('test');
    }
}
