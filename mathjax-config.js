/**
 * Advanced Math Plugin - MathJax 配置备份
 * 在主配置失败时提供备用配置
 */

(function() {
    'use strict';
    
    // 只有在 MathJax 未配置时才设置备用配置
    if (typeof window.MathJax === 'undefined' || !window.MathJax.tex) {
        window.MathJax = {
            loader: {
                load: [
                    '[tex]/ams',
                    '[tex]/bm',
                    '[tex]/color',
                    '[tex]/boldsymbol',
                    '[tex]/autoload',
                    '[tex]/noerrors',
                    '[tex]/noundefined'
                ]
            },
            tex: {
                packages: {'[+]': ['ams', 'bm', 'color', 'boldsymbol', 'autoload', 'noerrors', 'noundefined']},
                inlineMath: [['$', '$'], ['\\(', '\\)']],
                displayMath: [['$$', '$$'], ['\\[', '\\]']],
                processEscapes: true,
                processEnvironments: true,
                tags: 'ams'
            },
            svg: {
                fontCache: 'global',
                scale: 1,
                displayAlign: 'center'
            },
            options: {
                skipHtmlTags: ['script', 'noscript', 'style', 'textarea', 'pre', 'code'],
                renderActions: {
                    addMenu: [0, '', '']
                }
            },
            startup: {
                pageReady: function () {
                    return MathJax.startup.defaultPageReady().then(function () {
                        console.log('MathJax 备用配置初始化完成');
                    });
                }
            }
        };
        
        console.log('Advanced Math Plugin: 使用备用 MathJax 配置');
    }
})();