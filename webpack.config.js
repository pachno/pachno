const glob = require('glob');
const path = require('path');

const webpack = require('webpack');

module.exports = {
    mode: 'development',
    entry: glob.sync('./js/**.js').reduce(function(obj, el){
        obj[path.parse(el).name] = el;
        return obj
    },{
        mainCss: './themes/oxygen/scss/main.scss'
    }),
    output: {
        filename: 'public/js/dist/pachno/[name].js',
        path: path.resolve(__dirname),
    },
    resolve: {
        alias: {
            'quill$': path.resolve(__dirname, 'node_modules/quill/quill.js'),
        },
        extensions: ['.js', '.ts', '.svg']
    },
    devtool: 'inline-source-map',
    module: {
        rules: [
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
                test: /\.scss$/,
                use: [
                    {
                        loader: 'file-loader',
                        options: {
                            name: 'themes/oxygen/css/theme.css',
                        }
                    },
                    {
                        loader: 'extract-loader'
                    },
                    {
                        loader: 'css-loader?-url'
                    },
                    {
                        loader: 'postcss-loader'
                    },
                    {
                        loader: 'sass-loader'
                    }
                ]
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
                        options: { limit: 8192 }
                    }
                ]
            },
            {
                test: /\.css$/,
                use: [
                    { loader: 'style-loader' },
                    { loader: 'css-loader' }
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
