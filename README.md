# Advanced Math Plugin for Typecho

一个专注于 MathJax 数学公式渲染的 Typecho 插件，提供完整、稳定、高性能的 LaTeX 数学公式渲染解决方案。

## 功能特性

- ✅ **完整的 MathJax 3.x 支持** - 完整的 LaTeX 数学公式渲染
- ✅ **扩展包管理** - 可配置的 MathJax 扩展包
- ✅ **多 CDN 支持** - jsDelivr、UNPKG、国内镜像
- ✅ **短代码支持** - 灵活的公式渲染控制
- ✅ **Pjax 兼容** - 单页应用无缝支持
- ✅ **自定义宏定义** - 支持用户自定义 LaTeX 命令

## 安装方法

1. 下载插件并解压到 `/usr/plugins/` 目录
2. 将文件夹重命名为 `AdvancedMath`
3. 在 Typecho 后台启用插件
4. 进入插件设置，根据需求配置相关选项

## 配置说明

### CDN 设置

- **CDN 源选择**: 选择 MathJax 资源加载的 CDN 服务商
    - jsDelivr (推荐)
    - UNPKG
    - 国内镜像 (BootCDN)

### MathJax 扩展包

选择需要加载的 MathJax 扩展包：

- **基础数学** - 核心数学符号和函数
- **AMS 符号** - 美国数学学会标准符号
- **粗体符号** - 粗体数学符号支持
- **颜色支持** - 公式颜色渲染
- **物理符号** - 物理公式和符号扩展
- **化学公式** - 化学方程式支持
- **向量矩阵** - 向量、矩阵和粗体符号
- **自定义命令** - 用户自定义 LaTeX 命令
- **自动加载** - 自动检测和加载所需包
- **错误抑制** - 优雅处理语法错误
- **未定义处理** - 处理未定义命令

### 高级功能

- **短代码支持** - 启用 `[nomath]` 等短代码控制
- **Pjax 兼容** - 支持单页应用动态重新渲染
- **调试模式** - 在控制台输出调试信息

### 自定义配置

- **自定义 LaTeX 宏** - 每行一个自定义宏，格式：`\newcommand{\macro}{definition}`
- **自定义 MathJax CDN** - 留空使用默认 CDN，可在此覆盖 MathJax 链接

## 使用示例

### 基础数学公式

latex

```
行内公式：$E = mc^2$

块级公式：
$$
\int_{-\infty}^{\infty} e^{-x^2} dx = \sqrt{\pi}
$$
```



### 复杂数学公式

latex

```
矩阵运算：
$$
\begin{pmatrix}
a & b \\
c & d
\end{pmatrix}
\begin{pmatrix}
x \\
y
\end{pmatrix}
=
\begin{pmatrix}
ax + by \\
cx + dy
\end{pmatrix}
$$

多行公式：
\begin{align}
\nabla \times \vec{E} &= -\frac{\partial \vec{B}}{\partial t} \\
\nabla \times \vec{H} &= \vec{J} + \frac{\partial \vec{D}}{\partial t} \\
\nabla \cdot \vec{D} &= \rho \\
\nabla \cdot \vec{B} &= 0
\end{align}
```



### 物理和工程公式

latex

```
流体力学：$\dot{m}_w = \frac{Q_{core} \eta_{heat}}{\Delta h_w + \Delta h_{non-ideal}}$

空气动力学：$\vec{q}_{aero} = 0.5 \rho_{\infty} v_{\infty}^2 \Delta C_p(\text{Ma})$

导航系统：$\mathbf{P}_{INS}/\mathbf{P}_{GNSS}$
```



### 短代码使用

- `[nomath]` - 临时禁用数学渲染，适合在需要显示原始 LaTeX 代码时使用

示例：

text

```
[nomath]
这里的内容不会被 MathJax 渲染：$E = mc^2$
公式会显示为：`E = mc^2`
```



## 性能特点

- **强制加载** - 默认强制加载 MathJax，确保在各种页面类型下都能正确渲染
- **轻量级** - 专注于数学公式渲染，代码简洁高效
- **兼容性好** - 支持各种 Typecho 主题和浏览器

## 故障排除

### 常见问题

1. **公式不显示**
    - 检查 MathJax 是否启用
    - 确认 CDN 链接可访问
    - 开启调试模式查看错误信息
2. **行内公式渲染异常**
    - 确保使用正确的分隔符 `$...$` 或 `\(...\)`
    - 检查是否有语法错误
3. **扩展包不生效**
    - 在插件设置中确认已选择相应的扩展包
    - 清除浏览器缓存重新加载

### 获取帮助

如遇问题，请：

1. 开启调试模式查看详细错误
2. 检查浏览器控制台输出
3. 确保使用最新版本插件

## 更新日志

### v1.0.2

- 专注于 MathJax 数学公式渲染
- 移除 Mermaid 和 TikZ 相关功能
- 简化配置选项，默认强制加载
- 使用经过验证的简化 MathJax 配置

### v1.0.0

- 初始版本发布
- 完整的 MathJax 支持
- 扩展包管理和自定义宏定义

## 许可证

MIT License

## 技术支持

如有问题或建议，请访问项目主页或提交 Issue。