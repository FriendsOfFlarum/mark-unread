# FoF Mark Unread

![License](https://img.shields.io/badge/license-MIT-blue.svg) [![Latest Stable Version](https://img.shields.io/packagist/v/fof/mark-unread.svg)](https://packagist.org/packages/fof/mark-unread) [![Total Downloads](https://img.shields.io/packagist/dt/fof/mark-unread.svg)](https://packagist.org/packages/fof/mark-unread)

A [Flarum](https://flarum.org) extension by [FriendsOfFlarum](https://github.com/FriendsOfFlarum). Mark a discussion as unread.

Adds a **Mark as unread** control to the discussion controls menu, available from both the discussion list and the discussion page. Marking a discussion as unread resets your read position, so it shows as unread again in the discussion list.

Who can use it is controlled by the **Mark discussions as unread** permission.

![Discussion list](https://user-images.githubusercontent.com/16573496/140192759-121cd5ff-e885-437b-ae59-f810042e7718.png)

![Discussion page](https://user-images.githubusercontent.com/16573496/140192808-e970fccd-fdbc-4150-a65f-4fc9096ff758.png)

## Installation

Install with composer:

```sh
composer require fof/mark-unread:"*"
```

## Updating

```sh
composer update fof/mark-unread:"*"
php flarum cache:clear
```

## Migrating from blomstra/mark-unread

This extension was previously published as `blomstra/mark-unread`. To switch over:

```sh
composer remove blomstra/mark-unread
composer require fof/mark-unread:"*"
php flarum cache:clear
```

Then enable **FoF Mark Unread** in the admin panel. The permission name is unchanged, so existing permission grants carry over.

## Sponsored

This extension was originally sponsored by [Kagi Search](https://kagi.com/), an ad-free search engine, and released as open source for the wider Flarum community to benefit.

## Links

- [Packagist](https://packagist.org/packages/fof/mark-unread)
- [GitHub](https://github.com/FriendsOfFlarum/mark-unread)
- [Discuss](https://discuss.flarum.org/d/39955)
- [Report an issue](https://github.com/FriendsOfFlarum/mark-unread/issues)
