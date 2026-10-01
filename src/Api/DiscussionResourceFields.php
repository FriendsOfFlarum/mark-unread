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

use Flarum\Api\Context;
use Flarum\Api\Schema;
use Flarum\Discussion\Discussion;

class DiscussionResourceFields
{
    public function __invoke(): array
    {
        return [
            Schema\Boolean::make('canMarkUnread')
                ->get(fn (Discussion $discussion, Context $context) => !$context->getActor()->isGuest() && $context->getActor()->can('markUnread', $discussion)),

            // Write-only: 2.x rejects undeclared attributes, so the PATCH {unread: true} needs a field to land on.
            Schema\Boolean::make('unread')
                ->hidden()
                ->writable(fn (Discussion $discussion, Context $context) => $context->updating())
                ->set(function (Discussion $discussion, bool $unread, Context $context) {
                    if (!$unread) {
                        return;
                    }

                    $actor = $context->getActor();

                    $actor->assertRegistered();
                    $actor->assertCan('markUnread', $discussion);

                    $state = $discussion->stateFor($actor);
                    $state->last_read_post_number = 0;
                    $state->last_read_at = null;

                    // Keep the response in sync with the reset read state.
                    $discussion->setRelation('state', $state);

                    $discussion->afterSave(fn () => $state->save());
                }),
        ];
    }
}
