<?php
/**
 * Ninja Forms Dependency
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2024 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Extensions\NinjaForms
 */

namespace Pronamic\WordPress\Pay\Extensions\NinjaForms;

use Pronamic\WordPress\Pay\Dependencies\Dependency;

/**
 * Ninja Forms Dependency
 *
 * @author  Reüel van der Steege
 * @version 1.1.1
 * @since   1.1.1
 */
class NinjaFormsDependency extends Dependency {
	/**
	 * Is met.
	 *
	 * @link https://git.saturdaydrive.io/_/ninja-forms/ninja-forms/blob/3.4.24.1/ninja-forms.php#L55
	 * @return bool True if dependency is met, false otherwise.
	 */
	public function is_met() {
		if ( ! \class_exists( '\Ninja_Forms' ) ) {
			return false;
		}

		return true;
	}
}
