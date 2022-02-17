module.exports = {
    extends: [
        // add more generic rulesets here, such as:
        // "eslint:recommended",
        'plugin:vue/vue3-recommended',
        // 'eslint:recommended',
        'prettier',
        'prettier/vue',
        'plugin:prettier/recommended',
        // 'plugin:vue/recommended' // Use this if you are using Vue.js 2.x.
    ],
    //parser: 'babel-eslint',
    rules: {
        // override/add rules settings here, such as:
        'vue/no-unused-vars': 'error',
        'vue/no-v-html': 'warn',
        'prettier/prettier': 'error',
        indent: ['error', 'tab'],
    },
}