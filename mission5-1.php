<!DOCTYPE html>
<html lang ="ja">
    <head>
        <meta charset="UTF-8">
        <title>mission5-1</title>
    </head>

    <?php 
        //SQL接続
        $dsn = "データベース名";
        $user = "ユーザー名";
        $password = "パスワード";
        $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

        //編集が押されたとき
        $edit_name = "";
        $edit_text = "";
        $edit_num  = "";
        if(isset($_POST["edit"]) && !empty($_POST["edi"])){
            $edi = $_POST["edi"];
            $sql = 'SELECT * FROM tbtest';
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchAll();
            foreach($results as $row){
                if($edi == $row["id"] && $_POST["password"] == $row["password"] && $row["password"] !== ""){
                    $edit_num  = $row["id"];
                    $edit_name = $row["name"];
                    $edit_text = $row["comment"];             
                }
            }
        }
    ?>
        
    <body>
        <h1 class = "title" style="margin-left:100px" >掲示板</h1>
        <!--入力フォームの作成-->
        <form action="" method="POST">
            <table>
                <tr>
                    <td align="right"><b>名前:</b></td>
                    <td><input type="text" name = "name" size = "10" value = <?= $edit_name ?>></td>
                    <td><input type="text" name = "editNum" size = "10" value = <?= $edit_num ?>> </td>
                </tr>
                <tr>
                    <td align="right"><b>コメント:</b></td>
                    <td><textarea name = "comment" cols = "30" ><?= $edit_text ?></textarea></td>
                </tr>
                <tr>
                    <td align="right"><b>パスワード:</b></td>
                    <td><input type = "number" name = "password" cols = "3" min = "0"></td>
                </tr>
                <tr>
                    <td align="right"><b>削除番号:</b></td>
                    <td><input type = "number" name = "del" cols = "3" min = "0"></td>
                </tr>
                <tr>
                    <td align="right"><b>編集番号:</b></td>
                    <td><input type = "number" name = "edi" cols = "3" min = "0"></td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <input type = "submit" name = "submit">
                        <input type = "submit" name = "delete" value="削除">
                        <input type = "submit" name = "edit" value = "編集">
                    </td>
                    
                </tr>
            </table>
        </form>
        ※ 投稿の編集・削除にはパスワードが必須です

        <hr>

        <?php

        //コメントの書き出し
        function export_data(){
            $dsn = "データベース名";
            $user = "ユーザー名";
            $password = "パスワード";
            $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
            $sql = "SELECT * FROM tbtest";
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchAll();
            foreach ($results as $row){
                echo "番号:".$row["id"]."<br>";
                echo "名前:".$row["name"]."<br>";
                echo "コメント:".$row["comment"]."<br>";
                echo "時間:".$row["time"]."<br>"."<br>";
            }
        }

        //フォームを送信したとき
        if(!empty($_POST["name"]) && !empty($_POST["comment"])){ 
            $name = $_POST["name"];
            $str  = $_POST["comment"];
            $time = date("Y/m/d H:i:s");
            $pass = $_POST["password"];

            $editor_num = $_POST["editNum"];

            if($editor_num == ""){
            //データベースへの書き込み
            $sql = $pdo -> prepare("INSERT INTO tbtest (name, comment, time, password ) VALUES (:name, :comment, :time, :password)");
            $sql -> bindParam(":name", $name, PDO::PARAM_STR);
            $sql -> bindParam(":comment", $str, PDO::PARAM_STR);
            $sql -> bindParam(":time", $time, PDO::PARAM_STR);
            $sql -> bindParam(":password", $pass, PDO::PARAM_STR);
            $sql -> execute();

            }elseif($editor_num != ""){
                $sql = 'SELECT * FROM tbtest';
                $stmt = $pdo->query($sql);
                $results = $stmt->fetchAll();
                foreach($results as $row){
                    if($editor_num == $row['id'] ){
                        $sql = 'UPDATE tbtest SET name=:name,comment=:comment,time=:time WHERE id=:id';
                        $stmt = $pdo->prepare($sql);
                        $stmt->bindParam(":name", $name, PDO::PARAM_STR);
                        $stmt->bindParam(":comment", $str, PDO::PARAM_STR);
                        $stmt->bindParam(':time', $time, PDO::PARAM_STR);
                        $stmt->bindParam(":id", $editor_num, PDO::PARAM_INT);
                        $stmt->execute();
                    }
                }
            }
            export_data();
            
        }elseif(isset($_POST["delete"]) && !empty($_POST["del"])){ //削除が押されたとき
            $del = $_POST["del"]; 
            $sql = 'SELECT * FROM tbtest';
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchAll();
            foreach($results as $row){
                if($del == $row["id"] && $_POST["password"] == $row["password"] && $row["password"] !== "" ){
                    //データベースから削除
                    $sql = "delete from tbtest where id=:id";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(":id", $del, PDO::PARAM_INT);
                    $stmt->execute();
                }
            }
            export_data();
        }else{
            export_data();
        }
        ?>
    </body>
</html>