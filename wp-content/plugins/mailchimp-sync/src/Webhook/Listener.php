<?php

namespace MC4WP\Sync\Webhook;

use MC4WP\Sync\UserRepository;
use WP_User;
use MC4WP_Debug_Log;

/**
 * Class Listener
 *
 * This class listens on your-site.com/mc4wp-sync-api/webhook-listener for MailChimp webhook events.
 *
 * Once triggered, it will look for the corresponding WP user and update it using the field map defined in the settings of the Sync plugin.
 *
 * @package MC4WP\Sync\Webhook
 * @property UserRepository $user_repository
 */
class Listener {

	/**
	 * @var string
	 */
	private $user_meta_key;

	/**
	 * @var array
	 */
	private $field_mappers;

	/**
	 * @var string
	 */
	public $url = '/mc4wp-sync-api/webhook-listener';

	/**
	 * @param string $user_meta_key
	 * @param array $field_mappers
	 */
	public function __construct( $user_meta_key, $field_mappers = array() ) {
		$this->user_meta_key = $user_meta_key;
		$this->field_mappers = $field_mappers;
	}

	/**
	 * Add hooks
	 */
	public function add_hooks() {
		add_action( 'init', array( $this, 'listen' ) );
	}

	/**
	 * Listen for webhook requests
	 */
	public function listen() {
		if( $this->is_triggered() ) {
			$this->handle();
			exit;
		}
	}

	/**
	 * Yes?
	 *
	 * @return bool
	 */
	public function is_triggered() {
		return strpos( $_SERVER['REQUEST_URI'], $this->url ) !== false;
	}

	/**
	 * Handle the request
	 *
	 * @return boolean
	 */
	public function handle() {

		$log = $this->get_log();
		define( 'MC4WP_SYNC_DOING_WEBHOOK', true );

		// no parameters = MailChimp webhook validator
		if( empty( $_REQUEST['data'] ) || empty( $_REQUEST['type'] ) ) {
			status_header( 200 );
			return true;
		}

		$data = stripslashes_deep( $_REQUEST['data'] );
		$type = (string) $_REQUEST['type'];

		// parameters but incorrect: throw error status
		if( empty( $data['web_id'] ) ) {
			status_header( 400 );
			return false;
		}

		$users = new UserRepository( $this->user_meta_key );

		// find WP user by List_ID + MailChimp ID
		$user = $users->get_user_by_mailchimp_id( $data['web_id'] );

		// filter user
		$user = apply_filters( 'mailchimp_sync_webhook_user', $user, $data );

		if( ! $user instanceof WP_User ) {
			// fire event when no user is found
			do_action( 'mailchimp_sync_webhook_no_user', $data );
			echo 'No corresponding user found for this subscriber.';

			status_header( 404 );

			// exit early
			return false;
		}

		$updated = false;

		// if user was supplied by filter, it might not have a sync key.
		// add it, just in case.
		// @todo: DRY meta key prefix
		$sync_key = 'mailchimp_sync_' . $data['list_id'];
		if( empty( $user->{$sync_key} ) ) {
			update_user_meta( $user->ID, $sync_key, $data['web_id'] );
			$updated = true;
		}

		// update user email if it's given, valid and different
		if( ! empty( $data['email'] ) && is_email( $data['email'] ) && $data['email'] !== $user->user_email ) {
			update_user_meta( $user->ID, 'user_email', $data['email'] );
			$updated = true;
		}

		// update WP user with data (use reversed field map)
		// loop through mapping rules
		foreach( $this->field_mappers as $rule ) {

			// is this field present in the request data? do not use empty here
			if( isset( $data['merges'][ $rule['mailchimp_field'] ] ) ) {

				// is scalar value?
				$value = $data['merges'][ $rule['mailchimp_field'] ];
				if( ! is_scalar( $value ) ) {
					continue;
				}

				// update user property if it changed
				// @todo Default user properties can be combined into single `wp_update_user` call for performance improvement
				if( $user->{$rule['user_field']} !== $value ) {
					update_user_meta( $user->ID, $rule['user_field'], $value );
					$updated = true;
				}
			}

		}

		if( $updated ) {
			$log->info( sprintf( "Webhook: Updated user #%d", $user->ID ) );
		}

		// fire event to allow custom actions (like deleting the user)
		do_action( 'mailchimp_sync_webhook', $data, $user );

		// fire type specific event. Example: mailchimp_sync_webhook_unsubscribe
		do_action( 'mailchimp_sync_webhook_' . $type, $data, $user );

		echo 'OK';
	}

	/**
	 * @return MC4WP_Debug_Log
	 */
	private function get_log() {
		return mc4wp('log');
	}

}