# RandomFood

Website chọn món ăn ngẫu nhiên bằng HTML, CSS và JavaScript thuần. Không cần backend, cơ sở dữ liệu hay bước build; phù hợp để lưu trữ trên InfinityFree.

## Chạy thử

Mở `index.html` bằng trình duyệt, hoặc chạy một web server tĩnh trong thư mục dự án.

## Tự động triển khai lên InfinityFree qua GitHub

Workflow tại `.github/workflows/deploy.yml` sẽ tải website lên `htdocs` mỗi khi có cập nhật trên nhánh `main`; cũng có thể chạy thủ công từ tab **Actions**.

Thiết lập một lần:

1. Trong control panel InfinityFree, xác nhận `randomfood.freepage.cc` đã được gắn với hosting account. Lấy hostname FTP, username và password trong phần thông tin FTP của account.
2. Trên GitHub, mở **Settings → Secrets and variables → Actions → New repository secret**. Thêm ba secret `FTP_SERVER`, `FTP_USERNAME`, và `FTP_PASSWORD` với thông tin FTP tương ứng. Không ghi thông tin đăng nhập vào source code hoặc gửi trong chat.
3. Đưa `index.html`, `styles.css`, `app.js` và workflow này lên nhánh `main` của repository. Sau đó kiểm tra kết quả trong tab **Actions**.
4. Khi workflow thành công, mở `https://randomfood.freepage.cc/`. Nếu vẫn thấy trang mặc định, kiểm tra thư mục đích `htdocs` và đổi tên/xóa file trang mặc định như `index.php` đang được ưu tiên hơn `index.html`.

Món do người dùng thêm được lưu trong `localStorage` của trình duyệt hiện tại; dữ liệu không đồng bộ giữa các thiết bị. Font chữ tải từ Google Fonts; nếu không tải được, website tự dùng font hệ thống.

## Bảng phân công

<img width="356" height="261" alt="image" src="https://github.com/user-attachments/assets/83ac64c1-0a09-4947-bfd8-2bddd7fe8995" />
