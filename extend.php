<?php

/*
 * This file is part of fof/mark-unread.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace FoF\MarkUnread;

use Flarum\Api\Serializer\DiscussionSerializer;
use Flarum\Discussion\Event\Saving;
use Flarum\Extend;
use Flarum\Api\Context;
use Flarum\Api\Endpoint;
use Flarum\Api\Resource;
use Flarum\Api\Schema;

return [
    (new Extend\Frontend('forum'))
        ->js(__DIR__.'/js/dist/forum.js'),

    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js'),

    new Extend\Locales(__DIR__.'/locale'),

    // @TODO: Replace with the new implementation https://docs.flarum.org/2.x/extend/api#extending-api-resources
    (new Extend\ApiSerializer(DiscussionSerializer::class))
        ->attributes(Api\AddDiscussionAttributes::class),

    (new Extend\Event())
        ->listen(Saving::class, Listener\MarkUnread::class),
];
