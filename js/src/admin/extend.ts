import app from 'flarum/admin/app';
import Extend from 'flarum/common/extenders';

export default [
  new Extend.Admin() //
    .permission(
      () => ({
        icon: 'fas fa-envelope',
        label: app.translator.trans('fof-mark-unread.admin.permissions.mark_unread_label'),
        permission: 'discussion.markUnread',
      }),
      'view',
      65
    ),
];
