# RandomFood

Website chọn món ăn ngẫu nhiên, viết bằng PHP theo mô hình MVC, lưu dữ liệu bằng MySQL và dùng CSS responsive. Không cần bước build hay cài thư viện bên ngoài.

## Cấu trúc MVC

- `index.php`: front controller, khởi tạo session và nhận request.
- `controllers/AppController.php`: định tuyến, kiểm tra CSRF, xử lý biểu mẫu và điều phối model/view.
- `models/FoodModel.php`: lọc và quản lý món ăn, danh mục, yêu thích, lịch sử, hồ sơ, cài đặt và đánh giá.
- `models/food-data.json`: dữ liệu mẫu ban đầu.
- `models/Database.php`: kết nối MySQL qua PDO và biến môi trường.
- `database/schema.sql`: cấu trúc các bảng MySQL.
- `database/seed.php`: nạp dữ liệu mẫu ban đầu vào database.
- `views/layout.php`, `views/shared.php`: bố cục và component dùng chung.
- `views/guest/`: các trang thuộc Guest Flow (Trang chủ, Khám phá, Random Food, Chi tiết món ăn, Đăng nhập, Đăng ký).
- `views/user/`: các trang thuộc User Flow (Hồ sơ cá nhân, Món yêu thích, Lịch sử random, Cài đặt).
- `views/admin/`: các trang thuộc Admin Flow (Dashboard, Quản lý món ăn, danh mục, người dùng và đánh giá).
- `styles.css`: giao diện desktop và mobile.

Website có 15 trang: Trang chủ, Khám phá món ăn, Random Food, Chi tiết món ăn, Đăng nhập, Đăng ký, Hồ sơ cá nhân, Món yêu thích, Lịch sử random, Dashboard Admin, Quản lý món ăn, Quản lý danh mục, Quản lý người dùng, Quản lý đánh giá và Cài đặt. Bản Figma có 14 loại màn hình; Cài đặt là trang thứ 15.

## Cấu hình database và chạy thử

Cần PHP 8.1 trở lên, MySQL và extension `pdo_mysql`. Tạo database trong MySQL hoặc phpMyAdmin:

```sql
CREATE DATABASE if0_43452788_radom_food CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Sau đó import file `database/schema.sql` vào database `if0_43452788_radom_food`. Trong PowerShell, cấu hình kết nối và nạp dữ liệu mẫu:

```powershell
$env:DB_HOST = "127.0.0.1"
$env:DB_PORT = "3306"
$env:DB_NAME = "if0_43452788_radom_food"
$env:DB_USER = "root"
$env:DB_PASSWORD = ""
php database/seed.php
php -S 127.0.0.1:8000
```

Mở `http://127.0.0.1:8000/`. Biến môi trường chỉ tồn tại trong cửa sổ PowerShell hiện tại; đặt lại chúng nếu mở terminal mới. Không lưu thông tin database trong Git hoặc mã nguồn. Dữ liệu món ăn, danh mục, tài khoản, yêu thích, lịch sử, cài đặt và đánh giá được lưu trong MySQL; khách chưa đăng nhập chỉ có yêu thích/lịch sử tạm trong session.

Đăng ký tài khoản qua website trước, sau đó chạy câu lệnh sau trong database để cấp quyền quản trị cho email đó:

```sql
UPDATE users SET role = 'admin' WHERE email = 'email-cua-ban@example.com';
```

Các tài khoản người dùng mẫu được nạp từ JSON chỉ để hiển thị trong trang quản trị, không có mật khẩu đăng nhập. Trên GitHub, vào **Settings → Secrets and variables → Actions** và tạo `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASSWORD`; có thể tạo thêm `DB_PORT` (mặc định `3306`). Lấy MySQL hostname, username và password từ trang quản lý database của InfinityFree. Workflow sẽ tạo file cấu hình runtime từ các secrets và tải file đó lên hosting; file này bị `.gitignore` loại khỏi repository. Không dùng `127.0.0.1`/`root` mặc định cho hosting và không đưa password vào Git.

## Triển khai lên InfinityFree

Workflow `.github/workflows/deploy.yml` tải nội dung repository lên `htdocs` khi có cập nhật trên `main` hoặc khi chạy thủ công trong **Actions**. Tạo thêm GitHub Actions secrets `FTP_SERVER`, `FTP_USERNAME`, `FTP_PASSWORD` theo thông tin FTP của InfinityFree. Chọn database `if0_43452788_radom_food` trên hosting, import `database/schema.sql`, rồi nạp dữ liệu mẫu một lần. Workflow sẽ dùng `DB_*` secrets để cấu hình kết nối trên hosting. PHP và extension `pdo_mysql` phải được bật cho hosting account.

Sau khi deploy, mở `https://randomfood.freepage.cc/`. InfinityFree ưu tiên `index.php` khi cả `index.php` và `index.html` cùng tồn tại.

Font chữ tải từ Google Fonts; nếu không tải được, website tự dùng font hệ thống.

## Bảng phân công

<img width="356" height="261" alt="image" src="https://github.com/user-attachments/assets/83ac64c1-0a09-4947-bfd8-2bddd7fe8995" />
