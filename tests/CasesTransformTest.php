<?php

use MX\Misc\CasesTransform;

ini_set('error_reporting', 2147483647);
ini_set('display_errors', '1');

class CasesTransformTest extends PHPUnit_Framework_TestCase
{
    public function testNormalToUpperCamelCase()
    {
        $str1 = 'Hello I am a nice guy';
        $str2 = CasesTransform::from($str1)->toCamelCase();

        $this->assertEquals('HelloIAmANiceGuy', $str2);
    }

    public function testNormalToLowerCamelCase()
    {
        $str1 = 'Hello I am a nice guy';
        $str2 = CasesTransform::from($str1)->toCamelCase(true);

        $this->assertEquals('helloIAmANiceGuy', $str2);
    }

    public function testDashToUpperCamelCase()
    {
        $str1 = 'Hello-i-aM-a-nice-guy';
        $str2 = CasesTransform::from($str1)->toCamelCase();

        $this->assertEquals('HelloIAMANiceGuy', $str2);
    }

    public function testDashToLowerCamelCase()
    {
        $str1 = 'Hello-i-aM-a-nice-guy';
        $str2 = CasesTransform::from($str1)->toCamelCase(true);

        $this->assertEquals('helloIAMANiceGuy', $str2);
    }

    public function testNormalToDashCaseNormal()
    {
        $str1 = 'Hello I am a nice guy';
        $str2 = CasesTransform::from($str1)->toDashCase();

        $this->assertEquals('Hello-I-am-a-nice-guy', $str2);
    }

    public function testNormalToDashCaseUcfirst()
    {
        $str1 = 'Hello I am a nice guy';
        $str2 = CasesTransform::from($str1)->toDashCase(true);

        $this->assertEquals('Hello-I-Am-A-Nice-Guy', $str2);
    }

    public function testUpperCamelCaseToDashCaseNormal()
    {
        $str1 = 'helloIAmANiceGuy';
        $str2 = CasesTransform::from($str1)->toDashCase();

        $this->assertEquals('hello-I-Am-A-Nice-Guy', $str2);
    }

    public function testUpperCamelCaseToDashCaseUcfirst()
    {
        $str1 = 'helloIAmANiceGuy';
        $str2 = CasesTransform::from($str1)->toDashCase(true);

        $this->assertEquals('Hello-I-Am-A-Nice-Guy', $str2);
    }
}