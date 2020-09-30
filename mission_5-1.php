<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>mission_5-1-3</title>
    </head>
    <body>
        <?php
        //データベースに接続する
        $dsn = 'データベース名';
        $user = 'ユーザー名';//ユーザー名
        $password = 'パスワード';//パスワード
        $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

        //テーブル作成
        $sql = "CREATE TABLE IF NOT EXISTS mission513"
	    ." ("
	    . "id INT AUTO_INCREMENT PRIMARY KEY,"
	    . "name char(32),"
        . "comment TEXT,"
        . "pass1 char(32),"
        . "date char(32),"
        . "number char(32),"
        . "pass2 char(32),"
        . "number2 char(32),"
        . "pass3 char(32)"
	    .");";
        $stmt = $pdo->query($sql);
        
        //投稿機能・編集機能
        if(!empty($_POST["name"]) && !empty($_POST["comment"])){

            $name = $_POST["name"];//名前
            $comment = $_POST["comment"];//コメント
            $pass1 = $_POST["pass1"];//パスワード
            $edit2=$_POST["number3"];//編集したい番号(隠す)
            $date=date("Y/m/d H:i:s");//投稿日時

            if(empty($_POST["number3"])){//投稿機能(編集で返した番号number3がないとき)

                if(!empty($_POST["pass1"])){ //もしパスワードがあれば

                    $sql = $pdo -> prepare("INSERT INTO mission513 (name, comment, date, pass1) VALUES (:name, :comment, :date, :pass1)");
                    $sql -> bindParam(':name', $name, PDO::PARAM_STR);
                    $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
                    $sql -> bindParam(':date', $date, PDO::PARAM_STR);
                    $sql -> bindParam(':pass1', $pass1, PDO::PARAM_STR);
                    $sql -> execute();
                }

            }else{//編集機能

                if(!empty($_POST["pass1"])){//もしパスワードがあれば

                    $id = $edit2; //編集番号が一致する投稿番号を変更する
    
                    $sql = 'UPDATE mission513 SET name=:name,comment=:comment,date=:date,pass1=:pass1 WHERE id=:id';//投稿内容の変更
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                    $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                    $stmt-> bindParam(':date', $date, PDO::PARAM_STR);
                    $stmt-> bindParam(':pass1', $pass1, PDO::PARAM_STR);
                    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                    $stmt->execute();
                }
            }
        
        //削除機能
        }elseif(!empty($_POST["number"]) && !empty($_POST["pass2"])){//削除する場合

            $delete = $_POST["number"];//削除したい番号
            $pass2 = $_POST["pass2"];//パスワード(削除)

            $id = $delete; //投稿番号が削除番号と一致するとき
            $sql = 'SELECT * FROM mission513 WHERE id=:id ';//削除番号と一致する投稿番号のデータ取得
            $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
            $stmt->bindParam(':id', $id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
            $stmt->execute();                             // ←SQLを実行する。
            $results = $stmt->fetchAll(); 
	        foreach($results as $row){
                if($delete==$row['id'] && $pass2==$row['pass1']){//pass1:投稿時のパスワードと一致するとき
                    $sql = 'delete from mission513 where id=:id';
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                    $stmt->execute();
	            }
            }

        //編集機能
        }elseif(!empty($_POST["number2"]) && !empty($_POST["pass3"])){//編集する場合
            $edit=$_POST["number2"];//編集したい番号
            $pass3=$_POST["pass3"];//パスワード(編集)

            $id = $edit; //投稿番号が編集番号と一致するとき
            $sql = 'SELECT * FROM mission513 WHERE id=:id ';//編集番号と一致する投稿番号のデータ取得
            $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
            $stmt->bindParam(':id', $id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
            $stmt->execute();                             // ←SQLを実行する。
            $results = $stmt->fetchAll(); 
            foreach($results as $row){
                if($edit==$row['id'] && $pass3==$row['pass1']){//pass1:投稿時のパスワードと一致するとき
                    $name2=$row['name'];//名前の取得
                    $comment2=$row['comment'];//コメントの取得
                    $id2=$row['id'];//投稿番号の取得
                }
            }
        }
        ?>

        <!-- 各種フォームの作成 -->
        <span style="font-size: 50px;">WEB掲示板</span><br>
        <span style="font-size: 20px;">新規投稿</span><br>
        名前・コメント・パスワードを入力して下さい。パスワードが無いと投稿できません。<br>
        <form action="" method="post">
            <input type="text" name="name" placeholder="名前" value="<?php if(isset($name2)){echo $name2;} ?>">
            <input type="text" name="comment" placeholder="コメント" value="<?php if(isset($comment2)){echo $comment2;} ?>">
            <input type="hidden" name="number3" value="<?php if(isset($id2)){echo $id2;} ?>">
            <input type="text" name="pass1" placeholder="パスワードを入力">
            <input type="submit" name="submit">
        </form><br>
        <span style="font-size: 20px;">投稿の削除</span><br>
        削除したい番号を半角で入力して下さい。投稿時のパスワードと一致しないと削除できません。<br>
        <form action="" method="post">
            <input type="text" name="number" placeholder="削除したい番号">
            <input type="text" name="pass2" placeholder="パスワードを入力">
            <input type="submit" name="submit" value="削除">
        </form><br>
        <span style="font-size: 20px;">投稿の編集</span><br>
        編集したい番号を半角で入力して下さい。投稿時のパスワードと一致しないと編集できません。<br>
        パスワードの一致が確認できると、新規投稿フォームに投稿内容が表示されるので、投稿内容を編集して下さい。<br>
        <form action="" method="post">
            <input type="text" name="number2" placeholder="編集したい番号">
            <input type="text" name="pass3" placeholder="パスワードを入力">
            <input type="submit" name="submit2" value="編集">
        </form><br>

        <?php
        //投稿を表示

        $sql = 'SELECT * FROM mission513';
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
            foreach ($results as $row){
            echo "<hr>";
                echo $row['id'].'　';
                echo $row['name'].'　';
                echo $row['comment'].'　';
                echo $row['date'].'<br>';
            echo "<hr>";
            }
        ?>
    </body>
</html>