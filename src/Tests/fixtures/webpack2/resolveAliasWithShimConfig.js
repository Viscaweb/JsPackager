'use strict';

const outputConfig = {
    "filename": "[name].dist.js",
    "chunkFilename": "[id].dist.js",
    "path": "",
    "publicPath": "/"
};

const entriesConfig = {};

const aliasConfig = {};
aliasConfig['jquery'] = "js/vendor/jquery.min.js";
aliasConfig['bootstrap'] = "js/vendor/bootstrap.min.js";

const loadersConfig = [];

const pluginsConfig = [];

module.exports = {
    entry: entriesConfig,
    output: outputConfig,
    resolve: {
        alias: aliasConfig
    },
    module: {
        loaders: loadersConfig
    },
    plugins: pluginsConfig
};