/**
 * Advanced Math Plugin 性能监控
 * 收集和报告插件性能指标
 */

class AdvancedMathPerformance {
    constructor() {
        this.metrics = {
            mathjaxLoad: 0,
            mermaidLoad: 0,
            tikzLoad: 0,
            totalRender: 0,
            elementCount: {
                math: 0,
                mermaid: 0,
                tikz: 0
            }
        };
        
        this.init();
    }
    
    init() {
        // 监听资源加载
        this.monitorResourceLoading();
        // 监听渲染完成
        this.monitorRendering();
        // 报告性能数据
        this.reportPerformance();
    }
    
    monitorResourceLoading() {
        if ('PerformanceObserver' in window) {
            const resourceObserver = new PerformanceObserver((list) => {
                list.getEntries().forEach((entry) => {
                    if (entry.name.includes('mathjax')) {
                        this.metrics.mathjaxLoad = entry.duration;
                    } else if (entry.name.includes('mermaid')) {
                        this.metrics.mermaidLoad = entry.duration;
                    } else if (entry.name.includes('tikz')) {
                        this.metrics.tikzLoad = entry.duration;
                    }
                });
            });
            
            try {
                resourceObserver.observe({entryTypes: ['resource']});
            } catch (e) {
                console.warn('PerformanceObserver not supported:', e);
            }
        }
    }
    
    monitorRendering() {
        // 监听 MathJax 渲染
        if (window.MathJax) {
            MathJax.startup.promise.then(() => {
                this.metrics.elementCount.math = document.querySelectorAll('.MathJax').length;
                this.recordRenderTime('mathjax');
            }).catch(err => {
                console.warn('MathJax rendering monitoring failed:', err);
            });
        }
        
        // 监听 Mermaid 渲染
        if (window.mermaid) {
            const mermaidElements = document.querySelectorAll('.mermaid');
            this.metrics.elementCount.mermaid = mermaidElements.length;
            
            if (mermaidElements.length > 0) {
                setTimeout(() => {
                    this.recordRenderTime('mermaid');
                }, 1000);
            }
        }
        
        // 监听 TikZ 渲染
        const tikzElements = document.querySelectorAll('.tikz-container');
        this.metrics.elementCount.tikz = tikzElements.length;
        
        // 监听页面完全加载
        window.addEventListener('load', () => {
            this.metrics.totalRender = performance.now() - (window.advancedMathStartTime || 0);
        });
    }
    
    recordRenderTime(type) {
        const now = performance.now();
        this.metrics[`${type}Render`] = now - (window.advancedMathStartTime || now);
    }
    
    reportPerformance() {
        window.addEventListener('load', () => {
            setTimeout(() => {
                if (window.console && console.log) {
                    console.group('🎯 Advanced Math Plugin Performance');
                    console.table(this.metrics);
                    console.groupEnd();
                }
                
                // 发送性能数据到分析服务（可选）
                this.sendMetrics();
            }, 2000);
        });
    }
    
    sendMetrics() {
        // 可以在这里发送性能数据到分析服务
        if (typeof navigator.sendBeacon === 'function') {
            try {
                const data = JSON.stringify({
                    type: 'advanced-math-performance',
                    metrics: this.metrics,
                    timestamp: Date.now(),
                    url: window.location.href
                });
                
                navigator.sendBeacon('/analytics', data);
            } catch (e) {
                // 静默失败
            }
        }
    }
}

// 自动初始化
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        new AdvancedMathPerformance();
    });
} else {
    new AdvancedMathPerformance();
}