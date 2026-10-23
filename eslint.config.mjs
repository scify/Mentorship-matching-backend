import js from "@eslint/js";
import globals from "globals";

export default [
	{ ignores: ["public/**", "vendor/**", "node_modules/**", "storage/**", "e2e/**"] },
	js.configs.recommended,
	{
		languageOptions: {
			ecmaVersion: "latest",
			sourceType: "module",
			globals: globals.browser,
		},
	},
	{ files: ["vite.config.mjs", "eslint.config.mjs"], languageOptions: { globals: globals.node } },
];
