<?php

    $question = $this->GetQuestion();
?>

<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Login</title>
        <link rel="stylesheet" href="css/styles.css?v=1.0">
    </head>
    <body>
        <form method="POST">
            <input type="hidden" name="question_index" value="<?php echo $question['index']; ?>" />
            <p><?php echo $question['question']; ?></p>
            <input type="text" name="question_answer" />
            <input type="submit" name="submit" />
        </form>
    </body>
</html>
