module.exports = process.env.NODE_ENV === 'production' ? {
    outputDir: "../dist",
    
    publicPath: '/',
    filenameHashing: false,
    pages: {
        app: 'src/main.js'
    },
    chainWebpack: config => {
        config.plugins.delete('html-app')
        config.plugins.delete('preload-app')
        config.plugins.delete('prefetch-app')
    }
  } : {
    configureWebpack: {
      devServer: {
        proxy: {
          '^/calendars.json': {
            target: 'http://xxyyzz.xyz',
            secure: false,
            "changeOrigin": true
            },
          '^/proxy.php': {
            target: 'http://xxyyzz.xyz',
            secure: false,
            "changeOrigin": true
            }
        }
      }
    }
  }
  
  
  