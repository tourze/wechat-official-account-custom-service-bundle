# WeChat Official Account Custom Service Bundle

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/wechat-official-account-custom-service-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/wechat-official-account-custom-service-bundle)
[![PHP Version](https://img.shields.io/packagist/php-v/tourze/wechat-official-account-custom-service-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/wechat-official-account-custom-service-bundle)
[![Build Status](https://img.shields.io/github/actions/workflow/status/tourze/wechat-official-account-custom-service-bundle/ci.yml?style=flat-square)](https://github.com/tourze/wechat-official-account-custom-service-bundle/actions)
[![Coverage](https://img.shields.io/codecov/c/github/tourze/wechat-official-account-custom-service-bundle.svg?style=flat-square)](https://codecov.io/gh/tourze/wechat-official-account-custom-service-bundle)
[![License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)

A Symfony bundle for managing WeChat Official Account customer service representatives,
providing complete account management and automatic synchronization with WeChat servers.

## Table of Contents

- [Features](#features)
- [Installation](#installation)
- [Quick Start](#quick-start)
  - [1. Creating a Customer Service Account](#1-creating-a-customer-service-account)
  - [2. Syncing Accounts from WeChat](#2-syncing-accounts-from-wechat)
  - [3. Uploading Customer Service Avatar](#3-uploading-customer-service-avatar)
- [Advanced Usage](#advanced-usage)
  - [Custom Repository Methods](#custom-repository-methods)
  - [Event Listeners](#event-listeners)
- [API Reference](#api-reference)
  - [Entity: KfAccount](#entity-kfaccount)
  - [Enum: KfAccountStatus](#enum-kfaccountstatus)
  - [Repository Methods](#repository-methods)
- [Console Commands](#console-commands)
  - [wechat-official-account:custom-service:sync-account-list](#wechat-official-accountcustom-servicesync-account-list)
- [Automatic Synchronization](#automatic-synchronization)
- [Security](#security)
  - [Password Security](#password-security)
  - [Access Control](#access-control)
- [Configuration](#configuration)
- [Requirements](#requirements)
- [Contributing](#contributing)
- [License](#license)
- [References](#references)

## Features

- **Customer Service Account Management** - Create, update, and delete customer service accounts
- **Automatic Synchronization** - Two-way sync between local database and WeChat servers
- **Multi-Account Support** - Manage customer service for multiple official accounts
- **Avatar Management** - Upload and manage customer service avatars
- **Status Tracking** - Track account status (enabled, disabled, deleted)
- **Command Line Tools** - Batch operations via console commands

## Installation

```bash
composer require tourze/wechat-official-account-custom-service-bundle
```

## Advanced Usage

### Custom Repository Methods

The bundle provides advanced repository methods for complex queries:

```php
// Find accounts with avatars
$accountsWithAvatars = $kfAccountRepository->findAllWithAvatars();

// Find recently updated accounts
$recentAccounts = $kfAccountRepository->findRecentlyUpdated($days = 7);

// Bulk status update
$kfAccountRepository->bulkUpdateStatus([$accountId1, $accountId2], KfAccountStatus::DISABLED);
```

### Event Listeners

The bundle fires events for account lifecycle operations:

```php
use WechatOfficialAccountCustomServiceBundle\Event\KfAccountCreatedEvent;
use WechatOfficialAccountCustomServiceBundle\Event\KfAccountUpdatedEvent;
use WechatOfficialAccountCustomServiceBundle\Event\KfAccountDeletedEvent;

// Listen to account creation
$eventDispatcher->addListener(KfAccountCreatedEvent::class, function(KfAccountCreatedEvent $event) {
    $account = $event->getKfAccount();
    // Custom logic here
});
```

## Quick Start

### 1. Creating a Customer Service Account

```php
use WechatOfficialAccountCustomServiceBundle\Entity\KfAccount;
use WechatOfficialAccountCustomServiceBundle\Enum\KfAccountStatus;

$kfAccount = new KfAccount();
$kfAccount->setAccount($officialAccount);
$kfAccount->setKfAccount('service001@yourcompany');
$kfAccount->setNickname('Customer Service 001');
$kfAccount->setPassword('secure_password');
$kfAccount->setStatus(KfAccountStatus::ENABLED);

$entityManager->persist($kfAccount);
$entityManager->flush(); // Automatically syncs to WeChat
```

### 2. Syncing Accounts from WeChat

```bash
# Sync all official accounts
php bin/console wechat-official-account:custom-service:sync-account-list

# Sync specific official account
php bin/console wechat-official-account:custom-service:sync-account-list --account-id=123456
```

## Security

### Password Security

Customer service passwords are handled securely:

- Passwords are never logged or exposed in API responses
- Use strong passwords (minimum 8 characters)
- Change passwords regularly through WeChat backend

### Access Control

```php
// Validate account ownership before operations
if ($kfAccount->getAccount()->getId() !== $currentAccount->getId()) {
    throw new AccessDeniedException('Unauthorized access to customer service account');
}
```

### 3. Uploading Customer Service Avatar

```php
use WechatOfficialAccountCustomServiceBundle\Request\UploadKfAccountHeadimgRequest;

$request = new UploadKfAccountHeadimgRequest();
$request->setAccount($officialAccount);
$request->setKfAccount('service001@yourcompany');
$request->setMedia('/path/to/avatar.jpg');

$response = $officialAccountClient->request($request);
```

## API Reference

### Entity: KfAccount

| Property | Type | Description |
|----------|------|-------------|
| account | Account | Associated official account |
| kfAccount | string | Customer service account (unique) |
| nickname | string | Display name |
| password | string? | Account password |
| avatar | string? | Avatar URL |
| status | KfAccountStatus | Account status |
| kfId | string? | WeChat service ID |

### Enum: KfAccountStatus

- `ENABLED` - Active account
- `DISABLED` - Disabled account
- `DELETED` - Deleted account

### Repository Methods

```php
// Find all enabled accounts
$enabledAccounts = $kfAccountRepository->findAllEnabled();

// Find by customer service account
$account = $kfAccountRepository->findOneByKfAccount('service001@yourcompany');

// Count by status
$statusCounts = $kfAccountRepository->countGroupByStatus();
```

## Console Commands

### wechat-official-account:custom-service:sync-account-list

Synchronize customer service accounts from WeChat servers to local database.

**Usage:**
```bash
php bin/console wechat-official-account:custom-service:sync-account-list [options]
```

**Options:**
- `--account-id=ACCOUNT_ID` - Sync specific official account only

**Sync Logic:**
1. Fetches remote customer service list from WeChat
2. Compares with local database
3. Creates new accounts that don't exist locally
4. Updates existing account information
5. Marks deleted accounts as `DELETED` status

## Automatic Synchronization

The bundle includes automatic synchronization through Doctrine event listeners:

- **On Create**: Automatically creates account on WeChat servers
- **On Update**: Syncs changes to WeChat servers
- **On Delete**: Removes account from WeChat servers

Synchronization is protected by a `syncing` flag to prevent infinite loops.

## Configuration

The bundle automatically registers its services and event listeners.
No additional configuration is required beyond setting up the dependent
`wechat-official-account-bundle`.

## Requirements

- PHP 8.1+
- Symfony 6.4+
- tourze/wechat-official-account-bundle 0.1.*

## Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

## References

- [WeChat Official Account Customer Service API Documentation](
  https://developers.weixin.qq.com/doc/offiaccount/Message_Management/Service_Center_messages.html
)