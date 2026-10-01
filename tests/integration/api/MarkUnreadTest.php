<?php

/*
 * This file is part of fof/mark-unread.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace FoF\MarkUnread\Tests\integration\api;

use Carbon\Carbon;
use Flarum\Discussion\Discussion;
use Flarum\Discussion\UserState;
use Flarum\Post\Post;
use Flarum\Testing\integration\RetrievesAuthorizedUsers;
use Flarum\Testing\integration\TestCase;
use Flarum\User\User;
use PHPUnit\Framework\Attributes\Test;

class MarkUnreadTest extends TestCase
{
    use RetrievesAuthorizedUsers;

    protected function setUp(): void
    {
        parent::setUp();

        $this->extension('fof-mark-unread');

        $this->prepareDatabase([
            User::class => [
                $this->normalUser(),
                ['id' => 3, 'username' => 'moderator', 'password' => '$2y$10$LO59tiT7uggl6Oe23o/O6.utnF6ipngYjvMvaxo1TciKqBttDNKim', 'email' => 'moderator@machine.local', 'is_email_confirmed' => 1],
            ],
            'group_user' => [
                ['group_id' => 4, 'user_id' => 3],
            ],
            'group_permission' => [
                ['group_id' => 4, 'permission' => 'discussion.markUnread'],
            ],
            Discussion::class => [
                ['id' => 1, 'title' => 'Read discussion', 'created_at' => Carbon::now()->subDay(), 'last_posted_at' => Carbon::now()->subDay(), 'user_id' => 1, 'first_post_id' => 1, 'last_post_id' => 2, 'last_post_number' => 2, 'comment_count' => 2],
            ],
            Post::class => [
                ['id' => 1, 'discussion_id' => 1, 'created_at' => Carbon::now()->subDay(), 'user_id' => 1, 'type' => 'comment', 'content' => '<t><p>First</p></t>', 'number' => 1],
                ['id' => 2, 'discussion_id' => 1, 'created_at' => Carbon::now()->subDay(), 'user_id' => 1, 'type' => 'comment', 'content' => '<t><p>Second</p></t>', 'number' => 2],
            ],
            'discussion_user' => [
                ['user_id' => 2, 'discussion_id' => 1, 'last_read_post_number' => 2, 'last_read_at' => Carbon::now()],
                ['user_id' => 3, 'discussion_id' => 1, 'last_read_post_number' => 2, 'last_read_at' => Carbon::now()],
            ],
        ]);
    }

    protected function markUnread(int $userId, bool $unread = true)
    {
        return $this->send(
            $this->request('PATCH', '/api/discussions/1', [
                'authenticatedAs' => $userId,
                'json'            => [
                    'data' => [
                        'attributes' => [
                            'unread' => $unread,
                        ],
                    ],
                ],
            ])
        );
    }

    protected function stateFor(int $userId): ?UserState
    {
        // Boot the app so Eloquent has a connection when queried before any request is sent.
        $this->app();

        return UserState::query()
            ->where('discussion_id', 1)
            ->where('user_id', $userId)
            ->first();
    }

    #[Test]
    public function user_with_permission_can_mark_discussion_unread()
    {
        $response = $this->markUnread(3);

        $this->assertEquals(200, $response->getStatusCode());

        $body = json_decode($response->getBody()->getContents(), true);
        $this->assertSame(0, $body['data']['attributes']['lastReadPostNumber']);
        $this->assertNull($body['data']['attributes']['lastReadAt']);

        $state = $this->stateFor(3);
        $this->assertEquals(0, $state->last_read_post_number);
        $this->assertNull($state->last_read_at);
    }

    #[Test]
    public function user_without_permission_cannot_mark_discussion_unread()
    {
        $response = $this->markUnread(2);

        $this->assertEquals(403, $response->getStatusCode());

        $state = $this->stateFor(2);
        $this->assertEquals(2, $state->last_read_post_number);
        $this->assertNotNull($state->last_read_at);
    }

    #[Test]
    public function unread_false_leaves_read_state_untouched()
    {
        $response = $this->markUnread(3, false);

        $this->assertEquals(200, $response->getStatusCode());

        $state = $this->stateFor(3);
        $this->assertEquals(2, $state->last_read_post_number);
        $this->assertNotNull($state->last_read_at);
    }

    #[Test]
    public function admin_can_mark_never_read_discussion_unread()
    {
        $this->assertNull($this->stateFor(1));

        $response = $this->markUnread(1);

        $this->assertEquals(200, $response->getStatusCode());

        $state = $this->stateFor(1);
        $this->assertNotNull($state);
        $this->assertEquals(0, $state->last_read_post_number);
        $this->assertNull($state->last_read_at);
    }

    #[Test]
    public function can_mark_unread_attribute_reflects_permission()
    {
        $canMarkUnread = function (?int $userId): bool {
            $response = $this->send(
                $this->request('GET', '/api/discussions/1', $userId ? ['authenticatedAs' => $userId] : [])
            );

            $this->assertEquals(200, $response->getStatusCode());

            return json_decode($response->getBody()->getContents(), true)['data']['attributes']['canMarkUnread'];
        };

        $this->assertTrue($canMarkUnread(1));
        $this->assertTrue($canMarkUnread(3));
        $this->assertFalse($canMarkUnread(2));
        $this->assertFalse($canMarkUnread(null));
    }

    #[Test]
    public function guest_never_gets_can_mark_unread_even_with_permission()
    {
        $this->prepareDatabase([
            'group_permission' => [
                ['group_id' => 2, 'permission' => 'discussion.markUnread'],
            ],
        ]);

        $response = $this->send(
            $this->request('GET', '/api/discussions/1')
        );

        $this->assertEquals(200, $response->getStatusCode());

        $body = json_decode($response->getBody()->getContents(), true);
        $this->assertFalse($body['data']['attributes']['canMarkUnread']);
    }
}
