import app from 'flarum/forum/app';
import { extend } from 'flarum/common/extend';
import Button from 'flarum/common/components/Button';
import DiscussionPage from 'flarum/forum/components/DiscussionPage';
import DiscussionControls from 'flarum/forum/utils/DiscussionControls';
import type Discussion from 'flarum/common/models/Discussion';
import type ItemList from 'flarum/common/utils/ItemList';
import type Mithril from 'mithril';

export function markUnread(discussion: Discussion): Promise<void> {
  return discussion.save({ unread: true }).then(() => {
    if (app.current.matches(DiscussionPage)) {
      app.current.get('stream')?.update();
    }

    m.redraw();
  });
}

export default function extendDiscussionControls() {
  extend(DiscussionControls, 'moderationControls', function (items: ItemList<Mithril.Children>, discussion: Discussion) {
    if (!discussion.canMarkUnread() || discussion.isUnread()) return;

    items.add(
      'fof-mark-unread',
      <Button icon="fas fa-envelope" onclick={() => markUnread(discussion)}>
        {app.translator.trans('fof-mark-unread.forum.discussion_controls.mark_unread_button')}
      </Button>,
      30
    );
  });
}
