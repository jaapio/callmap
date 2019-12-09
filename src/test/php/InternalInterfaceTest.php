<?php
declare(strict_types=1);
/**
 * This file is part of bovigo\callmap.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  bovigo_callmap
 */
namespace bovigo\callmap;
use PHPUnit\Framework\TestCase;

use function bovigo\assert\assertThat;
use function bovigo\assert\predicate\equals;
use function bovigo\assert\predicate\isNull;
/**
 * Applies tests to a PHP internal interface.
 */
class InternalInterfaceTest extends TestCase
{
    /**
     * @var  \bovigo\callmap\ClassProxy
     */
    private $proxy;

    public function setUp(): void
    {
        $this->proxy = NewInstance::of(\Countable::class);
    }

    /**
     * @test
     */
    public function returnsNullIfMethodCallNotMapped(): void
    {
        assertThat($this->proxy->count(), isNull());
    }

    /**
     * @test
     */
    public function mapToSimpleValueReturnsValueOnMethodCall(): void
    {
        $this->proxy->returns(['count' => 3]);
        assertThat($this->proxy->count(), equals(3));
    }

    /**
     * @test
     */
    public function mapToClosureReturnsClosureReturnValueOnMethodCall(): void
    {
        $this->proxy->returns(['count' => function() { return 42; }]);
        assertThat($this->proxy->count(), equals(42));
    }

    /**
     * @test
     * @doesNotPerformAssertions
     */
    public function amountOfCallsToMethodIsZeroIfNotCalled(): void
    {
        verify($this->proxy, 'count')->wasNeverCalled();
    }

    /**
     * @test
     * @doesNotPerformAssertions
     */
    public function recordsAmountOfCallsToMethod(): void
    {
        $this->proxy->count();
        $this->proxy->count();
        verify($this->proxy, 'count')->wasCalled(2);
    }

    /**
     * @test
     * @doesNotPerformAssertions
     */
    public function canVerifyThatMethodDidNotReveiveArguments(): void
    {
        $this->proxy->count();
        verify($this->proxy, 'count')->receivedNothing();
    }
}
