# Hệ thống Quản lý Sinh viên

## 1. Giới thiệu
Dự án xây dựng hệ thống quản lý sinh viên sử dụng Docker Compose.
Hệ thống gồm ứng dụng PHP, cơ sở dữ liệu MySQL, Nginx reverse proxy,
Prometheus, Grafana, Loki và Promtail.

## 2. Công nghệ sử dụng
- PHP và Apache: chạy ứng dụng web.
- MySQL 8.0: lưu trữ dữ liệu sinh viên.
- Nginx: reverse proxy và bổ sung HTTP security headers.
- Docker Compose: triển khai các dịch vụ bằng container.
- Prometheus: thu thập số liệu giám sát.
- Grafana: trực quan hóa số liệu.
- Loki và Promtail: thu thập, truy vấn log.

## 3. Cấu trúc thư mục
- app/: mã nguồn ứng dụng PHP và Dockerfile.
- mysql/: tập lệnh SQL khởi tạo cơ sở dữ liệu.
- nginx/: cấu hình Nginx.
- monitoring/: cấu hình Prometheus.
- logging/loki/: cấu hình Loki.
- logging/promtail/: cấu hình Promtail.
- docker-compose.yml: cấu hình các dịch vụ.
- .env: biến môi trường và thông tin kết nối cơ sở dữ liệu (không đưa lên GitHub).

## 4. Yêu cầu
- Ubuntu hoặc hệ điều hành có Docker hỗ trợ.
- Docker Engine.
- Docker Compose plugin.

## 5. Cấu hình môi trường
Tạo tệp .env từ thông tin cấu hình phù hợp với docker-compose.yml.
Không chia sẻ mật khẩu hoặc đưa tệp .env lên GitHub.

Các biến môi trường cần cấu hình:
- MYSQL_ROOT_PASSWORD
- MYSQL_DATABASE
- MYSQL_USER
- MYSQL_PASSWORD

## 6. Triển khai
Tại thư mục dự án, kiểm tra cấu hình:

```bash
docker compose config -q
```

Khởi động các dịch vụ:

```bash
docker compose up -d
```

Kiểm tra trạng thái:

```bash
docker compose ps
```

Xem log dịch vụ:

```bash
docker compose logs --tail=50
```

## 7. Các địa chỉ truy cập
Các địa chỉ dưới đây sử dụng cổng đã cấu hình trong dự án:

- Website qua Nginx: http://localhost:8083
- Website PHP trực tiếp: http://localhost:8082
- Prometheus: http://localhost:9091
- Grafana: http://localhost:3001
- Loki: http://localhost:3101

Khi truy cập từ máy khác, thay localhost bằng địa chỉ IP của máy Ubuntu.
Đảm bảo cổng tương ứng có thể truy cập từ máy đó.

## 8. Giám sát và ghi log
- Prometheus cung cấp giao diện truy vấn số liệu tại cổng 9091.
- Grafana cung cấp dashboard tại cổng 3001.
- Loki tiếp nhận log và có thể được truy vấn trong Grafana Explore.
- Promtail thu thập log theo cấu hình trong logging/promtail/.

## 9. Bảo mật
- Không commit tệp .env chứa thông tin bí mật.
- Sử dụng mật khẩu phù hợp và không chia sẻ thông tin đăng nhập.
- Nginx được cấu hình một số HTTP security headers.
- Các dịch vụ được kết nối qua mạng Docker theo cấu hình Compose.

## 10. Lưu ý về dữ liệu
Dữ liệu MySQL được lưu trong Docker volume student-db-data.
Không xóa volume khi chưa sao lưu dữ liệu cần giữ.

## 11. Tắt dịch vụ
Để dừng các dịch vụ của dự án:

```bash
docker compose down
```

Lệnh trên không xóa named volumes nếu không sử dụng tùy chọn -v.
Không sử dụng docker compose down -v nếu cần giữ dữ liệu cơ sở dữ liệu.
