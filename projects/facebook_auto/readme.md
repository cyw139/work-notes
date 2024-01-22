# facebook_auto
## 1、常用操作
## 1.1、数据库操作
- migrate
- [ ] migrate:fresh          Drop all tables and re-run all migrations
- [ ] migrate:install        Create the migration repository
- [ ] migrate:refresh        Reset and re-run all migrations
- [ ] migrate:reset          Rollback all database migrations
- [ ] migrate:rollback       Rollback the last database migration
- [ ] migrate:status         Show the status of each migration

php artisan make:migration create_user --path=database/migrations/dcl
php artisan migrate --path=database/migrations/dcl
php artisan migrate:rollback --path=database/migrations/dcl
## 1.2、创建账号
php artisan tinker
App\Models\FacebookAuto\UserModel::create(["name"=>"insight","real_name"=>"insight","password"=>bcrypt("123456")]);

## 1.3、postman 配置

pm.request.headers.remove("x-sec-token")
pm.request.headers.remove("x-token")
pm.request.headers.add({
key: "x-token",
value: "{{token}}"
})
pm.request.headers.add({
key: "x-sec-token",
value: "{{sec_token1}}"
})
