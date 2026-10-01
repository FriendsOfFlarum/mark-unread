import app from 'flarum/admin/app';

app.initializers.add('fof/mark-unread', () => {
  app.extensionData.for('fof-mark-unread').registerPermission(
    {
      icon: 'fas fa-envelope',
      label: app.translator.trans('fof-mark-unread.admin.permissions.mark_unread_label'),
      permission: 'discussion.markUnread',
    },
    'view',
    65
  );
});
