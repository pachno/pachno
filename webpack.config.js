const glob = require('glob');
const path = require('path');

const webpack = require('webpack');

module.exports = {
    mode: 'development',
    entry: glob.sync('./js/**.js').reduce(function (obj, el) {
        obj[path.parse(el).name] = el;
        return obj
        // },{
        //     mainCss: './themes/oxygen/scss/main.scss'
    }),
    output: {
        filename: 'public/js/dist/pachno/[name].js',
        path: path.resolve(__dirname),
    },
    resolve: {
        alias: {
            '@': path.resolve(__dirname, './js'),
        },
        extensions: ['.js', '.ts', '.svg']
    },
    devtool: 'source-map',
    module: {
        rules: [
            {
                test: /\.(njk|nunjucks)$/,
                use: [
                    {loader: 'simple-nunjucks-loader', options: {searchPaths: ['js/templates']}},
                ],
            },
            {
                test: /\.m?js$/,
                exclude: /(node_modules|bower_components)/,
                use: {
                    loader: 'babel-loader',
                    options: {
                        presets: ['@babel/preset-env']
                    }
                }
            },
            {
                test: /\.svg$/,
                use: [{
                    loader: 'html-loader',
                    options: {
                        minimize: true
                    }
                }]
            },
            {
                test: /\.(png|jpg|gif|ttf|eot|woff|woff2)$/i,
                use: [
                    {
                        loader: 'url-loader',
                        options: {limit: 8192}
                    }
                ]
            },
            {
                test: /\.css$/,
                use: [
                    {loader: 'style-loader'},
                    {loader: 'css-loader?sourceMap'}
                ]
            }
        ]
    },
    plugins: [
        new webpack.ProvidePlugin({
            $: 'jquery',
            jQuery: 'jquery',
            'window.jQuery': 'jquery'
        })
    ]
};
