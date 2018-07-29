<?php
/**
 * class-affiliates-rankings.php
 *
 * Copyright (c) "gtsiokos" George Tsiokos www.netpad.gr
 *
 * This code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
 *
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This header and all notices must be kept intact.
 *
 * @author gtsiokos
 * @package affiliates-rankings
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Affiliates_Rankings {

	/**
	 * Initializes the class
	 */
	public static function init() {
		add_action( 'admin_notices', array( __CLASS__, 'admin_notices' ) );
		if ( self::check_dependencies() ) {
			self::add_ranking_groups();write_log('ok');
		}
	}

	/**
	 * Admin messages.
	 *
	 * @static
	 * @access private
	 *
	 * @var array
	 */
	private static $admin_messages = array();

	/**
	 * Ranking Groups names
	 *
	 * @static
	 * @access private
	 *
	 * @var array
	 */
	private static $ranking_groups = array(
		'Rank 1',
		'Rank 2',
		'Rank 3',
		'Rank 4',
		'Rank 5',
		'Rank 6'
	);

	/**
	 * Prints admin notices.
	 */
	public static function admin_notices() {
		if ( !empty( self::$admin_messages ) ) {
			foreach ( self::$admin_messages as $msg ) {
				echo wp_kses(
					$msg,
					array(
						'strong' => array(),
						'div' => array( 'class' ),
						'a' => array(
							'href'   => array(),
							'target' => array( '_blank' )
						),
						'div' => array(
							'class' => array()
						),
					)
				);
			}
		}
	}

	/**
	 * Checks plugin dependencies
	 *
	 * @return boolean|string
	 */
	public static function check_dependencies() {

		$result = true;
		$active_plugins = get_option( 'active_plugins', array() );
		$groups_is_active = in_array( 'groups/groups.php', $active_plugins );
		$affiliates_is_active = in_array( 'affiliates-pro/affiliates-pro.php', $active_plugins ) || in_array( 'affiliates-enterprise/affiliates-enterprise.php', $active_plugins );

		if ( !$groups_is_active ) {
			self::$admin_messages[] .= '<div class="error"><strong>Affiliates Rankings</strong> plugin requires <a href="http://www.wordpress.org/groups/">Groups</a> plugin to be installed and activated.</div>';
			$result = false;
		}
		if ( !$affiliates_is_active ) {
			self::$admin_messages[] .= '<div class="error"><strong>Affiliates Rankings</strong> plugin requires one of the <a href="http://www.itthinx.com/shop/affiliates-pro/">Affiliates Pro</a> or <a href="http://www.itthinx.com/shop/affiliates-enterprise/">Affiliates Enterprise</a> plugins to be installed and activated.</div>';
			$result = false;
		}
		if ( 
			!(
				defined( 'AFFILIATES_EXT_VERSION' ) &&
				version_compare( AFFILIATES_EXT_VERSION, '3.0.0' ) >= 0 &&
				class_exists( 'Affiliates_Referral' ) &&
				(
					!defined( 'Affiliates_Referral::DEFAULT_REFERRAL_CALCULATION_KEY' ) ||
					!get_option( Affiliates_Referral::DEFAULT_REFERRAL_CALCULATION_KEY, null )
				)
			)
		) {
			self::$admin_messages[] .= '<div class="error"><strong>Affiliates Rankings</strong> plugin requires Rates to be selected in Affiliates > Settings, under <strong>Commissions</strong> tab.</div>';
			$result = false;
		}

		return $result;
	}

	/**
	 * Check if Ranking Groups exist
	 * and add them accordingly 
	 */
	public static function add_ranking_groups() {
		$groups = self::$ranking_groups;
		if ( class_exists( 'Groups_Group' ) ) {
			foreach ( $groups as $group ) {
				if ( !Groups_Group::read_by_name( $group ) ) {
					$group_id = Groups_Group::create( array( $group ) );write_log( $group_id ? $group_id : 'false');
				}
			}
		}
	}

	public static function get_ranking_groups() {
		return apply_filters( 'affiliates_ranking_groups_names', self::$ranking_groups );
	}
}Affiliates_Rankings::init();
