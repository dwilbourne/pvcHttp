<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace tests\url;

use PHPUnit\Framework\TestCase;
use pvc\url\Url;

class UrlTest extends TestCase {

    protected Url $url;

    function setUp() : void {
        $this->url = new Url();

        $this->fileArrayRemote = array(
            'http://somebadurl.js',
            'https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js',
            'https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js',
            'https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css'
        );
    }

    public function testSetGetScheme() {
        $scheme = 'ftp';
        $this->url->setScheme($scheme);
        self::assertEquals($scheme, $this->url->getScheme());
    }

    public function testSetGetHost() {
        $host = 'google.com';
        $this->url->setHost($host);
        self::assertEquals($host, $this->url->getHost());
    }

    public function testSetGetPort() {
        $port = '443';
        $this->url->setPort($port);
        self::assertEquals($port, $this->url->getPort());
    }

    public function testSetGetUser() {
        $user = 'someuser';
        $this->url->setUser($user);
        self::assertEquals($user, $this->url->getUser());
    }

    public function testSetGetPassword() {
        $password = 'somepassword';
        $this->url->setPassword($password);
        self::assertEquals($password, $this->url->getPassword());
    }

    public function testSetGetPath() {
        $path = "/path/to/some/resource";
        $this->url->setPath($path);
        self::assertEquals($path, $this->url->getPath());
    }

    public function testSetGetQuery() {
        $query = "?axe=1;shovel=2;";
        $this->url->setQuery($query);
        self::assertEquals($query, $this->url->getQuery());
    }

    public function testSetGetFragment() {
        $fragment = "anchor";
        $this->url->setFragment($fragment);
        self::assertEquals($fragment, $this->url->getFragment());
    }

    public function testExists() {
        $remote = 'http://somebadurl';
        $this->url->parseURLString($remote);
        $this->assertFalse($this->url->exists());

        $remote = 'https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js';
        $this->url->parseURLString($remote);
        $this->assertTrue($this->url->exists());
    }


}
