Changelog
===========

#### 1.4.3 - April 13, 2016

**Improvements**

- When user switches role or no longer matches custom conditions (using the `mailchimp_sync_should_sync_user` filter) he will now be unsubscribed from the selected MailChimp list.
- User fields which are an array of values are now automatically converted to a comma-separated string before they are sent to MailChimp.

#### 1.4.2 - March 14, 2016

**Fixes**

- Re-run subscribe method if email isn't found on MailChimp list (because of an invalid email, for example)

**Improvements**

- Setup schedule to run sync process at least once an hour, to prevent long delays.
- Strip `EMAIL` from available field map fields to prevent invalid configurations.
- Webhook updating a user will now write to [the debug log](https://mc4wp.com/kb/how-to-enable-log-debugging/).


#### 1.4.1 - February 10, 2016

**Fixes**

- Webhook verification not working when setting up webhook in MailChimp.

**Improvements**

- Remove JS sourcemaps from admin scripts.

#### 1.4 - January 26, 2016

This update requires you to update [MailChimp for WordPress](https://wordpress.org/plugins/mailchimp-for-wp/) to version 3.1 first.

**Fixes**

- Deleted users were no longer unsubscribed in some cases.

**Improvements**

- Use new Queue class from MailChimp for WordPress 3.1 for improved background processing.
- [Use new debug log for easier debugging](https://mc4wp.com/kb/how-to-enable-log-debugging/).
- Add HTTP status codes to Webhook listener.
- Miscellaneous code improvements

**Changes**

- WP CLI commands are now named `wp mailchimp-sync all` and `wp mailchimp-sync user <user_id>` (backwards compatible)

**Additions**

- WP CLI command `wp mailchimp-sync all` is now showing a progress bar


#### 1.3.3 - January 14, 2016

**Fixes**

- Fatal error on settings page on lower PHP versions because of missing space between `<?php` and translation call. This gets Forced Sync to work again.

#### 1.3.2 - January 13, 2016

**Fixes**

- Subscription status wasn't showing on user profile.

**Improvements**

- Check for correct request parameters before processing [MailChimp webhook](https://mc4wp.com/kb/configure-webhook-for-2-way-synchronizing/).
- Change plugin name to "MailChimp User Sync"
- Document all WP CLI commands.
- Better mobile responsiveness for settings pages.
- Use Browserify to handle script dependencies.
- Improved compatibility with [MailChimp for WordPress v3.0](https://mc4wp.com/blog/the-big-three-o-release/)

#### 1.3.1 - November 13, 2015

**Improvements**

- Compatibility fixes for [the upcoming MailChimp for WordPress 3.0 release](https://mc4wp.com/blog/breaking-backwards-compatibility-in-version-3-0/).

**Additions**

- Added `mailchimp_sync_get_user_field` filter to get user fields from a custom source and sync those to MailChimp.

#### 1.3 - October 17, 2015

**Fixes**

- Webhook not picking up on custom fields, it was only updating default user fields.
- When creating user via `mailchimp_sync_webhook_user` filter, it was not staying in sync.

**Improvements**

- Changes are now sent to MailChimp **after** all changes are applied, at the end of the request.
- Individual changes in `user_meta` will now be taken into account as well.

#### 1.2.3 - October 12, 2015

**Fixes**

- Webhook listener not working since version 1.2.2.
- Fields in additional fields section were stripped on settings save (when using "+ Add Line" button).

**Improvements**

- Various defensive coding improvements to the webhook listener


#### 1.2.2 - October 7, 2015

**Additions**

- Introduced 2 new filters (`mailchimp_sync_webhook_user` and `mailchimp_sync_webhook_no_user`) which allow you to hook into the MailChimp webhook listener to specify the WP user or do something when there is no user for the MailChimp subscriber. [Here is a code example that creates a new user when the subscriber has no user account](https://gist.github.com/dannyvankooten/79fe429daaef611b6aa5).

#### 1.2.1 - October 1, 2015

**Improvements**

- For mapping user fields, you can now manually type the "meta key" value of the field. Comes with autocomplete if you have users with that field already.
- For WooCommerce checkout: run after custom fields have been added

**Fixes**

- Newly added rows could not be removed unless page was refreshed again.

#### 1.2 - September 24, 2015

**Additions**

- Added support for MailChimp webhooks, so data can be synchronized from MailChimp to WordPress as well.  To enable this, you need to [configure a webhook in your MailChimp account](https://mc4wp.com/kb/configure-webhook-for-2-way-synchronizing/).

#### 1.1.3 - September 9, 2015

**Fixes**

- Status indicator was not working for installations with a custom database prefix.

**Improvements**

- You can now view & clear the log file from the settings page.
- Nothing will be logged unless `WP_DEBUG` is enabled.

#### 1.1.2 - September 8, 2015

**Fixes**

- Status indicator (in sync / out of sync) is now showing the correct # of users when a role is set.

**Improvements**

- Field rules will now clear when changing the MailChimp list to subscribe to.
- Make it more clear that settings should be saved after choosing a MailChimp list.

#### 1.1.1 - August 28, 2015

**Additions**

- Allows you to send the user role as well.

#### 1.1 - August 28, 2015

**Additions**

- You can now send additional user fields.
- You can now subscribe individual users from their "edit user" page.

#### 1.0.2 - August 18, 2015

**Improvements**

- Errors are now written to dedicated log file, usually located in `/wp-content/uploads/mailchimp-sync.log`.
- Added `mailchimp_sync_should_sync_user` filter, which lets you set your own criteria for subscribing a user.

#### 1.0.1 - July 14, 2015

**Improvements**

- More detailed error message are now shown in the log.
- Force Sync will now start with unsynced users.

#### 1.0 - May 29, 2015

**Fixes**

- Force synchronization would not work on large data sets (> 10.000). The process is now batched.

**Improvements**

- Pause & resume the forced synchronization process

**Additions**

- Enable & disable auto-syncing
- Choose a user role to synchronize.
- [WP CLI](http://wp-cli.org/) commands: `wp mailchimp-sync sync-all` and `wp mailchimp-sync sync-user $user_id`.
- Filter: `mailchimp_sync_user_data` to modify user data before it's sent to MailChimp.

For more detailed usage info on the introduced features, have a look at the [MailChimp User Sync FAQ](https://wordpress.org/plugins/mailchimp-sync/faq/).

#### 0.1.2 - March 17, 2015

**Fixes**

- Synchronising would stop if a synchronize request failed
- Conflict with other plugins bundling old versions of Composer, throwing a fatal error on plugin activation
- Users who were deleted from a list would cause issues, they're now re-subscribed.

**Improvements**

- Added some feedback to Log whether a synchronization request succeeded or not.

#### 0.1.1 - February 17, 2015

**Fixes**

- Force Sync got stuck on users without a valid email address. ([#10](https://github.com/ibericode/mailchimp-user-sync/issues/10), thanks [girandovoy](https://github.com/girandovoy))
- JSON response was malformed when any plugin threw a PHP notice

**Improvements**

- Progress log now auto-scrolls to bottom
- Progress log now shows time
- Progress log now shows more actions
- Add settings link to Plugin overview
- Various JavaScript improvements

#### 0.1 - January 23, 2015

Initial release.