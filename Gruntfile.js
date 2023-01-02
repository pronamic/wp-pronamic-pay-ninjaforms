/**
 * Grunt
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

module.exports = function( grunt ) {
	require( 'load-grunt-tasks' )( grunt );

	// Project configuration.
	grunt.initConfig( {
		// Package.
		pkg: grunt.file.readJSON( 'package.json' ),

		// JSHint.
		jshint: {
			all: [ 'Gruntfile.js', 'composer.json', 'package.json' ]
		},

		// PHP Code Sniffer.
		phpcs: {
			options: {
				bin: 'vendor/bin/phpcs',
				standard: 'phpcs.xml.dist',
				showSniffCodes: true
			},
			application: {
				src: [
					'**/*.php',
					'!node_modules/**',
					'!vendor/**',
					'!wordpress/**',
					'!wp-content/**'
				]
			},
		},

		// PHPLint.
		phplint: {
			all: [ 'src/**/*.php' ]
		},

		// PHP Mess Detector.
		phpmd: {
			options: {
				bin: 'vendor/bin/phpmd',
				exclude: 'node_modules',
				reportFormat: 'xml',
				rulesets: 'phpmd.ruleset.xml'
			},
			application: {
				dir: 'src'
			}
		},

		// PHPUnit.
		phpunit: {
			options: {
				bin: 'vendor/bin/phpunit'
			},
			application: {

			}
		}
	} );

	// Default task(s).
	grunt.registerTask( 'default', [ 'jshint', 'phplint', 'phpmd', 'phpcs', 'phpunit' ] );
};
