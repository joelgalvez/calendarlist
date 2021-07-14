const proxyHost = process.env.VUE_PROXY_HOST || "localhost:8081"

module.exports =
    process.env.NODE_ENV === "production"
        ? {
            outputDir: "dist",

            publicPath: "/",
            filenameHashing: true,
            pages: {
                app: {
                    entry: "src/app/main.js",
                    template: "src/app/index.html",
                    filename: "index.html",
                    title: process.env.VUE_APP_TITLE || "Calendar List"
                }
            },
            chainWebpack: (config) => {
                config.plugins.delete("preload-app")
                config.plugins.delete("prefetch-app")
            }
        }
        : {
            pages: {
                index: {
                    entry: "src/app/main.js",
                    template: "src/app/index.html",
                    filename: "index.html",
                    title: process.env.VUE_APP_TITLE || "Calendar List"
                }
            },
            configureWebpack: {
                devServer: {
                    proxy: {
                        "^/calendars.json": {
                            target: `http://${proxyHost}`,
                            secure: false,
                            changeOrigin: true,
                        },
                        "^/proxy.php": {
                            target: `http://${proxyHost}`,
                            secure: false,
                            changeOrigin: true,
                        }
                    }
                }
            }
        }
