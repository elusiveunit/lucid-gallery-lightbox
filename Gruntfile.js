module.exports = function(grunt) {
	'use strict';

	grunt.initConfig({

		// Data from package.json
		pkg: grunt.file.readJSON('package.json'),

		// CSS concatenation and minification
		cssmin: {
			themes: {
				options: {
					banner: '/*! <%= pkg.title %> <%= pkg.version %> - Colorbox style */'
				},
				files: grunt.file.expandMapping(['*/colorbox.css'], 'themes/', {
					cwd: 'themes/',
					rename: function(destBase, destPath) {
						return destBase + destPath.replace(/\.css$/, '.min.css');
					}
				})
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