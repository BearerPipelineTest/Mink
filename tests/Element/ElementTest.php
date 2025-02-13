<?php

namespace Behat\Mink\Tests\Element;

use Behat\Mink\Driver\DriverInterface;
use Behat\Mink\Session;
use Behat\Mink\Selector\SelectorsHandler;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

abstract class ElementTest extends TestCase
{
    /**
     * Session.
     *
     * @var Session
     */
    protected $session;

    /**
     * @var DriverInterface&MockObject
     */
    protected $driver;

    /**
     * Selectors.
     *
     * @var SelectorsHandler&MockObject
     */
    protected $selectors;

    /**
     * @before
     */
    protected function prepareSession()
    {
        $this->driver = $this->getMockBuilder('Behat\Mink\Driver\DriverInterface')->getMock();
        $this->driver
            ->expects($this->once())
            ->method('setSession');

        $this->selectors = $this->getMockBuilder('Behat\Mink\Selector\SelectorsHandler')->getMock();
        $this->session = new Session($this->driver, $this->selectors);
    }

    protected function mockNamedFinder($xpath, array $results, $locator, $times = 2)
    {
        if (!is_array($results[0])) {
            $results = array($results, array());
        }

        // In case of empty results, a second call will be done using the partial selector
        $processedResults = array();
        foreach ($results as $result) {
            $processedResults[] = $result;
            if (empty($result)) {
                $processedResults[] = $result;
                ++$times;
            }
        }

        $returnValue = call_user_func_array(array($this, 'onConsecutiveCalls'), $processedResults);

        $this->driver
            ->expects($this->exactly($times))
            ->method('find')
            ->with('//html'.$xpath)
            ->will($returnValue);

        $this->selectors
            ->expects($this->exactly($times))
            ->method('selectorToXpath')
            ->with($this->logicalOr('named_exact', 'named_partial'), $locator)
            ->will($this->returnValue($xpath));
    }
}
