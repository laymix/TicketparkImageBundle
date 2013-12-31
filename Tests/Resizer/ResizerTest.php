<?php

namespace Ticketpark\Bundle\ApiBundle\Tests\Image;

use Ticketpark\ImageBundle\Resizer\Resizer;

class ResizerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->testImagePath  =  __DIR__.'/../../Test/Files/';
        $this->getInstance();
    }

    public function getInstance($fileInCache=false)
    {
        $this->imageResizer = new Resizer(
            $this->getFileHandlerMock($fileInCache)
        );
    }

    public function testResizeImage()
    {
        $this->assertEquals($this->testImagePath.'newlyCachedFile', $this->imageResizer->resize($this->testImagePath.'testimage-100.jpg', 'filter', 100));
    	$this->assertEquals(exif_imagetype($this->testImagePath.'newlyCachedFile'), 2);
    	unlink($this->testImagePath.'newlyCachedFile');
    }

    public function testResizeImageWithoutExtension()
    {
        $this->assertEquals($this->testImagePath.'newlyCachedFile', $this->imageResizer->resize($this->testImagePath.'jpgimagewithoutextension', 'filter', 100));
        $this->assertFalse(file_exists($this->testImagePath.'jpgimagewithoutextension.jpeg'));
        $this->assertEquals(exif_imagetype($this->testImagePath.'newlyCachedFile'), 2);
    	unlink($this->testImagePath.'newlyCachedFile');
    }

    public function testResizeImageInCache()
    {
        $this->getInstance(true);
        $this->assertEquals('oldFileFromCache', $this->imageResizer->resize('foo', 'filter', 100));
    }

    public function testResizeImageFromUrl()
    {
        $this->getInstance(false);
        $this->assertEquals($this->testImagePath.'newlyCachedFile', $this->imageResizer->resize('http://www.foo.com/bar.jpg', 'filter', 100));
        $this->assertEquals(exif_imagetype($this->testImagePath.'newlyCachedFile'), 2);
    	unlink($this->testImagePath.'newlyCachedFile');
    }

    public function testResizeImageFromUrlInCache()
    {
        $this->getInstance(true);
        $this->assertEquals('oldFileFromCache', $this->imageResizer->resize('http://www.foo.com/bar.jpg', 'filter', 100));
    }

    public function testResizeMmImage()
    {
        $this->assertEquals($this->testImagePath.'newlyCachedFile', $this->imageResizer->resizeMm($this->testImagePath.'testimage-100.jpg', 'filter', 100, 150));
        $this->assertEquals(exif_imagetype($this->testImagePath.'newlyCachedFile'), 2);
    	unlink($this->testImagePath.'newlyCachedFile');
    }

    /**
     * @expectedException Ticketpark\ImageBundle\Exception\NoImageException
     */
    public function testResizeNonImage()
    {
        $this->imageResizer->resize($this->testImagePath.'nonimage.txt', 'filter', 100);
    }

    public function getFileHandlerMock($fileInCache=false)
    {
        $fileHandler = $this->getMockBuilder('Ticketpark\FileBundle\FileHandler\FileHandler')
            ->disableOriginalConstructor()
            ->setMethods(array('fromCache', 'cache', 'get'))
            ->getMock();

        $fileHandler->expects($this->any())
            ->method('fromCache')
            ->will($this->returnValue(call_user_func(array($this, 'fromCache'), $fileInCache)));

        $fileHandler->expects($this->any())
            ->method('cache')
            ->will($this->returnValue($this->testImagePath.'newlyCachedFile'));

        $fileHandler->expects($this->any())
            ->method('get')
            ->will($this->returnCallback(array($this, 'getFile')));

         return $fileHandler;
    }

    public function getFile()
    {
        $args = func_get_args();

        if (false !== strpos($args[0], 'http')) {

            return $this->testImagePath.'testimage-100.jpg';
        }

        return $args[0];
    }

    public function fromCache($fileInCache)
    {
        if ($fileInCache) {
            return 'oldFileFromCache';
        }

        return false;
    }
}
