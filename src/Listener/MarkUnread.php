<?php

/*
 * This file is part of fof/mark-unread.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace FoF\MarkUnread\Listener;

use Flarum\Discussion\Event\Saving;
use Illuminate\Support\Arr;

class MarkUnread
{
    public function handle(Saving $event): void
    {
        $actor = $event->actor;
        $discussion = $event->discussion;

        // Saving also fires when a discussion is started; there is no read state to reset yet.
        if (!$discussion->exists || !Arr::get($event->data, 'attributes.unread')) {
            return;
        }

        $actor->assertRegistered();
        $actor->assertCan('markUnread', $discussion);

        $state = $discussion->stateFor($actor);
        $state->last_read_post_number = 0;
        $state->last_read_at = null;
        $state->save();
    }
}
