'use strict';

// Import `src` and `dest` from gulp for use in the task.
const { src, dest } = require( 'gulp' );

// ==================================================
// Supported Packages
var fs    = require( 'fs' ),
    pump    = require( 'pump' ),
    cleaner = require( 'del' )
;

var gulp       = require( 'gulp' ),
    sass         = require( 'gulp-sass' ),
    autoprefixer = require( 'gulp-autoprefixer' ),
    cleanCSS     = require( 'gulp-clean-css' ),
    clean        = require( 'gulp-clean' ),
    eslint       = require( 'gulp-eslint' ),
    uglify       = require( 'gulp-uglify-es' ).default,
    sourcemaps   = require( 'gulp-sourcemaps' ),
    concat       = require( 'gulp-concat' ),
    rename       = require( 'gulp-rename' ),
    replace      = require( 'gulp-replace' ),
    notify       = require( 'gulp-notify' ),
    wpPot        = require( 'gulp-wp-pot' ),
    zip          = require( 'gulp-zip' ),
    babel        = require( 'gulp-babel' )
;

// Get package.json file
var pckg = JSON.parse( fs.readFileSync( './package.json' ) );

// ==================================================
// Variables

// Localize strings
var strings = [
    'broken-link-checker.php',
    'uninstall.php',
    'core/*.php',
    'includes/**/*.php',
    'includes/*.php',
    'modules/**/*.php',
    'modules/*.php',
    'idn/*.php'
];

// ==================================================
// Paths

// Main locations
var folder = {
    js:     'assets/js/',
    css:    'assets/css/',
    scss:   'assets/scss/',
    lang:   'languages/',
    builds: 'builds/',
    root:   'builds/broken-link-checker'
};

// BLC Package list
var blc = [
    '*',
    '**',
    '!.git',
    '!.gitattributes',
    '!.gitignore',
    '!.gitmodules',
    '!.sass-cache',
    '!DS_Store',
    '!bitbucket-pipelines.yml',
    '!composer.json',
    '!composer.lock',
    '!composer.phar',
    '!createzip.bat',
    '!createzip.sh',
    '!package.json',
    '!package-lock.json',
    '!webpack.config.js.off',
    '!postcss.config.js',
    '!Gulpfile.js',
    '!README.md',
    '!.vscode/*',
    '!.vscode',
    '!builds/**',
    '!builds/*',
    '!builds',
    '!node_modules/**',
    '!node_modules/*',
    '!node_modules',
    '!nbproject',
    '!nbproject/*',
    '!nbproject/**',
    '!phpcs.ruleset.xml'
];

// ==================================================
// Packaging Tasks

// Task: Create language files
gulp.task( 'makepot', function() {
    return gulp.src( strings )
        .pipe( wpPot({
            package: 'Broken Link Checker ' + pckg.version
        }) )
        .pipe( gulp.dest( folder.lang + 'broken-link-checker.pot' ) )
        .pipe( notify({
            message: 'Localized strings extracted',
            onLast: true
        }) )
        ;
});

gulp.task( 'copy', function() {
    return gulp.src( blc )
        .pipe( gulp.dest( folder.root ) );
});

gulp.task( 'clean', function() {
    return cleaner( folder.root, { force: true } );
});

gulp.task( 'zipit', function() {
    var name  = pckg.name,
        version = pckg.version,
        file    = name + '-' + version + '.zip';

    var rep_args = { skipBinary: true };

    // Clean up existing zip file
    cleaner( folder.builds + file, { force: true } );

    return gulp.src( folder.root + '*/**' )
        .pipe( zip( file ) )
        .pipe( gulp.dest( folder.builds ) )
        .pipe( notify({
                message: 'Broken Link Checker ' + version + ' compressed',
                onLast: true
            })
        );
});

// Task: Pack plugin
gulp.task( 'build', gulp.series(
        'makepot',
        'copy',
        'zipit',
        'clean'
    )
);