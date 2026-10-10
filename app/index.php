<?php
session_start();

$host = 'student-db';
$dbname = 'student_db';
$username = 'student_user';
$password = 'Htka2408';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

try {
    $conn = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (
            !isset($_POST['csrf_token']) ||
            !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
        ) {
            http_response_code(403);
            exit('Yêu cầu không hợp lệ. Vui lòng tải lại trang.');
        }

        $action = $_POST['action'] ?? '';

        if ($action === 'add' || $action === 'update') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $class = trim($_POST['class'] ?? '');
            $scoreInput = trim($_POST['score'] ?? '');

            if ($name === '' || $email === '' || $class === '') {
                exit('Vui lòng nhập đầy đủ họ tên, email và lớp.');
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                exit('Email không đúng định dạng.');
            }

            $score = null;
            if ($scoreInput !== '') {
                if (!is_numeric($scoreInput) ||
                    (float)$scoreInput < 0 ||
                    (float)$scoreInput > 10) {
                    exit('Điểm phải nằm trong khoảng từ 0 đến 10.');
                }
                $score = (float)$scoreInput;
            }

            if ($action === 'add') {
                $stmt = $conn->prepare(
                    'INSERT INTO students (name, email, class, score)
                     VALUES (?, ?, ?, ?)'
                );
                $stmt->execute([$name, $email, $class, $score]);
            } else {
                $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

                if (!$id || $id < 1) {
                    exit('Mã sinh viên không hợp lệ.');
                }

                $stmt = $conn->prepare(
                    'UPDATE students
                     SET name = ?, email = ?, class = ?, score = ?
                     WHERE id = ?'
                );
                $stmt->execute([$name, $email, $class, $score, $id]);
            }

            header('Location: /');
            exit;
        }

        if ($action === 'delete') {
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

            if (!$id || $id < 1) {
                exit('Mã sinh viên không hợp lệ.');
            }

            $stmt = $conn->prepare('DELETE FROM students WHERE id = ?');
            $stmt->execute([$id]);

            header('Location: /');
            exit;
        }
    }

    $editStudent = null;
    $editId = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT);

    if ($editId && $editId > 0) {
        $stmt = $conn->prepare('SELECT * FROM students WHERE id = ?');
        $stmt->execute([$editId]);
        $editStudent = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    $stmt = $conn->query('SELECT * FROM students ORDER BY id');
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log($e->getMessage());
    http_response_code(500);
    exit('Đã xảy ra lỗi cơ sở dữ liệu. Vui lòng kiểm tra log máy chủ.');
}

function h($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hệ thống Quản lý Sinh viên</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
            background: #f4f6f9;
            color: #222;
        }
        h1 { color: #1d4ed8; }
        .form-box {
            background: white;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
        }
        input {
            box-sizing: border-box;
            width: 100%;
            padding: 10px;
            margin: 6px 0 14px;
        }
        button, .button {
            display: inline-block;
            border: 0;
            border-radius: 4px;
            padding: 9px 13px;
            background: #1d4ed8;
            color: white;
            text-decoration: none;
            cursor: pointer;
        }
        .delete { background: #dc2626; }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 11px;
            text-align: left;
        }
        th { background: #1d4ed8; color: white; }
        tr:nth-child(even) { background: #eef2f7; }
        form.inline { display: inline; }
    </style>
</head>
<body>
    <h1>HỆ THỐNG QUẢN LÝ SINH VIÊN</h1>
    <p>Quản lý thông tin sinh viên bằng PHP, MySQL và Docker.</p>

    <div class="form-box">
        <h2><?= $editStudent ? 'Sửa sinh viên' : 'Thêm sinh viên' ?></h2>

        <form method="post">
            <input type="hidden" name="csrf_token"
                   value="<?= h($_SESSION['csrf_token']) ?>">
            <input type="hidden" name="action"
                   value="<?= $editStudent ? 'update' : 'add' ?>">

            <?php if ($editStudent): ?>
                <input type="hidden" name="id"
                       value="<?= h($editStudent['id']) ?>">
            <?php endif; ?>

            <label>Họ và tên</label>
            <input name="name" required maxlength="100"
                   value="<?= h($editStudent['name'] ?? '') ?>">

            <label>Email</label>
            <input type="email" name="email" required maxlength="100"
                   value="<?= h($editStudent['email'] ?? '') ?>">

            <label>Lớp</label>
            <input name="class" required maxlength="50"
                   value="<?= h($editStudent['class'] ?? '') ?>">

            <label>Điểm (từ 0 đến 10, có thể để trống)</label>
            <input type="number" name="score" min="0" max="10"
                   step="0.01"
                   value="<?= h($editStudent['score'] ?? '') ?>">

            <button type="submit">
                <?= $editStudent ? 'Lưu thay đổi' : 'Thêm sinh viên' ?>
            </button>

            <?php if ($editStudent): ?>
                <a class="button" href="/">Hủy sửa</a>
            <?php endif; ?>
        </form>
    </div>

    <h2>Danh sách sinh viên</h2>

    <table>
        <thead>
            <tr>
                <th>Mã SV</th>
                <th>Họ và tên</th>
                <th>Email</th>
                <th>Lớp</th>
                <th>Điểm</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($students as $student): ?>
                <tr>
                    <td><?= h($student['id']) ?></td>
                    <td><?= h($student['name']) ?></td>
                    <td><?= h($student['email']) ?></td>
                    <td><?= h($student['class']) ?></td>
                    <td><?= h($student['score'] ?? 'Chưa có') ?></td>
                    <td>
                        <a class="button"
                           href="/?edit=<?= h($student['id']) ?>">Sửa</a>

                        <form class="inline" method="post"
                              onsubmit="return confirm('Bạn có chắc muốn xóa sinh viên này?');">
                            <input type="hidden" name="csrf_token"
                                   value="<?= h($_SESSION['csrf_token']) ?>">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id"
                                   value="<?= h($student['id']) ?>">
                            <button class="delete" type="submit">Xóa</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>

