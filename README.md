# shorturl 基于阿里云云函数的短链接系统

一款轻量级的 Serverless 短链接系统，直接部署到阿里云函数计算，无需独立购买服务器。

## 安装部署

1. 安装 [Serverless Dev](https://serverless-devs.com) 并登录你的阿里云账号
2. 创建数据库，并按 `db.sql` 创建表
3. 根据你的实际情况，配置 `s.yaml` 和 `config.php`
4. 安装 php 依赖。可以使用 `s build --use-sandbox` 或者手动 composer install 都行
5. 执行 `s deploy` 开始部署
6. 在阿里云函数计算中为此云函数配置自定义域名
7. 完成