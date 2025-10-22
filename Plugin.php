<?php
/**
 * Advanced Math Render Plugin for Typecho
 * 
 * 专注于 MathJax 数学公式渲染的 Typecho 插件
 * 提供完整、稳定、高性能的 LaTeX 数学公式渲染解决方案
 * 
 * @package AdvancedMath
 * @author Richard Yang
 * @version 1.0.2
 * @link https://github.com/Coder-Soder/AdvancedMath
 */

namespace TypechoPlugin\AdvancedMath;

use Typecho\Plugin\PluginInterface;
use Typecho\Widget\Helper\Form;
use Typecho\Widget\Helper\Form\Element\Checkbox;
use Typecho\Widget\Helper\Form\Element\Text;
use Typecho\Widget\Helper\Form\Element\Textarea;
use Typecho\Widget\Helper\Form\Element\Select;
use Widget\Options;

class Plugin implements PluginInterface
{
    // CDN 源配置
    const CDN_SOURCES = [
        'jsdelivr' => [
            'name' => 'jsDelivr (推荐)',
            'mathjax' => 'https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js'
        ],
        'unpkg' => [
            'name' => 'UNPKG',
            'mathjax' => 'https://unpkg.com/mathjax@3/es5/tex-mml-chtml.js'
        ],
        'china' => [
            'name' => '国内镜像 (BootCDN)',
            'mathjax' => 'https://cdn.bootcdn.net/ajax/libs/mathjax/3.2.2/es5/tex-mml-chtml.js'
        ]
    ];
    
    // MathJax 扩展包配置
    const MATHJAX_EXTENSIONS = [
        'base' => ['name' => '基础数学', 'default' => true, 'desc' => '核心数学符号和函数'],
        'ams' => ['name' => 'AMS 符号', 'default' => true, 'desc' => '美国数学学会标准符号'],
        'bm' => ['name' => '粗体符号', 'default' => true, 'desc' => '粗体数学符号支持'],
        'color' => ['name' => '颜色支持', 'default' => true, 'desc' => '公式颜色渲染'],
        'physics' => ['name' => '物理符号', 'default' => false, 'desc' => '物理公式和符号扩展'],
        'chemformula' => ['name' => '化学公式', 'default' => false, 'desc' => '化学方程式支持'],
        'boldsymbol' => ['name' => '向量矩阵', 'default' => true, 'desc' => '向量、矩阵和粗体符号'],
        'newcommand' => ['name' => '自定义命令', 'default' => false, 'desc' => '用户自定义 LaTeX 命令'],
        'autoload' => ['name' => '自动加载', 'default' => true, 'desc' => '自动检测和加载所需包'],
        'noerrors' => ['name' => '错误抑制', 'default' => true, 'desc' => '优雅处理语法错误'],
        'noundefined' => ['name' => '未定义处理', 'default' => true, 'desc' => '处理未定义命令']
    ];

    /**
     * 激活插件
     */
    public static function activate()
    {
        \Typecho\Plugin::factory('Widget_Archive')->footer = [__CLASS__, 'footer'];
        \Typecho\Plugin::factory('Widget_Abstract_Contents')->contentEx = [__CLASS__, 'parseContent'];
        \Typecho\Plugin::factory('Widget_Abstract_Contents')->excerptEx = [__CLASS__, 'parseExcerpt'];
        \Typecho\Plugin::factory('Widget_Abstract_Comments')->contentEx = [__CLASS__, 'parseContent'];
        
        return _t('Advanced Math 插件已激活，请配置相关设置。');
    }

    /**
     * 禁用插件
     */
    public static function deactivate()
    {
        return _t('Advanced Math 插件已禁用。');
    }

    /**
     * 插件配置界面
     */
    public static function config(Form $form)
    {
        // ========== CDN 与性能设置 ==========
        echo '<h2>CDN 设置</h2>';
        
        $cdnOptions = [];
        foreach (self::CDN_SOURCES as $key => $source) {
            $cdnOptions[$key] = _t($source['name']);
        }
        
        $cdnSource = new Select('cdn_source', $cdnOptions, 'jsdelivr', 
            _t('CDN 源选择'), _t('选择 MathJax 资源加载的 CDN 服务商'));
        $form->addInput($cdnSource);
        
        // ========== MathJax 扩展包设置 ==========
        echo '<h2>MathJax 扩展包</h2>';
        echo '<p>选择需要加载的 MathJax 扩展包：</p>';
        
        $extensionOptions = [];
        foreach (self::MATHJAX_EXTENSIONS as $key => $extension) {
            $extensionOptions[$key] = _t($extension['name'] . ' - ' . $extension['desc']);
        }
        
        $extensions = new Checkbox('extensions', $extensionOptions, 
            array_keys(array_filter(self::MATHJAX_EXTENSIONS, function($ext) { 
                return $ext['default']; 
            })), 
            _t('扩展包选择'));
        $form->addInput($extensions);
        
        // ========== 高级功能设置 ==========
        echo '<h2>高级功能</h2>';
        
        $shortcodeSupport = new Checkbox('shortcode_support', 
            ['1' => _t('启用短代码支持')], 
            ['1'], 
            _t('短代码支持'), 
            _t('启用 [mathjax], [nomath] 等短代码控制'));
        $form->addInput($shortcodeSupport);
        
        $pjaxSupport = new Checkbox('pjax_support', 
            ['1' => _t('启用 Pjax 兼容')], 
            ['1'], 
            _t('Pjax 兼容'), 
            _t('支持单页应用动态重新渲染'));
        $form->addInput($pjaxSupport);
        
        $debugMode = new Checkbox('debug_mode', 
            ['1' => _t('启用调试模式')], 
            [], 
            _t('调试模式'), 
            _t('在控制台输出调试信息'));
        $form->addInput($debugMode);
        
        // ========== 自定义配置 ==========
        echo '<h2>自定义配置</h2>';
        
        $customMacros = new Textarea('custom_macros', null, '', 
            _t('自定义 LaTeX 宏'), _t('每行一个自定义宏，格式：\newcommand{\\macro}{definition}'));
        $form->addInput($customMacros);
        
        $customCDN = new Text('custom_mathjax_cdn', null, '', 
            _t('自定义 MathJax CDN'), _t('留空使用默认 CDN，可在此覆盖 MathJax 链接'));
        $form->addInput($customCDN);
    }

    /**
     * 个人配置界面
     */
    public static function personalConfig(Form $form)
    {
        // 暂无个人配置需求
    }

    /**
     * 内容解析
     */
    public static function parseContent($content, $widget = null)
    {
        if (empty($content)) {
            return $content;
        }
        
        $config = Options::alloc()->plugin('AdvancedMath');
        
        // 处理短代码
        if (is_array($config->shortcodeSupport) && in_array('1', $config->shortcodeSupport)) {
            $content = self::processShortcodes($content);
        }
        
        return $content;
    }

    /**
     * 摘要解析
     */
    public static function parseExcerpt($content, $widget = null)
    {
        return self::parseContent($content, $widget);
    }

    /**
     * 处理短代码
     */
    private static function processShortcodes($content)
    {
        // [nomath] 短代码 - 临时禁用数学渲染
        if (strpos($content, '[nomath]') !== false) {
            $content = preg_replace('/\$(.*?)\$/s', '`$1`', $content);
            $content = preg_replace('/\\\\\(.*?\\\\\)/s', '`$1`', $content);
            $content = preg_replace('/\\\\\[.*?\\\\\]/s', '`$1`', $content);
            $content = str_replace('[nomath]', '', $content);
        }
        
        return $content;
    }

    /**
     * 页脚资源输出 - 强制加载 MathJax
     */
    public static function footer()
    {
        $config = Options::alloc()->plugin('AdvancedMath');
        $cdnSource = $config->cdn_source ?: 'jsdelivr';
        $cdnConfig = self::CDN_SOURCES[$cdnSource] ?? self::CDN_SOURCES['jsdelivr'];
        
        $resourceContent = '';
        
        // MathJax 资源
        $mathjaxUrl = $config->custom_mathjax_cdn ?: $cdnConfig['mathjax'];
        
        $resourceContent .= '<script>';
        $resourceContent .= self::generateSimpleMathJaxConfig($config);
        $resourceContent .= '</script>';
        $resourceContent .= '<script defer src="' . $mathjaxUrl . '"></script>';
        
        // 输出调试信息
        if (is_array($config->debugMode) && in_array('1', $config->debugMode)) {
            $debugInfo = [
                'cdn_source' => $cdnSource,
                'extensions' => self::getEnabledExtensions($config)
            ];
            $resourceContent .= '<!-- Advanced Math Debug: ' . json_encode($debugInfo) . ' -->';
        }
        
        echo $resourceContent;
    }

    /**
     * 生成简化的 MathJax 配置 - 使用之前成功的配置
     */
    private static function generateSimpleMathJaxConfig($config)
    {
        $enabledExtensions = self::getEnabledExtensions($config);
        
        // 简化的 MathJax 配置 - 使用之前验证成功的配置
        $configScript = 'window.MathJax = {';
        $configScript .= '  tex: {';
        $configScript .= '    inlineMath: [[\'$\', \'$\'], [\'\\\\(\', \'\\\\)\']],';
        $configScript .= '    displayMath: [[\'$$\', \'$$\'], [\'\\\\[\', \'\\\\]\']],';
        $configScript .= '    processEscapes: true,';
        $configScript .= '    processEnvironments: true';
        
        // 添加扩展包配置
        if (!empty($enabledExtensions)) {
            $configScript .= '    ,packages: {\'[+]\': ' . json_encode($enabledExtensions) . '}';
        }
        
        $configScript .= '  },';
        $configScript .= '  options: {';
        $configScript .= '    skipHtmlTags: [\'script\', \'noscript\', \'style\', \'textarea\', \'pre\', \'code\']';
        $configScript .= '  }';
        $configScript .= '};';
        
        // 添加调试信息
        if (is_array($config->debugMode) && in_array('1', $config->debugMode)) {
            $configScript .= 'console.log("MathJax Config Loaded:", ' . json_encode([
                'extensions' => $enabledExtensions
            ]) . ');';
        }
        
        return $configScript;
    }

    /**
     * 获取启用的扩展包
     */
    private static function getEnabledExtensions($config)
    {
        $enabled = [];
        $selected = is_array($config->extensions) ? $config->extensions : [];
        
        foreach (self::MATHJAX_EXTENSIONS as $key => $extension) {
            if (in_array($key, $selected) || $extension['default']) {
                $enabled[] = $key;
            }
        }
        
        return array_unique($enabled);
    }
}