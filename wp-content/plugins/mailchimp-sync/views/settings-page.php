<?php
namespace MC4WP\Sync\Admin;

use MC4WP\Sync\Plugin;

defined( 'ABSPATH' ) or exit;


/** @var StatusIndicator $status_indicator */
?>
<div class="wrap" id="mc4wp-admin">

	<p class="breadcrumbs">
		<span class="prefix"><?php echo __( 'You are here: ', 'mailchimp-for-wp' ); ?></span>
		<a href="<?php echo admin_url( 'admin.php?page=mailchimp-for-wp' ); ?>">MailChimp for WordPress</a> &rsaquo;
		<span class="current-crumb"><strong>User Sync</strong></span>
	</p>

	<div class="main-content row">

		<!-- Main Content -->
		<div class="main-content col col-4 col-sm-6">
			<h1 class="page-title">MailChimp User Sync</h1>

			<form method="post" action="<?php echo admin_url( 'options.php' ); ?>">

				<?php settings_fields( Plugin::OPTION_NAME ); ?>


				<h2><?php _e( 'Settings' ); ?></h2>
				<?php settings_errors(); ?>

				<table class="form-table">

					<tr>
						<th scope="row"><?php _e( 'Enable auto-sync', 'mailchimp-sync' ); ?></th>
						<td class="nowrap">
							<label><input type="radio" name="<?php echo $this->name_attr( 'enabled' ); ?>" value="1" <?php checked( $this->options['enabled'], 1 ); ?> /> <?php _e( 'Yes' ); ?></label> &nbsp;
							<label><input type="radio" name="<?php echo $this->name_attr( 'enabled' ); ?>" value="0" <?php checked( $this->options['enabled'], 0 ); ?> /> <?php _e( 'No' ); ?></label>
							<p  class="help"><?php _e( 'Select "yes" if you want the plugin to "listen" to all changes in your WordPress user base and auto-sync them with the selected MailChimp list.', 'mailchimp-sync' ); ?></p>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row"><?php _e( 'Sync users with this list', 'mailchimp-sync' ); ?></th>
						<td>
							<?php if( empty( $lists ) ) {
								printf( __( 'No lists found, <a href="%s">are you connected to MailChimp</a>?', 'mailchimp-for-wp' ), admin_url( 'admin.php?page=mailchimp-for-wp' ) ); ?>
							<?php } else { ?>

							<select name="<?php echo $this->name_attr( 'list' ); ?>" class="widefat">
								<option disabled <?php selected( $this->options['list'], '' ); ?>><?php _e( 'Select a list..', 'mailchimp-sync' ); ?></option>
								<?php foreach( $lists as $list ) { ?>
									<option value="<?php echo esc_attr( $list->id ); ?>" <?php selected( $this->options['list'], $list->id ); ?>><?php echo esc_html( $list->name ); ?></option>
								<?php } ?>
							</select>
							<?php } ?>

							<p class="help"><?php _e( 'Select the list to synchronize your WordPress user base with.' ,'mailchimp-sync' ); ?></p>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row"><?php _e( 'Double opt-in?', 'mailchimp-for-wp' ); ?></th>
						<td class="nowrap">
							<label>
								<input type="radio" name="<?php echo $this->name_attr( 'double_optin' ); ?>" value="1" <?php checked( $this->options['double_optin'], 1 ); ?> />
								<?php _e( 'Yes', 'mailchimp-for-wp' ); ?>
							</label> &nbsp;
							<label>
								<input type="radio" id="mc4wp_checkbox_double_optin_0" name="<?php echo $this->name_attr( 'double_optin' ); ?>" value="0" <?php checked( $this->options['double_optin'], 0 ); ?> />
								<?php _e( 'No', 'mailchimp-for-wp' ); ?>
							</label>

							<p class="help"><?php _e( 'Select "yes" if you want people to confirm their email address before being subscribed (recommended)', 'mailchimp-for-wp' ); ?></p>
						</td>
					</tr>

					<?php $enabled = !$this->options['double_optin']; ?>
					<tr id="mc4wp-send-welcome"  valign="top" <?php if(!$enabled) { ?>class="hidden"<?php } ?>>
						<th scope="row"><?php _e( 'Send Welcome Email?', 'mailchimp-for-wp' ); ?></th>
						<td class="nowrap">
							<input type="radio" id="mc4wp_checkbox_send_welcome_1" name="<?php echo $this->name_attr( 'send_welcome' ); ?>" value="1" <?php if($enabled) { checked( $this->options['send_welcome'], 1 ); } else { echo 'disabled'; } ?> />
							<label for="mc4wp_checkbox_send_welcome_1"><?php _e( 'Yes', 'mailchimp-for-wp' ); ?></label> &nbsp;
							<input type="radio" id="mc4wp_checkbox_send_welcome_0" name="<?php echo $this->name_attr( 'send_welcome' ); ?>" value="0" <?php if($enabled) { checked( $this->options['send_welcome'], 0 ); } else { echo 'disabled'; } ?> />
							<label for="mc4wp_checkbox_send_welcome_0"><?php _e( 'No', 'mailchimp-for-wp' ); ?></label> &nbsp;

							<p class="help">
								<?php _e( 'Select "yes" if you want to send your lists Welcome Email if a subscribe succeeds (only when double opt-in is disabled).', 'mailchimp-for-wp' ); ?>
							</p>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row"><?php _e( 'Role to sync', 'mailchimp-sync' ); ?></th>
						<td>
							<select name="<?php echo $this->name_attr('role'); ?>" id="role-select">
								<option value="" <?php selected( $this->options['role'], '' ); ?>><?php _e( 'All roles', 'mailchimp-sync' ); ?></option>
								<?php
								$roles = get_editable_roles();
								foreach( $roles as $key => $role ) {
									echo '<option value="' . $key . '" '. selected( $this->options['role'], $key, false ) .'>' . $role['name'] . '</option>';
								}
								?>
							</select>

							<p class="help"><?php _e( 'Select a specific role to synchronize.', 'mailchimp-sync' ); ?></p>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row">
							<label><?php _e( 'Send Additional Fields', 'mailchimp-sync' ); ?></label>
						</th>
						<td class="mc4wp-sync-field-map">
							<?php

							if( ! isset( $selected_list ) ) {
								echo '<p class="help">' . __( 'Please select a MailChimp list first (and then save your settings).', 'mailchimp-sync' ) . '</p>';
							} else {

								foreach( $this->options['field_mappers'] as $index => $rule ) {
								?>
								<div class="field-map-row">
									<input name="<?php echo $this->name_attr( '[field_mappers]['.$index.'][user_field]' ); ?>" class="user-field" value="<?php echo esc_attr( $rule['user_field'] ); ?>" placeholder="<?php _e( 'User field' ,'mailchimp-sync' ); ?>">

									&nbsp; <?php _e( 'to', 'mailchimp-sync' ); ?> &nbsp;

									<select name="<?php echo $this->name_attr( '[field_mappers]['.$index.'][mailchimp_field]' ); ?>" class="mailchimp-field">
										<option disabled <?php selected( $rule['mailchimp_field'], '' ); ?>><?php esc_html_e( 'MailChimp field', 'mailchimp-sync' ); ?></option>
										<?php foreach( $available_mailchimp_fields as $field ) { ?>
											<option value="<?php echo esc_attr( $field->tag ); ?>" <?php selected( $field->tag, $rule['mailchimp_field'] ); ?>>
												<?php echo strip_tags( $field->name ); ?>
											</option>
										<?php } ?>
									</select>
									<?php
									// output button to remove this row
									if( $index > 0 ) {
										echo '<input type="button" value="&times;" class="button remove-row" />';
									} ?>
								</div>
								<?php
								}
								?>

								<p><input type="button" class="button add-row" value="&plus; <?php esc_attr_e( 'Add line', 'mailchimp-sync' ); ?>" style="margin-left:0; "/></p>

							<?php } ?>
						</td>
					</tr>

				</table>

				<?php submit_button(); ?>
			</form>

			<?php if( '' !== $this->options['list'] ) { ?>
				<h2><?php _e( 'Status', 'mailchimp-for-wp' ); ?></h2>

				<?php if( $this->options['enabled'] ) { ?>
					<p><?php _e( 'Right now, the plugin is listening to changes in your users and will automatically keep your userbase synced with the selected MailChimp list.', 'mailchimp-sync' ); ?></p>
				<?php } else { ?>
					<p><?php _e( 'The plugin is currently not listening to any changes in your users.', 'mailchimp-sync' ); ?></p>
				<?php } ?>

				<table class="form-table">
					<tr valign="top">
						<th scope="row" style="min-width: 250px;">
							<?php _e( 'Status', 'mailchimp-sync' ); ?>
						</th>
						<td>
							<?php
							if( $status_indicator->status ) {
								echo '<span class="status positive">' . __( 'IN SYNC', 'mailchimp-sync' ) . '</span>';
							} else {
								echo '<span class="status negative">' . __( 'OUT OF SYNC', 'mailchimp-sync' ) . '</span>';
							} ?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<?php _e( 'Subscribed Users / Total Users', 'mailchimp-sync' ); ?>
						</th>
						<td>
							<?php echo $status_indicator->subscriber_count . '/' . $status_indicator->user_count; ?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<?php _e( 'Force Synchronisation', 'mailchimp-sync' ) ; ?>
						</th>
						<td id="wizard">
							<?php _e( 'Please enable JavaScript to use the Synchronisation Wizard.', 'mailchimp-sync' ); ?>
						</td>
					</tr>

				</table>

			<?php } ?>

			<div class="notice inline notice-info">
				<p><?php printf( __( 'Need some help debugging? Take a look at the <a href="%s">debug log</a>.', 'mailchimp-sync' ), admin_url( 'admin.php?page=mailchimp-for-wp-other' ) ); ?></p>
			</div>

		<!-- / Main Content -->
		</div>

		<!-- Sidebar -->
		<div class="sidebar col col-2">
			<?php include MC4WP_PLUGIN_DIR . '/includes/views/parts/admin-sidebar.php'; ?>
		</div>

	<!-- / Row -->
	</div>

	<?php
	/**
	 * @ignore
	 */
	do_action( 'mc4wp_admin_footer' );
	?>

</div>