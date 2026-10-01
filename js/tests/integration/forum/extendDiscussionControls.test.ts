import bootstrapForum from '@flarum/jest-config/src/bootstrap/forum';
import { jest } from '@jest/globals';
import app from 'flarum/forum/app';
import Discussion from 'flarum/common/models/Discussion';
import DiscussionControls from 'flarum/forum/utils/DiscussionControls';
import forumExtend from '../../../src/forum/extend';
import extendDiscussionControls, { markUnread } from '../../../src/forum/extenders/extendDiscussionControls';

function pushDiscussion(attributes: Record<string, unknown>): Discussion {
  app.store.pushPayload({
    data: {
      type: 'discussions',
      id: '1',
      attributes: {
        title: 'Discussion',
        slug: 'discussion',
        lastPostedAt: new Date().toISOString(),
        lastPostNumber: 2,
        commentCount: 2,
        ...attributes,
      },
    },
  });

  return app.store.getById<Discussion>('discussions', '1')!;
}

beforeAll(() => {
  bootstrapForum();
  app.bootExtensions({ 'fof-mark-unread': { extend: forumExtend } });
  app.boot();
  extendDiscussionControls();
});

describe('mark unread discussion control', () => {
  it('is shown for a read discussion the user can mark unread', () => {
    const discussion = pushDiscussion({ canMarkUnread: true, lastReadPostNumber: 2 });

    expect(DiscussionControls.moderationControls(discussion).has('fof-mark-unread')).toBe(true);
  });

  it('is hidden without permission', () => {
    const discussion = pushDiscussion({ canMarkUnread: false, lastReadPostNumber: 2 });

    expect(DiscussionControls.moderationControls(discussion).has('fof-mark-unread')).toBe(false);
  });

  it('is hidden when the discussion is already unread', () => {
    const discussion = pushDiscussion({ canMarkUnread: true, lastReadPostNumber: 1 });

    expect(DiscussionControls.moderationControls(discussion).has('fof-mark-unread')).toBe(false);
  });
});

describe('markUnread()', () => {
  it('PATCHes the existing discussion with only the unread attribute', async () => {
    const discussion = pushDiscussion({ canMarkUnread: true, lastReadPostNumber: 2 });
    const request = jest.spyOn(app, 'request').mockResolvedValue({
      data: { type: 'discussions', id: '1', attributes: { lastReadPostNumber: 0 } },
    } as never);

    await markUnread(discussion);

    expect(request).toHaveBeenCalledTimes(1);
    expect(request.mock.calls[0][0]).toMatchObject({
      method: 'PATCH',
      url: `${app.forum.attribute('apiUrl')}/discussions/1`,
      body: { data: { type: 'discussions', id: '1', attributes: { unread: true } } },
    });
    expect((request.mock.calls[0][0] as any).body.data.attributes).toEqual({ unread: true });
  });
});
