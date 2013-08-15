module.exports = function(grunt) {
	'use strict';

	grunt.initConfig({

		// Data from package.json
		pkg: grunt.file.readJSON('package.json'),

		// CSS concatenation and minification
		cssmin: {
			themes: {
				options: {
					banner: '/*! <%= pkg.title %> <%= pkg.version %> - Magnific Popup CSS */'
				},
				files: {
					'css/magnific-popup.min.css': ['css/magnific-popup.css']
				}
			}
		}

	});

	// Load tasks
	grunt.loadNpmTasks('grunt-contrib-cssmin');

	// Register task
	grunt.registerTask('default', [
		'cssmin'
	]);

};