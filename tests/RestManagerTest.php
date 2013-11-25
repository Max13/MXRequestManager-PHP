<?php

use MX\RestManager;

ini_set('error_reporting', 2147483647);
ini_set('display_errors', '1');

class RestManagerTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->m_url = 'http://api.virtual-info.info';
        $this->m_path = '/self.json';
        $this->m_params = array(
            'key1'  => 1,
            'key2'  => 'val2',
            'key3'  => array(
                'key3-1'    => 'val3-1',
                'key3-2'    => 'val3-2'
            ),
        );
        $this->m_headers = array(
            'X-Test-Header1'    => 'VALUE1',
            'X-Test-Header2'    => 'VALUE2',
        );
    }

    public function testHeadWithSuccess()
    {
        $mx = new RestManager($this->m_url);

        $mx->head($this->m_path);

        $this->assertEquals(200, $mx->response('headers', 'Code'));
    }

    public function testGetWithSuccess()
    {
        $mx = new RestManager($this->m_url);
        $mx->setHeaders($this->m_headers);

        $res = $mx->get($this->m_path, $this->m_params, true);

        $this->assertEquals(200, $mx->response('headers', 'Code'));
        $this->assertFalse($res === false);
        $this->assertFalse($res === true);
        $this->assertInternalType('array', $res);
        $this->assertEquals('GET', $res['self']['REQUEST_METHOD']);
        $this->assertEquals($this->m_params, $res['query_parameters']['GET']);
        $this->assertEquals($mx->userAgent(), $res['self']['HEADERS']['User-Agent']);
        foreach ($this->m_headers as $key => $val) {
            $this->assertArrayHasKey($key, $res['self']['HEADERS']);
            $this->assertEquals($val, $res['self']['HEADERS'][$key]);
        }
    }

    public function testPostWithSuccess()
    {
        $mx = new RestManager($this->m_url);
        $mx->setHeaders($this->m_headers);

        $res = $mx->post($this->m_path, $this->m_params, true);

        $this->assertEquals(200, $mx->response('headers', 'Code'));
        $this->assertFalse($res === false);
        $this->assertFalse($res === true);
        $this->assertInternalType('array', $res);
        $this->assertEquals('POST', $res['self']['REQUEST_METHOD']);
        $this->assertEquals($this->m_params, $res['query_parameters']['POST']);
        $this->assertEquals($mx->userAgent(), $res['self']['HEADERS']['User-Agent']);
        foreach ($this->m_headers as $key => $val) {
            $this->assertArrayHasKey($key, $res['self']['HEADERS']);
            $this->assertEquals($val, $res['self']['HEADERS'][$key]);
        }
    }

    public function testPostJsonWithSuccess()
    {
        $mx = new RestManager($this->m_url);
        $mx->setHeaders(array_merge($this->m_headers, array('Content-Type' => 'application/json')));

        $res = $mx->post($this->m_path, json_encode($this->m_params), true);

        $this->assertEquals(200, $mx->response('headers', 'Code'));
        $this->assertFalse($res === false);
        $this->assertFalse($res === true);
        $this->assertInternalType('array', $res);
        $this->assertEquals('POST', $res['self']['REQUEST_METHOD']);
        $this->assertEquals($mx->userAgent(), $res['self']['HEADERS']['User-Agent']);
        $this->assertEquals('application/json', $res['self']['CONTENT_TYPE']);
        $this->assertJsonStringEqualsJsonString(json_encode($this->m_params), $res['body']);
        foreach ($this->m_headers as $key => $val) {
            $this->assertArrayHasKey($key, $res['self']['HEADERS']);
            $this->assertEquals($val, $res['self']['HEADERS'][$key]);
        }
    }

    public function testPutWithSuccess()
    {
        $mx = new RestManager($this->m_url);
        $mx->setHeaders($this->m_headers);

        $res = $mx->put($this->m_path, $this->m_params, true);

        $this->assertEquals(200, $mx->response('headers', 'Code'));
        $this->assertFalse($res === false);
        $this->assertFalse($res === true);
        $this->assertInternalType('array', $res);
        $this->assertEquals('PUT', $res['self']['REQUEST_METHOD']);
        $this->assertEquals($this->m_params, $res['query_parameters']['REQUEST']);
        $this->assertEquals($mx->userAgent(), $res['self']['HEADERS']['User-Agent']);
        foreach ($this->m_headers as $key => $val) {
            $this->assertArrayHasKey($key, $res['self']['HEADERS']);
            $this->assertEquals($val, $res['self']['HEADERS'][$key]);
        }
    }

    public function testDeleteWithSuccess()
    {
        $mx = new RestManager($this->m_url);
        $mx->setHeaders($this->m_headers);

        $res = $mx->delete($this->m_path, $this->m_params, true);

        $this->assertEquals(200, $mx->response('headers', 'Code'));
        $this->assertFalse($res === false);
        $this->assertFalse($res === true);
        $this->assertInternalType('array', $res);
        $this->assertEquals('DELETE', $res['self']['REQUEST_METHOD']);
        $this->assertEquals($this->m_params, $res['query_parameters']['REQUEST']);
        $this->assertEquals($mx->userAgent(), $res['self']['HEADERS']['User-Agent']);
        foreach ($this->m_headers as $key => $val) {
            $this->assertArrayHasKey($key, $res['self']['HEADERS']);
            $this->assertEquals($val, $res['self']['HEADERS'][$key]);
        }
    }

    public function testCustomVerbWithSuccess()
    {
        $mx = new RestManager($this->m_url);
        $mx->setHeaders($this->m_headers);
        $verb = 'ACTION';

        $res = $mx->custom($verb, $this->m_path, $this->m_params, true);

        $this->assertEquals(200, $mx->response('headers', 'Code'));
        $this->assertFalse($res === false);
        $this->assertFalse($res === true);
        $this->assertInternalType('array', $res);
        $this->assertEquals($verb, $res['self']['REQUEST_METHOD']);
        $this->assertEquals($this->m_params, $res['query_parameters']['REQUEST']);
        $this->assertEquals($mx->userAgent(), $res['self']['HEADERS']['User-Agent']);
        foreach ($this->m_headers as $key => $val) {
            $this->assertArrayHasKey($key, $res['self']['HEADERS']);
            $this->assertEquals($val, $res['self']['HEADERS'][$key]);
        }
    }
}
