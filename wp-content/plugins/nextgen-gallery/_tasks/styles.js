const sass = require('gulp-sass')(require('sass'));

const sassFiles = [
	'assets/scss/about.scss',
	'assets/scss/admin.scss',
];

module.exports = function(gulp, plugins) {
	return function() {
		let stream = gulp
			.src(sassFiles, { base: './' })
			.pipe(
				sass({ outputStyle: 'compressed' }) //nested
					.on('error', sass.logError),
			)
			.pipe(
				plugins.rename(function(path) {
					console.log(path);
					path.dirname = path.dirname.replace('scss', 'css');
					return path;
				}),
			)
			.pipe(gulp.dest('./'))
		return stream;
	};
};
