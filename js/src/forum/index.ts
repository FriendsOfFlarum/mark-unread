import app from 'flarum/forum/app';
import extendDiscussionControls from './extenders/extendDiscussionControls';

export { default as extend } from './extend';

app.initializers.add('fof/mark-unread', () => {
  extendDiscussionControls();
});
