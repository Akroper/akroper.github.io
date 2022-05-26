<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=0.5, maximum-scale=3">
        <title>Панель администратора</title>
        <script defer src="../css_js/simple-adaptive-slider.js"></script>
        <link rel="stylesheet" href="../css_js/simple-adaptive-slider.css">
        <link charset="utf-8" href="../css_js/css.css" rel="stylesheet" type="text/css">
        <link charset="utf-8" href="../css_js/rating.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <?php
            // error_reporting(E_ALL);
            require_once('../components/db.php');
            $stmt = $pdo->query("select * from password");
            $pass = $stmt->fetchAll();
            $pass = $pass[0]['password'];
            session_start();
            if(!empty($_POST['exitButton'])){
                session_destroy();
                header('Location: index.php');
            }
            if(empty($_SESSION['pass']) && $pass == $_POST['pass'])$_SESSION['pass'] = $pass;
            if($_SESSION['pass'] == $pass){
                function showModalWindow($text, $mode, $isInForm){
                    echo("<div class='modalWindowParent'><div class='modalWindow'><div class='modalWindowMessage'>");
                    echo($text);
                    echo("</div>");
                    if(!$isInForm)echo("<form action='index.php' method='post'>");
                    if($mode){
                        echo("<div class='modalWindowButtons'>");
                        echo("<input type='submit' name='accept' value='Да' class='buttonSubmit");
                        if($mode == 2)echo(" deleteButton");
                        echo("' />");
                        echo("<input type='submit' name='cancel' value='Нет' class='buttonSubmit' />");
                        echo("</div>");
                    }
                    else{
                        echo("<input type='submit' name='action' value='OK' class='buttonSubmit' />");
                    }
                    if(!$isInForm)echo("</form>");
                    echo("</div></div>");
                }
                $categories = $pdo->query("SELECT * FROM categories")->fetchAll();
                $_SESSION['emptyCategory'] = empty($categories);
                if(!$_SESSION['emptyCategory']){
                    if((!isset($_SESSION['category']) && empty($_POST['category'])) || empty($pdo->query("SELECT * FROM categories WHERE categoryName = '" . $_SESSION['category'] . "'")->fetchAll()))$_SESSION['category'] = $categories[0]['categoryName'];
                    else if(!empty($_POST['category'])) $_SESSION['category'] = $_POST['category'];
                    $goods = $pdo->query("SELECT * FROM goods WHERE goodCategory = '" . $pdo->query("SELECT * FROM categories WHERE categoryName = '" . $_SESSION['category'] . "'")->fetchAll()[0]['categoryId'] . "'")->fetchAll();
                    if(empty($_POST['good']))$good = $goods[0]['goodName'];
                    else if(empty($pdo->query("SELECT * FROM goods WHERE goodName = '" . $_POST['good'] . "' AND goodCategory = '" . $pdo->query("SELECT * FROM categories WHERE categoryName = '" . $_SESSION['category'] . "'")->fetchAll()[0]['categoryId'] . "'")->fetchAll()))$good = $goods[0]['goodName'];
                    else $good = $_POST['good'];
                }
                $photos = $pdo->query("SELECT * FROM galleryPhotos")->fetchAll();
                $newCategoryError = '';
                if(!empty($_POST['addNewCategory'])){
                    if(empty($_POST['newCategoryName']))$newCategoryError = 'notEnoughData';
                    else if(!empty($pdo->query("SELECT * FROM categories WHERE categoryName = '" . $_POST['newCategoryName'] . "'")->fetchAll()))$newCategoryError = true;
                    else $pdo->query("INSERT INTO categories VALUES (NULL, '" . $_POST['newCategoryName'] . "')");
                    header("Location: index.php");
                }
                if(!empty($_POST['addNewGood'])){
                    if(!isset($_POST['newGoodName']) || !isset($_POST['newGoodCost']))$_SESSION['newGoodError'] = 'notEnoughData';
                    else if(!empty($pdo->query("SELECT * FROM goods WHERE goodName = '" . $_POST['newGoodName'] . "' AND goodCategory = '" . $pdo->query("SELECT * FROM categories WHERE categoryName = '" . $_SESSION['category'] . "'")->fetchAll()[0]['categoryId'] . "'")->FetchAll()))$_SESSION['newGoodError'] = 'exists';
                    else{
                        $pdo->query("INSERT INTO goods VALUES (NULL, '" . $_POST['newGoodName'] . "', " . $_POST['newGoodCost'] . ", " . $pdo->query("SELECT * FROM categories WHERE categoryName = '" . $_POST['category'] . "'")->fetchAll()[0]['categoryId'] . ")");
                        header('Location: ./index.php');
                    }
                }
                if(!empty($_POST['deleteCategory'])){
                    if(empty($_POST['accept']) && empty($_POST['cancel'])){
                        echo("<form action='index.php' method='post'>");
                        echo("<input type='hidden' name='category' value='" . $_POST['category'] . "' />");
                        echo("<input type='hidden' name='deleteCategory' value='true' />");
                        showModalWindow("Вы действительно хотите удалить категорию " . $_POST['category'] . "?", 2, 1);
                        echo("</form>");
                    }
                    else if($_POST['accept']){
                        $pdo->query("DELETE FROM categories WHERE categoryName='" . $_POST['category'] . "'");
                        header("Location: index.php");
                    }
                }
                $editingMode;
                $editError = false;
                if($_POST['editGoodButton'])$editingMode = true;
                else $editingMode = '';
                if($_POST['editGoodSave']){
                    if($_POST['editGoodName'] != $good && !empty($pdo->query("SELECT * FROM goods WHERE goodName = '" . $_POST['editGoodName'] . "' AND goodCategory = '" . $pdo->query("SELECT * FROM categories WHERE categoryName = '" . $_SESSION['category'] . "'")->fetchAll()[0]['categoryId'] . "'")->FetchAll()))$editError = 'exists';
                    else if(empty($_POST['editGoodName']) || empty($_POST['editGoodCost']))$editError = 'notEnoughData';
                    else if(empty($_POST['accept']) && empty($_POST['cancel'])){
                        echo("<form action='index.php' method='post'>");
                        echo("<input type='hidden' name='good' value='" . $good . "' />");
                        echo("<input type='hidden' name='editGoodName' value='" . $_POST['editGoodName'] . "' />");
                        echo("<input type='hidden' name='editGoodCost' value='" . $_POST['editGoodCost'] . "' />");
                        echo("<input type='hidden' name='editGoodSave' value='true' />");
                        showModalWindow("Вы действительно изменить название товара '" . $good . "' на '" . $_POST['editGoodName'] . "', и его цену " . $pdo->query("SELECT goodCost FROM goods WHERE goodName = '" . $good . "' AND goodCategory = " . $pdo->query("SELECT * FROM categories WHERE categoryName = '" . $_POST['category'] . "'")->fetchAll()[0]['categoryId'])->fetchAll()[0]['goodCost']  . " на " . $_POST['editGoodCost'] . "?", 1, 1);
                        echo("</form>");
                    }
                    else if(!empty($_POST['accept'])){
                        $pdo->query("UPDATE goods SET goodName = '" . $_POST['editGoodName'] . "', goodCost = " . $_POST['editGoodCost'] . " WHERE goodName = '" . $good . "' AND goodCategory = " . $pdo->query("SELECT * FROM categories WHERE categoryName = '" . $_SESSION['category'] . "'")->fetchAll()[0]['categoryId']);
                        header("Location: index.php");
                    }
                }
                if($_POST['deleteGood']){
                    if(empty($_POST['accept']) && empty($_POST['cancel'])){
                        echo("<form action='index.php' method='post'>");
                        echo("<input type='hidden' name='good' value='" . $good . "' />");
                        echo("<input type='hidden' name='deleteGood' value='true' />");
                        showModalWindow("Вы действительно удалить товар '" . $good . "?", 2, 1);
                        echo("</form>");
                    }
                    else if(!empty($_POST['accept'])){
                        $pdo->query("DELETE FROM goods WHERE goodName = '" . $good . "' AND goodCategory = " . $pdo->query("SELECT * FROM categories WHERE categoryName = '" . $_SESSION['category'] . "'")->fetchAll()[0]['categoryId']);
                        header("Location: index.php");
                    }
                }
                $addPhotoError = '';
                if($_POST['addNewPhoto']){
                    if(empty($_FILES['newPhotoName']))$addPhotoError = 'notEnoughData';
                    else{
                        $imagesPath = dirname(dirname(__FILE__)) . '\\images\\gallery\\';
                        $filePath = time() . baseName($_FILES['newPhotoName']['name']);
                        echo($imagesPath . $filePath);
                        move_uploaded_file($_FILES['newPhotoName']['tmp_name'], ($imagesPath . $filePath));
                        $pdo->query("INSERT INTO galleryPhotos VALUES(NULL, '" . $filePath . "')");
                        header("Location: index.php");
                    }      
                }
                if($_POST['deletePhoto']){
                    if(empty($_POST['accept']) && empty($_POST['cancel'])){
                        echo("<form action='index.php' method='post'>");
                        echo("<input type='hidden' name='deletePhotoNumber' value='" . $_POST['deletePhotoNumber'] . "' />");
                        echo("<input type='hidden' name='deletePhoto' value='true' />");
                        showModalWindow("Вы действительно удалить " . $_POST['deletePhotoNumber'] . "?", 2, 1);
                        echo("</form>");
                    }
                    else if(!empty($_POST['accept'])){
                        $pdo->query("DELETE FROM galleryPhotos WHERE photoId = " . $photos[(int)array_slice(explode(' ', $_POST['deletePhotoNumber']), -1)[0] - 1]['photoId']);
                        header("Location: index.php");
                    }
                }
                if(!empty($_POST['clearImages'])){
                    if(empty($_POST['accept']) && empty($_POST['cancel'])){
                        echo("<form action='index.php' method='post'>");
                        echo("<input type='hidden' name='clearImages' value='true' />");
                        showModalWindow("Вы действительно очистить папку галереи от лишних изображений?", 2, 1);
                        echo("</form>");
                    }
                    else if(!empty($_POST['accept'])){
                        $imagesFiles = scandir('..\\images\\gallery');
                        foreach($imagesFiles as $f){
                            $b = false;
                            foreach($photos as $p)if($f == $p['photoAddress'] || $f == '.' || $f == '..'){
                                $b = true;
                                break;
                            }
                            if(!$b)unlink('..\\images\\gallery\\' . $f);
                        }
                        header("Location: index.php");
                    }
                }
                $changePassError;
                if(!empty($_POST['changePassword'])){
                    if(empty($_POST['newPass'] || $_POST['newPassRepeat']))$changePassError = 'notEnoughData';
                    else if($_POST['newPass'] != $_POST['newPassRepeat'])$changePassError = 'repeatDoesntMatch';
                    else if(empty($_POST['accept']) && empty($_POST['cancel'])){
                        echo("<form action='index.php' method='post'>");
                        echo("<input type='hidden' name='newPass' value='" . $_POST['newPass'] . "' />");
                        echo("<input type='hidden' name='newPassRepeat' value='" . $_POST['newPassRepeat'] . "' />");
                        echo("<input type='hidden' name='changePassword' value='true' />");
                        showModalWindow("Вы действительно изменить пароль?", 2, 1);
                        echo("</form>");
                    }
                    else if(!empty($_POST['accept'])){
                        $pdo->prepare("UPDATE password SET password = ?")->execute(array($_POST['newPass']));
                        $_SESSION['pass'] = $_POST['newPass'];
                    }
                }
                $moderatingFeedbacks = $pdo->query("SELECT * FROM feedbacks WHERE feedbackStatus = 0 ORDER BY feedbackId ASC")->fetchAll();
                if(!empty($_POST['agree'])){
                    $pdo->query("UPDATE feedbacks SET feedbackStatus = 1 WHERE feedbackId = " . $moderatingFeedbacks[(int)$_POST['agree'] - 1]['feedbackId']);
                    header("Location: index.php");
                }
                if(!empty($_POST['deny'])){
                    if(empty($_POST['accept']) && empty($_POST['cancel'])){
                        echo("<form action='index.php' method='post'>");
                        echo("<input type='hidden' name='deny' value='" . $_POST['deny'] . "' />");
                        showModalWindow("Вы действительно отклонить отзыв от " . $moderatingFeedbacks[(int)$_POST['deny'] - 1]['feedbackDate'] . " от " . $moderatingFeedbacks[(int)$_POST['deny'] - 1]['feedbackName'] . "?", 2, 1);
                        echo("</form>");
                    }
                    else if(!empty($_POST['accept'])){
                        $pdo->query("DELETE FROM feedbacks WHERE feedbackId = " . $moderatingFeedbacks[(int)$_POST['deny'] - 1]['feedbackId']);
                        header("Location: index.php");
                    }
                }
                if(!empty($_POST['deleteFeedback'])){
                    $feedbackForDeleting = $pdo->query("SELECT * FROM feedbacks WHERE feedbackStatus = 1 ORDER BY feedbackId DESC")->fetchAll()[(int)$_POST['deleteFeedback'] - 1];
                    if(empty($_POST['accept']) && empty($_POST['cancel'])){
                        echo("<form action='index.php' method='post'>");
                        echo("<input type='hidden' name='deleteFeedback' value='" . $_POST['deleteFeedback'] . "' />");
                        showModalWindow("Вы действительно удалить отзыв от " . $feedbackForDeleting['feedbackDate'] . " от " . $feedbackForDeleting['feedbackName'] . "?", 2, 1);
                        echo("</form>");
                    }
                    else if(!empty($_POST['accept'])){
                        $pdo->query("DELETE FROM feedbacks WHERE feedbackId = " . $feedbackForDeleting['feedbackId']);
                        header("Location: index.php");
                    }
                }
                if(!empty($_POST['saveAnswer'])){
                    $feedbackAnswer = $_POST['feedbackAnswer'];
                    $feedbackNumber = $_POST['saveAnswerNumber'];
                    $feedbackForAnswering = $pdo->query("SELECT * FROM feedbacks WHERE feedbackStatus = 1")->fetchAll()[(int)$_POST['saveAnswerNumber'] - 1];
                    $stmt = $pdo->prepare("UPDATE feedbacks SET feedbackAnswer = ? WHERE feedbackId = ?");
                    $stmt->execute(array($feedbackAnswer, $feedbackForAnswering['feedbackId']));
                    // header("Location: index.php");
                }
                $admin = 1;
                echo("<form method='post' action='index.php'>
                <input type='submit' name='exitButton' value='Выйти' class='buttonSubmit' />
            </form>
            <div class='adminForms'>
                <h2>Категории товаров</h2>
                <form method='post' action='index.php' class='goodsForms'>
                    <input type='hidden' name='pass' value='<?=$pass?>' />
                    <div class='categoriesForms'>
                        <div class='selectCategoryForm'>
                            <select name='category'");
                if($_SESSION['emptyCategories'])echo('disabled');
                echo(">");
                foreach($categories as $cat){
                    echo('<option ');
                    if($cat['categoryName'] == $_SESSION['category'])echo('selected');
                    echo('>' . $cat['categoryName'] . '</option>');
                }
                echo("</select>
                <input type='submit' name='selectCategory' value='Открыть' class='buttonSubmit' ");
                if($_SESSION['emptyCategories'])echo('disabled');
                echo(" />
                <input type='submit' name='deleteCategory' value='Удалить' class='buttonSubmit deleteButton' ");
                if($_SESSION['emptyCategories'])echo('disabled');
                echo(" />");
                echo("</div>
                <label>Добавить новую категорию:</label>
                <input type='text' name='newCategoryName' />
                <input type='submit' name='addNewCategory' value='Добавить' class='buttonSubmit' />");
                if($newCategoryError){
                    echo('<span class="errorMessage">');
                    if($newCategoryError == 'exists')echo("Категория с таким названием уже существует");
                    else echo("Поле не должно быть пустым");
                    echo("</span>");
                    $newCategoryError = '';
                }
                echo("</div>
                <h2>Товары</h2>
                <div class='goods'>
                    <table>
                        <tr>
                            <th>Наименование</th>
                            <th>Цена, р</th>
                        </tr>");
                if(!$_SESSION['emptyCategories'])foreach($pdo->query("SELECT * FROM goods WHERE goodCategory=" . $pdo->query("SELECT * FROM categories WHERE categoryName = '" . $_SESSION['category'] . "'")->fetchAll()[0]['categoryId']) as $g)echo("<tr><td>" . $g['goodName'] . "</td><td>" . $g['goodCost'] . "</td></tr>");
                echo("</table>
                <br />
                <br />
                <label>Редактировать товары:</label>
                <select name='good'");
                if($_SESSION['emptyCategories'])echo('disabled');
                echo(">");
                foreach($goods as $g){
                    echo('<option ');
                    if($g['goodName'] == $good){
                        echo('selected');
                    }
                    echo('>' . $g['goodName'] . '</option>');
                }
                echo("</select>
                <input type='submit' name='editGoodButton' value='Редактировать' class='buttonSubmit'");
                if($_SESSION['emptyCategories'])echo('disabled');
                echo(" />");
                echo("<input type='text' name='editGoodName' ");
                if($editingMode)echo("value = '" . $good . "'");
                else echo('disabled');
                if($_SESSION['emptyCategories'])echo('disabled');
                echo(" />");
                echo("<input type='text' name='editGoodCost' ");
                if($editingMode)echo("value = '" . $pdo->query("SELECT * FROM goods WHERE goodName = '" . $good . "' AND goodCategory = '" . $pdo->query("SELECT * FROM categories WHERE categoryName = '" . $_SESSION['category'] . "'")->fetchAll()[0]['categoryId'] . "'")->fetchAll()[0]['goodCost'] . "'");
                else echo("disabled");
                if($_SESSION['emptyCategories'])echo('disabled');
                echo("/>");
                echo("<input type='submit' name='editGoodSave' value='Сохранить' class='buttonSubmit' ");
                if(!$editingMode)echo("disabled");
                if($_SESSION['emptyCategories'])echo('disabled');
                echo(" />");
                echo("<input type='submit' name='deleteGood' value='Удалить' class='buttonSubmit deleteButton' ");
                if($_SESSION['emptyCategories'])echo('disabled');
                echo(" />");
                if($editError){
                    echo("<span class='errorMessage'>");
                    if($editError == 'exists')echo("В данной категории уже существует товар с таким названием");
                    else echo("Для сохранения товара должны быть заполнены оба поля");
                    echo("</span>");
                    $editError = false;
                }
                echo("<br />
                <br />
                <label>Добавить новый товар:</label>
                <input type='text' name='newGoodName' ");
                if($_SESSION['emptyCategories'])echo('disabled');
                echo(" />");
                echo("<input type='text' name='newGoodCost' ");
                if($_SESSION['emptyCategories'])echo('disabled');
                echo(" />");
                echo("<input type='submit' name='addNewGood' class='buttonSubmit' value='Добавить' ");
                if($_SESSION['emptyCategories'])echo('disabled');
                echo(" />");
                if($_SESSION['newGoodError']){
                    echo("<span class='errorMessage'>");
                    if($_SESSION['newGoodError'] == 'exists')echo("В данной категории уже существует товар с таким названием");
                    else echo("Для добавления нового товара должны быть заполнены оба поля");
                    echo("</span>");
                    $_SESSION['newGoodError'] = false;
                }
                echo(" </form>
                <h2>Редактирование галереи</h2>");
                require_once("../components/galleryForAdmin.php");
                echo(" <form action='index.php' method='post' class='addPhotoForm' enctype='multipart/form-data'>           
                <label>Добавить фото:</label>
                <input required type='file' name='newPhotoName' />
                <input type='submit' name='addNewPhoto' value='Добавить' class='buttonSubmit' />");
                if($addPhotoError)echo("<span class='errorMessage'>Чтобы добавить новое фото необходимо прикрепить файл</span>");
                echo("</form>
                <form action='index.php' method='post' class='deletePhotoForm'>
                    <label>Удалить фото:</label>
                    <select name='deletePhotoNumber'>");
                if(!empty($photos)){
                    $i = 1;
                    foreach($photos as $p){
                        echo('<option>Фото ' . $i . '</option>');
                        $i++;
                    }
                }
                echo("</select>
                <input type='submit' name='deletePhoto' value='Удалить' class='buttonSubmit deleteButton' />
            </form>
            <br />
            <form action='index.php' method='post' class='clearServerForm'>
                <label>Очистить папку от изображений, не используемых в галерее</label>
                <input type='submit' name='clearImages' value='Очистить' class='buttonSubmit deleteButton' />
            </form>
            <h2>Управление паролем</h2>
            <br />
            <form action='index.php' method='post' class='adminForm'>
                <label>Изменить пароль:</label>
                <br />
                <br />
                <label>Новый пароль:</label>
                <input type='password' name='newPass' required />
                <label>Повторите новый пароль:</label>
                <input type='password' name='newPassRepeat' required />
                <input type='submit' name='changePassword' value='Изменить' class='buttonSubmit deleteButton' />");
                if($changePassError){
                    echo('<span class="errorMessage">');
                    if($changePassError == 'notEnoughData')echo('Должны быть заполнены оба поля');
                    else echo('Пароль и его повтор не совпадают');
                    echo("</span>");
                }
                echo(" </form>
                <h2>Управление отзывами</h2>
            <div id='moderatingFeedbacksHeader' class='feedbacksHeader'>
                <label>Отзывы, находящиеся в модерации:</label>
                <a id='hideModeratingFeedbacks' class='hideButton'><img src='../images/hideButton.png' /></a>  
            </div>
            <form action='index.php' method='post' class='feedbacksForm'>
                <div id='moderatingFeedbacks' class='feedbacks feedbacksAdmin' />");
                $k = 1;
                foreach($moderatingFeedbacks as $m){
                    echo("<div id='moderatedFeedbacks' class='feedback feedbackAdmin'>");
                    echo("<div class='feedbackButtons'>");
                    echo("<input type='submit' class='agreeButton' name='agree' value='" . $k . "' />");
                    echo("<input type='submit' class='denyButton' name='deny' value='" . $k . "' />");
                    echo("</div>");
                    echo("<span class='feedbackDate'>" . $m['feedbackDate'] . "</span>");
                    echo("<h3>" . $m['feedbackName'] . "</h3>");
                    echo($m['feedbackEmail']);
                    echo("<div class='rating-result'>");
                    $i = 1;
                    while($i <= $m['feedbackRate']){
                        echo("<span class='active'></span>");
                        $i++;
                    }
                    while($i <= 5){
                        echo("<span></span>");
                        $i++;
                    }
                    echo("</div>");
                    echo("<p>" . $m['feedbackText'] . "</p>");
                    echo("</div>");
                    $k++;
                }
                echo("</div>
                </form>
                <div id='shownFeedbacksHeader' class='feedbacksHeader'>
                    <label>Отзывы, размещенные на странице:</label>
                    <a id='hideShownFeedbacks' class='hideButton'><img src='../images/hideButton.png' /></a>  
                </div>");
                require_once('../components/feedbacks.php');
                echo("<script type='text/javascript'>
                var moderatingCommentsToggle = false;
                var shownCommentsToggle = false;
                function hideModeratingFeedbacks(){
                    if(moderatingCommentsToggle){
                        document.getElementById('moderatingFeedbacks').classList.remove('feedbacksHidden');
                        document.getElementById('moderatingFeedbacksHeader').classList.remove('feedbacksHeaderHidden');
                        document.getElementById('hideModeratingFeedbacks').style.transform = '';
                    }
                    else{
                        document.getElementById('moderatingFeedbacks').classList.add('feedbacksHidden');
                        document.getElementById('moderatingFeedbacksHeader').classList.add('feedbacksHeaderHidden');
                        document.getElementById('hideModeratingFeedbacks').style.transform = 'rotate(180deg)';
                    }
                    moderatingCommentsToggle = !moderatingCommentsToggle;
                }
                function hideShownFeedbacks(){
                    if(shownCommentsToggle){
                        document.getElementById('shownFeedbacks').classList.remove('feedbacksHidden');
                        document.getElementById('shownFeedbacksHeader').classList.remove('feedbacksHeaderHidden');
                        document.getElementById('hideShownFeedbacks').style.transform = '';
                    }
                    else{
                        document.getElementById('shownFeedbacks').classList.add('feedbacksHidden');
                        document.getElementById('shownFeedbacksHeader').classList.add('feedbacksHeaderHidden');
                        document.getElementById('hideShownFeedbacks').style.transform = 'rotate(180deg)';
                    }
                    shownCommentsToggle = !shownCommentsToggle;
                }
                document.getElementById('hideModeratingFeedbacks').addEventListener('click', hideModeratingFeedbacks, false);
                document.getElementById('hideShownFeedbacks').addEventListener('click', hideShownFeedbacks, false);
            </script>
        </div>
    </div>");
            }
            else{
                echo('<table id="loginTable">
                <caption>Введите пароль</caption>
                <form method="post" action="index.php">
                    <tr>
                        <td><input type="password" name="pass" /></td>
                        <td><input type="submit" value="Войти" class="buttonSubmit" /></td>
                    </tr>');
                    if(!empty($_POST['pass'])){
                        echo('<tr class="errorMessage">
                            <td colspan="2">Введен неправильный пароль</td>
                        </tr>');
                    }
                    echo('</form>
                    </table>');
            }
        ?>                 
    </body>
</html>