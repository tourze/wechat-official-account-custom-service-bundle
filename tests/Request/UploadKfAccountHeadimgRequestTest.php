<?php

namespace WechatOfficialAccountCustomServiceBundle\Tests\Request;

use HttpClientBundle\Tests\Request\RequestTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use WechatOfficialAccountCustomServiceBundle\Request\UploadKfAccountHeadimgRequest;

/**
 * @internal
 */
#[CoversClass(UploadKfAccountHeadimgRequest::class)]
final class UploadKfAccountHeadimgRequestTest extends RequestTestCase
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
        // 使用具体类 UploadedFile 创建 Mock 的原因：
        // 1. UploadedFile 是 Symfony 框架中的核心文件上传类，没有对应的接口
        // 2. 测试中需要验证 setter/getter 行为，使用 Mock 可以避免实际文件系统操作
        // 3. 这是测试文件处理逻辑的标准做法，不依赖真实文件
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
