# shorturl 基于阿里云云函数的短链接系统

一款面向中小型企业的短链接系统实现方案。

## 安装

1. 创建数据库，并构建如 `db.sql` 中的 schema
2. 根据你的实际情况，配置 `config.php`
3. 将其余 php 文件放到你的云函数中
4. 在阿里云云函数中为此云函数配置自定义域名