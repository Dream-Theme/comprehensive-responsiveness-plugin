const settings = {
	extends: [
		'eslint:recommended',
		'plugin:@wordpress/eslint-plugin/recommended',
	],
	globals: {
		jQuery: true,
		IntersectionObserver: true,
	},
	rules: {
		'prettier/prettier': 'warn',
		'@wordpress/no-unsafe-wp-apis': 'off',
		camelcase: [ 'error', { allow: [ '^dt-cr_' ] } ],
	},
	settings: {
		'import/resolver': 'webpack',
	},
};

module.exports = settings;
