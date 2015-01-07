<?php
namespace werx\Messages\Tests;

use werx\Messages\Decorators;
use werx\Messages\Messages;
use Symfony\Component\HttpFoundation\Session as Session;

class MessagesTests extends \PHPUnit_Framework_TestCase
{
	public $session = null;
	
	public function __construct()
	{
		$this->session = new Session\Session(new Session\Storage\MockArraySessionStorage);
	}

	public function setup()
	{
		//$messages = new Messages($this->session);
		Messages::getInstance($this->session);
	}

	public function teardown()
	{
		Messages::destroy();
	}

	public function testNullSessionInterfaceShouldUseDefault()
	{
		Messages::destroy();
		$session = Messages::getInstance()->session;
		$this->assertInstanceOf('\Symfony\Component\HttpFoundation\Session\Session', $session);
	}

	public function testCanAddError()
	{
		Messages::error('Foo');

		$all = Messages::all();

		$this->assertArrayHasKey('error', $all);
		$this->assertEquals('Foo', $all['error'][0]);
	}

	public function testCanAddInfo()
	{
		Messages::info('Foo');

		$all = Messages::all();

		$this->assertArrayHasKey('info', $all);
		$this->assertEquals('Foo', $all['info'][0]);
	}

	public function testCanAddSuccess()
	{
		Messages::success('Foo');

		$all = Messages::all();

		$this->assertArrayHasKey('success', $all);
		$this->assertEquals('Foo', $all['success'][0]);
	}

	public function testNoMessagesReturnsEmptyArray()
	{
		$all = Messages::all();

		$this->assertInternalType('array', $all);
		$this->assertCount(0, $all);
	}

	/**
	 * @expectedException \Exception
	 * @expectedExceptionMessage Messages not initialized
	 */
	public function testUnitializedObjectShouldThrowException()
	{
		Messages::destroy();

		$all = Messages::all();
	}

	public function testCanFormatArray()
	{
		$string = 'The %s brown %s jumped over the log';
		$format = ['quick', 'fox'];

		$this->assertEquals('The quick brown fox jumped over the log', Messages::format($string, $format));
	}

	public function testCanFormatString()
	{
		$string = 'The quick brown %s jumped over the log';
		$format = 'fox';

		$this->assertEquals('The quick brown fox jumped over the log', Messages::format($string, $format));
	}

	public function testFormatEmptyPlaceholderShouldReturnOriginalString()
	{
		$string = 'The quick brown fox jumped over the log';

		$this->assertEquals($string, Messages::format($string));
	}

	public function testDisplayNoMessagesShouldReturnEmptyString()
	{
		$result = Messages::display();

		$this->assertEquals('', $result);
	}

	public function testDisplayNoMessagesReturnsNull()
	{
		$result = Messages::display();

		$this->assertNull($result);
	}

	public function testSetNullDecoratorDefaultsToSimpleList()
	{
		Messages::setDecorator();

		$decorator = Messages::getInstance()->decorator;

		$this->assertInstanceOf('\werx\Messages\Decorators\SimpleList', $decorator);
	}

	public function testNoDecoratorSpecifiedDefaultsToSimpleList()
	{
		$decorator = Messages::getInstance()->decorator;

		$this->assertInstanceOf('\werx\Messages\Decorators\SimpleList', $decorator);
	}

	public function testSetBootstrapDecoratorReturnsCorrectClass()
	{
		Messages::setDecorator(new Decorators\Bootstrap());

		$decorator = Messages::getInstance()->decorator;

		$this->assertInstanceOf('\werx\Messages\Decorators\Bootstrap', $decorator);
	}

	public function testSetSimpleListDecoratorReturnsCorrectClass()
	{
		Messages::setDecorator(new Decorators\SimpleList);

		$decorator = Messages::getInstance()->decorator;

		$this->assertInstanceOf('\werx\Messages\Decorators\SimpleList', $decorator);
	}

	public function testDisplayErrorsReturnsExpectedString()
	{
		Messages::error('Message One');
		$result = Messages::display();

		$this->assertRegExp('/\<ul class="error">/', $result);
		$this->assertRegExp('/\<li>Message One\<\/li\>/', $result);
	}

	public function testDisplayInfoReturnsExpectedString()
	{
		Messages::info('Message One');
		$result = Messages::display();

		$this->assertRegExp('/\<ul class="info">/', $result);
		$this->assertRegExp('/\<li>Message One\<\/li\>/', $result);
	}

	public function testDisplaySuccessReturnsExpectedString()
	{
		Messages::success('Message One');
		$result = Messages::display();

		$this->assertRegExp('/\<ul class="success">/', $result);
		$this->assertRegExp('/\<li>Message One\<\/li\>/', $result);
	}
}