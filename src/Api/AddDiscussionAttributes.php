<?php

/*
 * This file is part of fof/mark-unread.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace FoF\MarkUnread\Api;

use Flarum\Api\Serializer\DiscussionSerializer;
use Flarum\Discussion\Discussion;

class AddDiscussionAttributes
{
    /**
     * @param array<string, mixed> $attributes
     *
     * @return array<string, mixed>
     */
    public function __invoke(DiscussionSerializer $serializer, Discussion $discussion, array $attributes): array
    {
        $actor = $serializer->getActor();

        $attributes['canMarkUnread'] = !$actor->isGuest() && $actor->can('markUnread', $discussion);

        return $attributes;
    }
}
