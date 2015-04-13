<?php
/**
 * This file is part of bovigo\callmap.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  bovigo_callmap
 */
namespace bovigo\callmap;
/**
 * Helper class for the test.
 */
class OneMoreSelfDefined
{
    public function getName()
    {
        return 'bar';
    }
}
/**
 * Tests for call mapping with a list of return values.
 */
class InvocationResultsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @type  bovigo\callmap\Proxy
     */
    private $proxy;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->proxy = NewInstance::of('\ReflectionObject', [$this]);
    }

    /**
     * @test
     */
    public function mapToInvocationResultsReturnsResultOnMethodCall()
    {
        $this->proxy->mapCalls(
            ['getName' => onConsecutiveCalls('foo', 'bar', 'baz')]
        );
        foreach (['foo', 'bar', 'baz'] as $expected) {
            assertEquals($expected, $this->proxy->getName());
        }
    }

    /**
     * @test
     */
    public function invocationResultIsResultOfOriginalMethodIfCalledMoreOftenThenResultsDefined()
    {
        $this->proxy->mapCalls(['getName' => onConsecutiveCalls('foo')]);
        $this->proxy->getName(); // foo
        assertEquals(__CLASS__, $this->proxy->getName());  // falls back to original method
    }

    /**
     * @test
     * @since  0.6.0
     */
    public function invocationResultIsNullForStubIfCalledMoreOftenThenResultsDefined()
    {
        $proxy = NewInstance::stub('bovigo\callmap\OneMoreSelfDefined');
        $proxy->mapCalls(['getName' => onConsecutiveCalls('foo')]);
        $proxy->getName(); // foo
        assertNull($proxy->getName());
    }

    /**
     * @test
     * @since  0.6.0
     */
    public function invocationResultIsNullForInterfaceIfCalledMoreOftenThenResultsDefined()
    {
        $proxy = NewInstance::stub('\Countable');
        $proxy->mapCalls(['count' => onConsecutiveCalls(303)]);
        $proxy->count(); // 303
        assertNull($proxy->count());
    }
}
