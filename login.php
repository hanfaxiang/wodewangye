<?php
$conn = mysqli_connect("localhost", "root", "123456", "zhongbei");
mysqli_set_charset($conn, 'utf8');
echo $conn ? '连接数据库成功' : '连接数据库失败';
//处理post表单请求
if ($_SERVER["REQUEST_METHOD"] == "POST") {//如果 $_POST['id'] 存在且不为null，则将其赋值给 $id 变量；
    //否则，将空字符串赋值给 $id 变量。
    $id = $_POST['id'] ?? '';
    $username = $_POST['username'] ?? '';
    $xuehao = $_POST['xuehao'] ?? '';
    $class = $_POST['class'] ?? '';
    $telephone = $_POST['telephone'] ?? '';
 /*首先，isset($_POST['id']) 检查 $_POST['id'] 是否存在。如果存在，则将 $_POST['id'] 的值经过 trim() 函数去除首尾空格，
 并将结果赋值给 $id 变量；如果 $_POST['id'] 不存在，则将空字符串赋值给 $id 变量。
    $id = isset($_POST['id']) ? trim($_POST['id']) : '';
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $xuehao = isset($_POST['xuehao']) ? trim($_POST['xuehao']) : '';
    $class = isset($_POST['class']) ? trim($_POST['class']) : '';
    $telephone = isset($_POST['telephone']) ? trim($_POST['telephone']) : '';
 */
    if ($id) {//构建表中数据语句
        // 更新数据
        $update = "UPDATE users SET ";
        $update .= "`username` = '$username', ";
        $update .= "`xuehao` = '$xuehao', ";
        $update .= "`class` = '$class', ";
        $update .= "`telephone` = '$telephone' ";
        $update .= "WHERE id = '$id'";

        if (mysqli_query($conn, $update)) {
            echo "修改记录成功。";
        } else {
            echo "修改记录失败：" . mysqli_error($conn);
        }

        // 重定向回登录页面
        header("Location: login.html");
        exit();
    } else {
        // 插入数据
        $insert = "INSERT INTO users (`username`, `xuehao`, `class`, `telephone`) VALUES ('$username', '$xuehao', 
'$class', '$telephone')";
        if (mysqli_query($conn, $insert)) {
            echo "插入记录成功。";
        } else {
            echo "插入记录失败：" . mysqli_error($conn);
        }
    }
}
if (isset($_GET['delete_id'])) {
    $deleteId = $_GET['delete_id'];

    $deleteSql = "DELETE FROM users WHERE id = '$deleteId'";

    if (mysqli_query($conn, $deleteSql)) {
        echo "记录删除成功。";
    } else {
        echo "记录删除失败：" . mysqli_error($conn);
    }

    // 重定向回登录页面
    header("Location: login.html");
    exit();
}
$sql = "SELECT * FROM users";
$result = mysqli_query($conn, $sql);

if ($result) {
    echo "<style>";
    echo "table {";
    echo "    width: 100%;";
    echo "    border-collapse: collapse;";
    echo "    text-align: center;";
    echo "}";
    echo "th, td {";
    echo "    border: 1px solid black;";
    echo "    width: 150px;";
    echo "    height: 50px;";
    echo "    padding: 8px;";
    echo "}";
    echo "th {";
    echo "    background-color: #f2f2f2;";
    echo "}";
    echo "</style>";

    echo "<div>";
    echo "<table>";
    echo "<tr>";
    echo "<th>姓名</th>";
    echo "<th>学号</th>";
    echo "<th>班级</th>";
    echo "<th>电话号码</th>";
    echo "<th>修改</th>";
    echo "<th>删除</th>";
    echo "</tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row['username'] . "</td>";
        echo "<td>" . $row['xuehao'] . "</td>";
        echo "<td>" . $row['class'] . "</td>";
        echo "<td>" . $row['telephone'] . "</td>";
        echo "<td><a href='login.php?alterid=" . $row['id'] . "&mode=edit'>修改</a></td>";
        if (isset($_GET['alterid']) && isset($_GET['mode']) && $_GET['mode'] === 'edit') {
            $alterId = $_GET['alterid'];
            // 查询要修改的记录的详细信息
            $query = "SELECT * FROM users WHERE id = '$alterId'";
            $result = mysqli_query($conn, $query);
            $row = mysqli_fetch_assoc($result);
            if ($row) {
                echo '<form method="POST" action="login.php">';
                echo '<input type="hidden" name="id" value="' . $alterId . '">';
                echo '姓名：<input type="text" name="username" value="' . $row['username'] . '"><br>';
                echo '学号：<input type="number" name="xuehao" value="' . $row['xuehao'] . '"><br>';
                echo '班级：<input type="text" name="class" value="' . $row['class'] . '"><br>';
                echo '电话：<input type="number" name="telephone" value="' . $row['telephone'] . '"><br>';
                echo '<input type="submit" value="保存修改">';
                echo '</form>';
            } else {
                echo "找不到要修改的记录。";
            }
        }
        echo "<td><a href='login.php?delete_id=" . $row['id'] . "'>删除</a></td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "</div>";
} else {
    echo "查询失败：" . mysqli_error($conn);
}
mysqli_close($conn);