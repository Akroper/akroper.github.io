<?php
    session_start();
    if($_SESSION['gotFeedback']){
        $_SESSION['gotFeedback'] = false;
        echo("<div class='modalWindowParent'><div class='modalWindow'><div class='modalWindowMessage'>Спасибо за ваш отзыв! Как только он пройдет модерацию его смогут увидеть другие пользователи :)</div><form action='index.php' method='post'><input type='submit' value='OK' class='buttonSubmit' /></form></div></div>");
    }
    require_once("db.php");
    if(!empty($_POST['sendFeedback'])){
        $email = $_POST['email'];
        $correctEmail = strpos($email, '@');
        if($correctEmail === false){
            $_SESSION['b'] = 0;
            $_SESSION['feedbackError'] = 'incorrectEmail';
        }
        else{
            if(strpos(substr($email, $correctEmail + 1), '.') === false){
                $_SESSION['b'] = 0;
                $_SESSION['feedbackError'] = 'incorrectEmail';
            }
            else{
                $name = $_POST['name'];
                $rate = $_POST['rating'];
                $fb = $_POST['feedback'];
                $stmt = $pdo->prepare("INSERT INTO feedbacks VALUES (NULL, ?, ?, ?, ?, ? , NULL, 0)");
                $stmt->execute(array($name, $email, $rate, $fb, date('Y.m.j')));
                $_SESSION['gotFeedback'] = true;
            }
        }
        header("Location: index.php"); 
    }
?>
<div id="mainPhotoContainer"><img src="images/shopPhoto.jpg" id="mainPhoto" align="center" /></div>
    <div class="advantagesHeader">
        <h2>Выбирая нас, Вы выбираете:</h2>
    </div>
    <div id="advantages">
        <div>
            <img src="images/iconLike.svg" />
            <h3>Качество товара</h3>
        </div>
        <div>
            <img src="images/iconChecklist.svg" />
            <h3>Широкий выбор</h3>
        </div>
        <div>
            <img src="images/iconSale.svg" />
            <h3>Выгодные цены</h3></div>
        <div>
            <img src="images/iconCashier.svg" />
            <h3>Вежливый и отзывчивый персонал</h3></div>
        </div>
    </div>
    <div id="mainText" class="darkThemeDarker">
        <div>
            <h2>Наш график работы:</h2>
            <div id="schedule">
                <div style="border-right: 1px #ccc solid">По будням: с 9.00 до 19.00</div>
                <div style="border-left: 1px #ccc solid">По выходным: с 9.00 до 17.00</div>
            </div>
            <p>
                Наши магазины находятся по адресам: г. Энгельс, ул. Полтавская, д. 54а</br> и г. Энгельс, ул. 148-й Черниговской Дивизии, 25.
            </p>
        </div>
    </div>
    <div id="map"><iframe src="https://yandex.ru/map-widget/v1/?um=constructor%3Ac2938c86ce483e8a828653e7d02ae32b0ce4d565219a6f798dc7dbb223380891&amp;source=constructor" frameborder="0"></iframe></div>
    <div id="feedbacks">
        <div id="leaveFeedback" class="darkThemeDarker">
            <h2>Хотите оставить отзыв?</h2>
            <form method="post" action="index.php" class="userForm">
                <label>Ваше имя:</label>
                <input type="text" name="name" required maxlength="30" />
                <br />
                <label>Ваш email:</label>
                <input type="text" name="email" required maxlength="30" />
                <br />
                <label>Ваша оценка:</label>
                <div class="rating-area">
                    <input type="radio" id="star-5" name="rating" value="5" required>
                    <label for="star-5" title="Оценка «5»"></label>	
                    <input type="radio" id="star-4" name="rating" value="4">
                    <label for="star-4" title="Оценка «4»"></label>    
                    <input type="radio" id="star-3" name="rating" value="3">
                    <label for="star-3" title="Оценка «3»"></label>  
                    <input type="radio" id="star-2" name="rating" value="2">
                    <label for="star-2" title="Оценка «2»"></label>    
                    <input type="radio" id="star-1" name="rating" value="1">
                    <label for="star-1" title="Оценка «1»"></label>
                </div>
                <label>Ваш отзыв:</label>
                <br />
                <textarea name="feedback" required  maxlength="200"></textarea>
                <input type="submit" name="sendFeedback" class="buttonSubmit" id="feedbackSubmit" value="Отправить" />
                <?php
                    if($_SESSION['feedbackError']){
                        echo("<span class='errorMessageLight feedbackErrorMessage'>Пожалуйста, введите корректную почту</span>");
                        if($_SESSION['b']){
                            $_SESSION['feedbackError'] = '';
                            $_SESSION['b'] = 0;
                        }
                        $_SESSION['b']++;
                    }
                ?>
            </form>
        </div>
        <div class="showFeedbacks">
        <div id='shownFeedbacksHeader' class='feedbacksHeader feedbacksHeaderUser'>
            <h2>Наши отзывы:</h2>
            <a id='hideShownFeedbacks' class='hideButton'><img src='../images/hideButton.png' /></a>  
        </div>
        <?php require_once('components/feedbacks.php'); ?>
        </div>
    </div>
    <script type="text/javascript">
        var shownCommentsToggle = false;
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
        document.getElementById('hideShownFeedbacks').addEventListener('click', hideShownFeedbacks, false);
    </script>