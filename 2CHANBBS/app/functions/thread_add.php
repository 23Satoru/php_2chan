
<?php
$error_message = array();

if (isset($_POST["threadSubmitButton"])) {

    //スレッド名入力チェック
    if (empty($_POST["title"])) {
        $error_message["title"] = "お名前を入力してください。";
    } else {
        //エスケープ
        $escaped["title"] = htmlspecialchars($_POST["title"], ENT_QUOTES, "UTF-8");
    }

    //名前入力チェック
    if (empty($_POST["username"])) {
        $error_message["username"] = "お名前を入力してください。";
    } else {
        //エスケープ
        $escaped["username"] = htmlspecialchars($_POST["username"], ENT_QUOTES, "UTF-8");
    }

    // コメント入力チェック
    if (empty($_POST["body"])) {
        $error_message["body"] = "コメントを入力してください。";
    } else {
        //エスケープ
        $escaped["body"] = htmlspecialchars($_POST["body"], ENT_QUOTES, "UTF-8");
    }

    if (empty($error_message)) {
        $post_date = date("Y-m-d H:i:s");

        //トランザクション開始
        $pdo->beginTransaction();

        try {
            //スレッド追加
            $sql = "INSERT INTO `thread` (`title`) VALUES (:title);";
            $statement = $pdo->prepare($sql);

            $statement->bindParam(":title", $escaped["title"], PDO::PARAM_STR);

            $statement->execute();


            //コメント追加
            $sql = "INSERT INTO comment (username, body, post_date, thread_id) 
        VALUES (:username, :body, :post_date, (SELECT id FROM thread WHERE title = :title))";
            $statement = $pdo->prepare($sql);

            $statement->bindParam(":username", $escaped["username"], PDO::PARAM_STR);
            $statement->bindParam(":body", $escaped["body"], PDO::PARAM_STR);
            $statement->bindParam(":post_date", $post_date, PDO::PARAM_STR);
            $statement->bindParam(":title", $escaped["title"], PDO::PARAM_STR);

            $statement->execute();

            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
        }
    }

    //掲示板ページに遷移
    header("Location: http://localhost:8080/2CHANBBS");
}
