# RandomFood

Website chọn món ăn ngẫu nhiên, viết bằng PHP theo mô hình MVC và dùng CSS responsive. Dự án chạy trên hosting PHP như InfinityFree, không cần bước build hay cài thư viện bên ngoài.

## Cấu trúc MVC

- `index.php`: front controller, khởi tạo session và nhận request.
- `controllers/AppController.php`: định tuyến, kiểm tra CSRF, xử lý biểu mẫu và điều phối model/view.
- `models/FoodModel.php`: lọc và quản lý món ăn, danh mục, yêu thích, lịch sử, hồ sơ, cài đặt và đánh giá.
- `models/food-data.json`: dữ liệu mẫu ban đầu.
- `views/layout.php`, `views/shared.php`: bố cục và component dùng chung.
- `views/pages/`: từng trang giao diện được tách riêng.
- `styles.css`: giao diện desktop và mobile.

Website có 15 trang: Trang chủ, Khám phá món ăn, Random Food, Chi tiết món ăn, Đăng nhập, Đăng ký, Hồ sơ cá nhân, Món yêu thích, Lịch sử random, Dashboard Admin, Quản lý món ăn, Quản lý danh mục, Quản lý người dùng, Quản lý đánh giá và Cài đặt. Bản Figma có 14 loại màn hình; Cài đặt là trang thứ 15.

## Chạy thử

Cần PHP 8.1 trở lên. Từ thư mục dự án, chạy:

```sh
php -S 127.0.0.1:8000
```

Sau đó mở `http://127.0.0.1:8000/`. Dữ liệu thao tác được giữ bằng PHP session; khi đóng session hoặc xóa cookie, dữ liệu demo có thể được đặt lại. Chưa có cơ sở dữ liệu, xác thực tài khoản thật, phân quyền admin hay đồng bộ giữa thiết bị. Không dùng chức năng demo này để bảo vệ dữ liệu thật.

## Triển khai lên InfinityFree

Workflow `.github/workflows/deploy.yml` tải nội dung repository lên `htdocs` khi có cập nhật trên `main` hoặc khi chạy thủ công trong **Actions**. Thêm các GitHub Actions secrets `FTP_SERVER`, `FTP_USERNAME`, `FTP_PASSWORD` theo thông tin FTP của InfinityFree. Đảm bảo `index.php`, `models/food-data.json`, toàn bộ thư mục `models/`, `controllers/`, `views/` và `styles.css` được tải lên. PHP phải được bật cho hosting account.

Sau khi deploy, mở `https://randomfood.freepage.cc/`. InfinityFree ưu tiên `index.php` khi cả `index.php` và `index.html` cùng tồn tại.

Font chữ tải từ Google Fonts; nếu không tải được, website tự dùng font hệ thống.

## Bảng phân công

<img width="356" height="261" alt="image" src="https://github.com/user-attachments/assets/83ac64c1-0a09-4947-bfd8-2bddd7fe8995" />
