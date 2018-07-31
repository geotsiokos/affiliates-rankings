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

/**
 * Class Affiliates Rankings
 */
class Affiliates_Rankings {

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
	 * Ranking Conditions per Rank
	 *
	 * @static
	 * @access private
	 *
	 * @var array
	 */
	private static $ranking_conditions = array(
		10,
		20,
		30,
		40,
		50,
		60
	);

	/**
	 * Initializes the class
	 */
	public static function init() {
		add_action( 'admin_notices', array( __CLASS__, 'admin_notices' ) );
		if ( self::check_dependencies() ) {
			self::add_ranking_groups();
			add_action( 'affiliates_added_affiliate', array( __CLASS__, 'affiliates_added_affiliate' ) );
			add_action( 'affiliates_referral', array( __CLASS__, 'affiliates_referral' ), 10, 2 );
		}
	}

	/**
	 * Add new affiliate to first ranking group
	 *
	 * @param int $affiliate_id
	 */
	public static function affiliates_added_affiliate( $affiliate_id ) {
		$groups = self::get_ranking_groups();
		$user_id = affiliates_get_affiliate_user( $affiliate_id );
		$user = get_user_by( 'ID', $user_id );
		if ( $user ) {
			$group = Groups_Group::read_by_name( $groups[0] );
			if ( $group ) {
				if ( !Groups_User_Group::read( $user_id , $group->group_id ) ) {
					Groups_User_Group::create( array(
						'user_id' => $user_id,
						'group_id' => $group->group_id
					) );
				}
			}
		}
	}

	/**
	 * Promote affiliate to a higher rank
	 *
	 * @param int $referral_id
	 * @param array $params
	 */
	public static function affiliates_referral( $referral_id, $params ) {
		$ranking_groups = self::get_ranking_groups();
		$ranking_conditions = self::get_ranking_conditions();
		$affiliate_id = $params['affiliate_id'];

		$user_id = affiliates_get_affiliate_user( $affiliate_id );
		$user = get_user_by( 'ID', $user_id );
		if ( $user ) {
			$current_rank = self::get_affiliate_rank( $affiliate_id );
			$max_rank_key = count( $ranking_groups ) - 1;

			// @todo replace $affiliate_referrals calculation
			// with a pluggable ranking factor
			$affiliate_referrals = affiliates_get_affiliate_referrals( $affiliate_id );
			$current_ranking_key = array_search( $current_rank, $ranking_groups );
			if ( $current_ranking_key ) {

				// Affiliate hasn't reached the maximum Rank
				if ( $current_ranking_key < $max_rank_key ) {
					if ( $affiliate_referrals > $ranking_conditions[$current_ranking_key] ) {

						// Remove from current group
						$current_ranking_group = Groups_Group::read_by_name( $ranking_groups[$current_ranking_key] );
						if ( $current_ranking_group ) {
							Groups_User_Group::delete( $user_id, $current_ranking_group->group_id );
						}
						// Add to next ranking group
						$next_ranking_key = $current_ranking_key + 1;
						$next_ranking_group = Groups_Group::read_by_name( $ranking_groups[$promotion_ranking_key] );
						if ( $next_ranking_group ) {
							Groups_User_Group::create( array(
								'user_id' => $user_id,
								'group_id' => $next_ranking_group->group_id
							) );
						}
					}
				}
			}
		}
	}

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
		// We need Rates to be used as Commission method
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
		$groups = self::get_ranking_groups();
		if ( class_exists( 'Groups_Group' ) ) {
			foreach ( $groups as $group ) {
				if ( !Groups_Group::read_by_name( $group ) ) {
					$group_id = Groups_Group::create( array( 'name' => $group ) );
				}
			}
		}
	}

	/**
	 * A filter hook for groups names
	 *
	 * @return array
	 */
	public static function get_ranking_groups() {
		return apply_filters( 'affiliates_ranking_groups_names', self::$ranking_groups );
	}

	/**
	 * A filter hook for ranking conditions
	 *
	 * @return mixed
	 */
	public static function get_ranking_conditions() {
		return apply_filters( 'affiliates_ranking_conditions', self::$ranking_conditions );
	}

	/**
	 * Get active affiliates
	 *
	 * @return array|NULL
	 */
	private static function get_affiliates() {
		return affiliates_get_affiliates();
	}

	/**
	 * Get affiliate Rank
	 *
	 * @param int $aff_id
	 * @return string group name
	 */
	private static function get_affiliate_rank( $aff_id ) {
		$ranking_groups = self::get_ranking_groups();
		// The default Rank is the first Rank
		$result = $ranking_groups[0];
		$default_rank_group = Groups_Group::read_by_name( $ranking_groups[0] );

		$user_id = affiliates_get_affiliate_user( $aff_id );
		$user = get_user_by( 'ID', $user_id );
		if ( $user ) {
			foreach ( $ranking_groups as $ranking_group ) {
				$group = Groups_Group::read_by_name( $ranking_group );
				if ( $group ) {
					if ( Groups_User_Group::read( $user_id , $group->group_id ) ) {
						$result = $group->name;
					}
				}
			}
			if ( $result == $ranking_groups[0] ) {
				Groups_User_Group::create( array(
					'user_id' => $user_id,
					'group_id' => $default_rank_group->group_id
				) );
			}
		}
		return $result;
	}

	/**
	 * Add rates for ranking groups
	 *
	 * @todo this does nothing atm
	 */
	public static function add_ranking_rates() {

	}

	/**
	 * Syncronize existing affiliates into ranks
	 *
	 * @todo does nothing atm
	 */
	public static function sync_affiliates() {

	}
}Affiliates_Rankings::init();
