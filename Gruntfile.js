'use strict';
module.exports = function( grunt ) {

    // load all grunt tasks matching the `grunt-*` pattern
    // Ref. https://npmjs.org/package/load-grunt-tasks
    require( 'load-grunt-tasks' )( grunt );

    grunt.initConfig( {
        // watch for changes and trigger sass, jshint, uglify and livereload
        watch: {
            sass: {
                files: [ 'includes/assets/css/sass/**/*.{scss,sass}' ],
                tasks: [ 'sass' ]
            },
            autoprefixer: {
                files: [ 'includes/assets/css/*.css' ],
                tasks: [ 'autoprefixer' ]
            },
            js: {
                files: '<%= uglify.frontend.src %>',
                tasks: [ 'uglify' ]
            },
            livereload: {
                // Here we watch the files the sass task will compile to
                // These files are sent to the live reload server after sass compiles to them
                options: { livereload: true },
                files: [ '*.php', '*.css' ]
            }
        },
        // sass
        sass: {
            dist: {
                options: {
                    style: 'expanded',
                    sourcemap: 'none'
                },
                files: {
                    'includes/assets/css/bpps.css': 'includes/assets/css/sass/bpps.scss'
                }
            },
            minify: {
                options: {
                    style: 'compressed',
                    sourcemap: 'none'
                },
                files: {
                    'includes/assets/css/bpps.min.css': 'includes/assets/css/sass/bpps.scss'
                }
            }
        },
        // autoprefixer
        autoprefixer: {
            dist: {
                options: {
                    browsers: [ 'last 2 versions', 'ie 9', 'ios 6', 'android 4' ],
                    expand: true, flatten: true
                },
                files: {
                    'includes/assets/css/bpps.css': 'includes/assets/css/bpps.css',
                    'includes/assets/css/bpps.min.css': 'includes/assets/css/bpps.min.css'
                }
            }
        },
        // Uglify Ref. https://npmjs.org/package/grunt-contrib-uglify
        uglify: {
            options: {
                banner: '/*! \n * BP Profile Status JavaScript Library \n * @package BP Profile Status \n */'
            },
            frontend: {
                src: [
                    'includes/assets/js/bpps.js'
                ],
                dest: 'includes/assets/js/bpps.min.js'
            }
        },
        checktextdomain: {
            options: {
                text_domain: 'bp-profile-status', //Specify allowed domain(s)
                keywords: [ //List keyword specifications
                    '__:1,2d',
                    '_e:1,2d',
                    '_x:1,2c,3d',
                    'esc_html__:1,2d',
                    'esc_html_e:1,2d',
                    'esc_html_x:1,2c,3d',
                    'esc_attr__:1,2d',
                    'esc_attr_e:1,2d',
                    'esc_attr_x:1,2c,3d',
                    '_ex:1,2c,3d',
                    '_n:1,2,4d',
                    '_nx:1,2,4c,5d',
                    '_n_noop:1,2,3d',
                    '_nx_noop:1,2,3c,4d'
                ]
            },
            target: {
                files: [ {
                        src: [
                            '*.php',
                            '**/*.php',
                            '!node_modules/**',
                            '!tests/**'
                        ], //all php
                        expand: true
                    } ]
            }
        },
        makepot: {
            target: {
                options: {
                    cwd: '.', // Directory of files to internationalize.
                    domainPath: 'languages/', // Where to save the POT file.
                    exclude: [ 'node_modules/*' ], // List of files or directories to ignore.
                    mainFile: 'index.php', // Main project file.
                    potFilename: 'bp-profile-status.pot', // Name of the POT file.
                    potHeaders: { // Headers to add to the generated POT file.
                        poedit: true, // Includes common Poedit headers.
                        'Last-Translator': 'Sanket <sanket.parmar11@gmail.com>',
                        'Language-Team': 'Sanket <sanket.parmar11@gmail.com>',
                        'report-msgid-bugs-to': '',
                        'x-poedit-keywordslist': true // Include a list of all possible gettext functions.
                    },
                    type: 'wp-plugin', // Type of project (wp-plugin or wp-theme).
                    updateTimestamp: true // Whether the POT-Creation-Date should be updated without other changes.
                }
            }
        }

    } );
    // register task
    grunt.registerTask( 'default', [ 'sass', 'autoprefixer', 'uglify', 'checktextdomain', 'makepot', 'watch' ] );
};