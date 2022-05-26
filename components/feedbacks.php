    <?php
        echo("<div id='shownFeedbacks' class='feedbacks");
        if(isset($admin))echo(" feedbacksAdmin");
        echo("'>");
        require_once("db.php");
        $feedbacks = $pdo->query("SELECT * FROM feedbacks WHERE feedbackStatus = 1 ORDER BY feedbackId DESC")->fetchAll();
        if(isset($admin)){
            $k = 1;
        }
        $pages = ceil(count($feedbacks) / 10);
        $j = 1;
        foreach($feedbacks as $f){
            if(($j % 10) == 1)echo("<div class='page'>");
            echo("<div class='feedback");
            if(isset($admin))echo(" feedbackAdmin");
            echo("'>");
            if(isset($admin)){
                echo("<div class='feedbackButtons'>");
                echo("<input type='submit' class='denyButton' name='deleteFeedback' value='" . $k . "' />");
                echo("</div>");
            }
            echo("<h3>" . $f['feedbackName'] . "</h3>");
            echo("<span class='feedbackDate'>" . $f['feedbackDate'] . "</span>");
            if(isset($admin)){
                echo($f['feedbackEmail']);
            }
            echo("<div class='rating-result'>");
            $i = 1;
            while($i <= $f['feedbackRate']){
                echo("<span class='active'></span>");
                $i++;
            }
            while($i <= 5){
                echo("<span></span>");
                $i++;
            }
            echo("</div>");
            echo("<p>" . $f['feedbackText'] . "</p>");
            if($admin){
                echo("<label class='answerLabel'>Наш ответ:</label>");
                echo("<textarea name='feedbackAnswer'>");
                if(isset($f['feedbackAnswer']))echo($f['feedbackAnswer']);
                echo("</textarea>");
                echo("<input type='hidden' name='saveAnswerNumber' value='$k' />");
                echo("<input type='submit' name='saveAnswer' value='Сохранить' class='buttonSubmit' />");
                echo("<br />");
                echo("Чтобы удалить ответ, оставьте поле пустым");
            }
            if(!$admin && !empty($f['feedbackAnswer'])){
                echo("<div class='answerBlock'><div>");
                echo("<h2 class='answerLabel'>Ответ магазина:</h2>");
                echo("<p>" . $f['feedbackAnswer'] . "</p>");
                echo("</div></div>");
            }
            echo("</div>");
            if(isset($admin)){
                $k++;
            }
            if(!($j % 10))echo("</div>");
            $j++;
        }
    ?>
</div>
</div>
<!-- <div class="pagination">
    <a class='pagination-first'>&laquo;</a>
    <?php
        if($pages < 7){
            $counter = 1;
            while($counter <= $pages){
                echo("<a class='paginationItem'>$counter</a>");
                $counter++;
            }
        }
        else{
            echo("<a class='paginationItem'>1</a>");
            echo("<a class='paginationItem'>2</a>");
            echo("<a class='paginationItem'>3</a>");
            echo("<a class='paginationItem paginationHollow'>...</a>");
            echo("<a class='paginationItem'>");
            echo($pages - 1);
            echo("</a>");
        }
    ?>
    <a class='pagination-last'>&raquo;</a>
</div> -->
<!-- <script type="text/javascript">
    var activePage = 0;
    var paginationItems = document.querySelectorAll('.paginationItem');
    var i = 0;
    function changePage(pageNumber){
        var activeItem = document.querySelector('.pagination').querySelector('.active');
        if(!(activeItem === null))activeItem.classList.remove('.active');
        paginationItems[pageNumber].classList.add('.active');
        activePage = pageNumber;
    }
    changePage(activePage);
    while(i < paginationItems.length){
        paginationItems[i].addEventListener('click')
    }
    document.querySelector('.paginationItem').classList.add('active'); -->
</script>