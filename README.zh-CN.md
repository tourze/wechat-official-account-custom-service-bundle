# 微信公众号客服包

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/wechat-official-account-custom-service-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/wechat-official-account-custom-service-bundle)
[![PHP Version](https://img.shields.io/packagist/php-v/tourze/wechat-official-account-custom-service-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/wechat-official-account-custom-service-bundle)
[![Build Status](https://img.shields.io/github/actions/workflow/status/tourze/wechat-official-account-custom-service-bundle/ci.yml?style=flat-square)](https://github.com/tourze/wechat-official-account-custom-service-bundle/actions)
[![Coverage](https://img.shields.io/codecov/c/github/tourze/wechat-official-account-custom-service-bundle.svg?style=flat-square)](https://codecov.io/gh/tourze/wechat-official-account-custom-service-bundle)
[![License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)

用于管理微信公众号客服账号的 Symfony 包，提供完整的账号管理功能和与微信服务器的自动同步。

## 目录

- [功能特性](#功能特性)
- [安装](#安装)
- [快速开始](#快速开始)
  - [1. 创建客服账号](#1-创建客服账号)
  - [2. 从微信同步账号](#2-从微信同步账号)
  - [3. 上传客服头像](#3-上传客服头像)
- [高级用法](#高级用法)
  - [自定义仓储方法](#自定义仓储方法)
  - [事件监听器](#事件监听器)
- [API 参考](#api-参考)
  - [实体：KfAccount](#实体kfaccount)
  - [枚举：KfAccountStatus](#枚举kfaccountstatus)
  - [仓储方法](#仓储方法)
- [控制台命令](#控制台命令)
  - [wechat-official-account:custom-service:sync-account-list](#wechat-official-accountcustom-servicesync-account-list)
- [自动同步](#自动同步)
- [安全性](#安全性)
  - [密码安全](#密码安全)
  - [访问控制](#访问控制)
- [配置](#配置)
- [系统要求](#系统要求)
- [贡献](#贡献)
- [许可证](#许可证)
- [参考文档](#参考文档)

## 功能特性

- **客服账号管理** - 创建、更新和删除客服账号
- **自动同步** - 本地数据库与微信服务器双向同步
- **多账号支持** - 管理多个公众号的客服
- **头像管理** - 上传和管理客服头像
- **状态跟踪** - 跟踪账号状态（启用、禁用、已删除）
- **命令行工具** - 通过控制台命令进行批量操作

## 安装

```bash
composer require tourze/wechat-official-account-custom-service-bundle
```

## 高级用法

### 自定义仓储方法

本包提供高级仓储方法用于复杂查询：

```php
// 查找有头像的账号
$accountsWithAvatars = $kfAccountRepository->findAllWithAvatars();

// 查找最近更新的账号
$recentAccounts = $kfAccountRepository->findRecentlyUpdated($days = 7);

// 批量状态更新
$kfAccountRepository->bulkUpdateStatus([$accountId1, $accountId2], KfAccountStatus::DISABLED);
```

### 事件监听器

本包为账号生命周期操作触发事件：

```php
use WechatOfficialAccountCustomServiceBundle\Event\KfAccountCreatedEvent;
use WechatOfficialAccountCustomServiceBundle\Event\KfAccountUpdatedEvent;
use WechatOfficialAccountCustomServiceBundle\Event\KfAccountDeletedEvent;

// 监听账号创建
$eventDispatcher->addListener(KfAccountCreatedEvent::class, function(KfAccountCreatedEvent $event) {
    $account = $event->getKfAccount();
    // 自定义逻辑
});
```

## 快速开始

### 1. 创建客服账号

```php
use WechatOfficialAccountCustomServiceBundle\Entity\KfAccount;
use WechatOfficialAccountCustomServiceBundle\Enum\KfAccountStatus;

$kfAccount = new KfAccount();
$kfAccount->setAccount($officialAccount);
$kfAccount->setKfAccount('service001@yourcompany');
$kfAccount->setNickname('客服001');
$kfAccount->setPassword('secure_password');
$kfAccount->setStatus(KfAccountStatus::ENABLED);

$entityManager->persist($kfAccount);
$entityManager->flush(); // 自动同步到微信
```

### 2. 从微信同步账号

```bash
# 同步所有公众号
php bin/console wechat-official-account:custom-service:sync-account-list

# 同步指定公众号
php bin/console wechat-official-account:custom-service:sync-account-list --account-id=123456
```

## 安全性

### 密码安全

客服密码处理安全：

- 密码不会被记录或在API响应中暴露
- 使用强密码（最少8个字符）
- 通过微信后台定期更改密码

### 访问控制

```php
// 操作前验证账号所有权
if ($kfAccount->getAccount()->getId() !== $currentAccount->getId()) {
    throw new AccessDeniedException('无权访问此客服账号');
}
```

### 3. 上传客服头像

```php
use WechatOfficialAccountCustomServiceBundle\Request\UploadKfAccountHeadimgRequest;

$request = new UploadKfAccountHeadimgRequest();
$request->setAccount($officialAccount);
$request->setKfAccount('service001@yourcompany');
$request->setMedia('/path/to/avatar.jpg');

$response = $officialAccountClient->request($request);
```

## API 参考

### 实体：KfAccount

| 属性 | 类型 | 描述 |
|-----|------|-----|
| account | Account | 关联的公众号 |
| kfAccount | string | 客服账号（唯一） |
| nickname | string | 显示名称 |
| password | string? | 账号密码 |
| avatar | string? | 头像URL |
| status | KfAccountStatus | 账号状态 |
| kfId | string? | 微信客服ID |

### 枚举：KfAccountStatus

- `ENABLED` - 启用状态
- `DISABLED` - 禁用状态
- `DELETED` - 已删除状态

### 仓储方法

```php
// 查找所有启用的账号
$enabledAccounts = $kfAccountRepository->findAllEnabled();

// 根据客服账号查找
$account = $kfAccountRepository->findOneByKfAccount('service001@yourcompany');

// 按状态统计数量
$statusCounts = $kfAccountRepository->countGroupByStatus();
```

## 控制台命令

### wechat-official-account:custom-service:sync-account-list

从微信服务器同步客服账号到本地数据库。

**用法：**
```bash
php bin/console wechat-official-account:custom-service:sync-account-list [选项]
```

**选项：**
- `--account-id=ACCOUNT_ID` - 仅同步指定的公众号

**同步逻辑：**
1. 从微信获取远程客服列表
2. 与本地数据库比对
3. 创建本地不存在的新账号
4. 更新已存在的账号信息
5. 将已删除的账号标记为 `DELETED` 状态

## 自动同步

本包通过 Doctrine 事件监听器实现自动同步：

- **创建时**：自动在微信服务器创建账号
- **更新时**：同步更改到微信服务器
- **删除时**：从微信服务器删除账号

同步机制通过 `syncing` 标记防止无限循环。

## 配置

本包会自动注册服务和事件监听器。除了配置依赖的 `wechat-official-account-bundle` 外，无需额外配置。

## 系统要求

- PHP 8.1+
- Symfony 6.4+
- tourze/wechat-official-account-bundle 0.1.*

## 贡献

详情请查看 [CONTRIBUTING.md](CONTRIBUTING.md)。

## 许可证

MIT 许可证。详情请查看[许可证文件](LICENSE)。

## 参考文档

- [微信公众号客服接口文档](https://developers.weixin.qq.com/doc/offiaccount/Message_Management/Service_Center_messages.html)