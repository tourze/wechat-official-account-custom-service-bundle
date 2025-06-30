<?php

namespace WechatOfficialAccountCustomServiceBundle\Tests\Unit\Request;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use WechatOfficialAccountCustomServiceBundle\Request\UploadKfAccountHeadimgRequest;

class UploadKfAccountHeadimgRequestTest extends TestCase
{
    public function testGetRequestPath(): void
    {
        $request = new UploadKfAccountHeadimgRequest();
        
        $this->assertEquals('https://api.weixin.qq.com/customservice/kfaccount/uploadheadimg', $request->getRequestPath());
    }

    public function testKfAccountGetterAndSetter(): void
    {
        $request = new UploadKfAccountHeadimgRequest();
        $request->setKfAccount('test@account');
        
        $this->assertEquals('test@account', $request->getKfAccount());
    }

    public function testFileGetterAndSetter(): void
    {
        $request = new UploadKfAccountHeadimgRequest();
        $file = $this->createMock(UploadedFile::class);
        $request->setFile($file);
        
        $this->assertSame($file, $request->getFile());
    }

    public function testGetRequestOptions(): void
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'test');
        file_put_contents($tempFile, 'test content');
        
        $request = new UploadKfAccountHeadimgRequest();
        $request->setKfAccount('test@account');
        
        $file = new UploadedFile($tempFile, 'test.jpg', null, null, true);
        $request->setFile($file);
        
        $options = $request->getRequestOptions();
        
        $this->assertIsArray($options);
        $this->assertArrayHasKey('multipart', $options);
        $this->assertIsArray($options['multipart']);
        $this->assertCount(2, $options['multipart']);
        
        unlink($tempFile);
    }
}