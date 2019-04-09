module.exports = function (grunt) {
    const sass = require('node-sass');
    require('load-grunt-tasks')(grunt, {scope: 'devDependencies'});
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        // Before generating any new files, remove any previously-created file(s).
        sass: {
            options: {
                implementation: sass,
                sourceMap: true
            },
            dist: {
                files: {
                    'themes/oxygen/css/theme.css': 'themes/oxygen/scss/main.scss'
                }
            }
        }
    });

    grunt.registerTask('default', ['sass']);
};
