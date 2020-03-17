<?php

declare(strict_types=1);

// tests/Bundle/MessageTest.php
namespace App\Tests\Bundle;

use App\Bundle\Message;
use PHPUnit\Framework\TestCase;

class MessageTest extends TestCase
{
    private $message;

    public function setUp(): void
    {
        $this->message = new Message();
    }

    public function testAddMessageAndGetMessage(): void
    {
        $text1 = 'String 1';
        $text2 = 'String 2';
        $text3 = 'String 3';

        $this->message->addMessage($text1);
        $this->message->addMessage($text2);
        $this->message->addMessage($text3);
        $array = $this->message->getMessage();

        $this->assertIsArray($array);
        $this->assertIsString($array[0]);
        $this->assertIsString($array[1]);
        $this->assertIsString($array[2]);
        $this->assertEquals($text1, $array[0]);
        $this->assertEquals($text2, $array[1]);
        $this->assertEquals($text3, $array[2]);
    }

    public function testSetMessageAndGetStrMessage(): void
    {
        $text = 'String 4';

        $setMessage = MessageTest::getPrivateMethod(
            $this->message,
            'setMessage'
        );
        $setMessage->invoke($this->message, $text);
        $string = $this->message->getStrMessage();

        $this->assertIsString($string);
        $this->assertEquals($text, $string);
    }

    public function testIsMessageAndSetOkAndGetOk(): void
    {
        $this->assertFalse($this->message->isMessage());
        $this->assertFalse($this->message->getOk());

        $this->message->addMessage('String 5');
        $this->message->setOk(true);

        $this->assertTrue($this->message->isMessage());
        $this->assertTrue($this->message->getOk());

        $setMessage = MessageTest::getPrivateMethod(
            $this->message,
            'setMessage'
        );
        $setMessage->invoke($this->message, '');
        $this->message->setOk(false);

        $this->assertFalse($this->message->isMessage());
        $this->assertFalse($this->message->getOk());
    }

    protected static function getPrivateMethod(
        object $object,
        string $name
    ): object {
        $class = new \ReflectionClass($object);
        $method = $class->getMethod($name);
        $method->setAccessible(true);

        return $method;
    }
}
