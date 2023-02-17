const path = require('path');
let webpack = require('webpack');

module.exports = {
    entry: './public/src/js/index.js',
    target: 'web',
    output: {
        filename: 'bundle.js',
        path: path.resolve(__dirname, 'public/build'),
    },
    module: {
        rules: [
            {
                test: /\.less$/i,
                use:
                    [
                        {
                            loader: 'style-loader'
                        },
                        {
                            loader: 'css-loader'
                        },
                        {
                            loader: 'less-loader',
                            options: {
                                sourceMap: true,
                            },
                        }
                    ]
            },
            {
                test: /\.css$/i,
                // use: [MiniCssExtractPlugin.loader,'style-loader', 'css-loader'],
                use: ['style-loader', 'css-loader'],
            },
            {
                test: /\.svg$/,
                use: ['svg-loader']
            },

            {
                test: /\.(png|woff|woff2|eot|ttf|svg)$/,
                use: ['url-loader?limit=100000']
            },
            {
                test: /\.(png|jpe?g|gif|svg|eot|ttf|woff|woff2)$/i,
                loader: 'url-loader',
                options: {
                    limit: 8192,
                },
            },
            {
                test: /\.(png|jpe?g|gif|svg|eot|ttf|woff|woff2)$/i,
                loader: 'url-loader',
                options: {
                    limit: 8192,
                },
            },

        ],
    },
    plugins: [
        new webpack.ProvidePlugin({
            $: 'jquery',
            jQuery: 'jquery',
            'window.jQuery': 'jquery'
        }),
    ],
};