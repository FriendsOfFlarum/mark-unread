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

return [
    (new Extend\Frontend('forum'))
        ->js(__DIR__.'/js/dist/forum.js'),

    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js'),

    new Extend\Locales(__DIR__.'/locale'),

    (new Extend\ApiSerializer(DiscussionSerializer::class))
        ->attributes(Api\AddDiscussionAttributes::class),

    (new Extend\Event())
        ->listen(Saving::class, Listener\MarkUnread::class),
];
