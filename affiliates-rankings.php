<?php
/**
 * Plugin Name: Affiliates Rankings
 * Plugin URI: http://www.netpad.gr
 * Description: A Ranking system for Affiliates Pro or Enterprise plugins based on Groups
 * Version: 1.0
 * Author: George Tsiokos
 * Author URI: http://www.netpad.gr
 * License: GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Copyright (c) 2015-2016 "gtsiokos" George Tsiokos www.netpad.gr
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @author gtsiokos
 * @package affiliates-rankings
 * @since 1.0.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'plugins_loaded', 'ar_plugins_loaded' );

/**
 * Check plugin dependencies
 */
function ar_plugins_loaded() {
//	if (
	//	defined( 'AFFILIATES_EXT_VERSION' ) &&
		//version_compare( AFFILIATES_EXT_VERSION, '3.0.0' ) >= 0 &&
		//class_exists( 'Affiliates_Referral' ) &&
		//(
			//!defined( 'Affiliates_Referral::DEFAULT_REFERRAL_CALCULATION_KEY' ) ||
			//!get_option( Affiliates_Referral::DEFAULT_REFERRAL_CALCULATION_KEY, null )
		//)
	//) {
		require_once 'class-affiliates-rankings.php';
	//}
}
