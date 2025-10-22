/**
 * Advanced Math Plugin 兼容性检测
 * 检测浏览器兼容性并报告问题
 */

class AdvancedMathCompatibility {
    constructor() {
        this.features = this.detectFeatures();
        this.issues = this.checkCompatibility();
        this.reportIssues();
    }
    
    detectFeatures() {
        return {
            // 浏览器特性
            intersectionObserver: 'IntersectionObserver' in window,
            promise: 'Promise' in window,
            es6: typeof Symbol !== 'undefined' && typeof Map !== 'undefined',
            modules: 'noModule' in HTMLScriptElement.prototype,
            
            // MathJax 相关
            mathjax: typeof window.MathJax !== 'undefined',
            mermaid: typeof window.mermaid !== 'undefined',
            tikzjax: typeof window.tikzjax !== 'undefined',
            
            // 性能 API
            performance: 'performance' in window && 'now' in performance,
            performanceObserver: 'PerformanceObserver' in window,
            
            // 网络特性
            beacon: 'sendBeacon' in navigator,
            
            // CSS 特性
            cssGrid: 'grid' in document.documentElement.style,
            cssVariables: '--test' in document.documentElement.style
        };
    }
    
    checkCompatibility() {
        const issues = [];
        
        if (!this.features.intersectionObserver) {
            issues.push('IntersectionObserver not supported - lazy loading disabled');
        }
        
        if (!this.features.promise) {
            issues.push('Promise not supported - some features may not work');
        }
        
        if (!this.features.modules && document.querySelector('script[type="module"]')) {
            issues.push('ES6 modules not supported - Mermaid may not load');
        }
        
        // 检查资源加载状态
        setTimeout(() => {
            if (!this.features.mathjax && document.querySelector('.MathJax')) {
                issues.push('MathJax failed to load');
            }
            
            if (!this.features.mermaid && document.querySelector('.mermaid')) {
                issues.push('Mermaid failed to load');
            }
        }, 3000);
        
        return issues;
    }
    
    reportIssues() {
        if (this.issues.length > 0) {
            console.group('⚠️ Advanced Math Plugin Compatibility Issues');
            this.issues.forEach(issue => console.warn(issue));
            console.groupEnd();
        }
        
        // 显示用户可见的警告（可选）
        if (this.issues.length > 2 && !sessionStorage.getItem('mathPluginWarningShown')) {
            this.showUserWarning();
        }
    }
    
    showUserWarning() {
        const warning = document.createElement('div');
        warning.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 4px;
            padding: 12px;
            max-width: 300px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            z-index: 10000;
            font-family: system-ui, sans-serif;
            font-size: 14px;
        `;
        
        warning.innerHTML = `
            <strong>渲染兼容性提示</strong>
            <p style="margin: 8px 0; line-height: 1.4;">当前浏览器对某些高级功能支持有限，部分数学公式或图表可能无法正常显示。</p>
            <button onclick="this.parentElement.remove(); sessionStorage.setItem('mathPluginWarningShown', 'true')" style="
                background: #6c757d;
                color: white;
                border: none;
                padding: 4px 8px;
                border-radius: 3px;
                cursor: pointer;
                margin-top: 8px;
            ">知道了</button>
        `;
        
        document.body.appendChild(warning);
        
        // 10秒后自动隐藏
        setTimeout(() => {
            if (warning.parentElement) {
                warning.remove();
                sessionStorage.setItem('mathPluginWarningShown', 'true');
            }
        }, 10000);
    }
}

// 自动初始化兼容性检测
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        new AdvancedMathCompatibility();
    });
} else {
    new AdvancedMathCompatibility();
}