<?php
    require_once('db.php');
    $photos = $pdo->query("SELECT * FROM galleryPhotos")->fetchAll();
    if(!empty($photos)){
        echo('<div class="slider adminSlider"><div class="slider__wrapper"><div class="slider__items">');
        foreach($photos as $p){
            echo("<div class='slider__item'><img src='..\\images\\gallery\\" . $p['photoAddress'] . "' /></div>\n ");
        }
        echo("</div></div>\n");
        echo('<a class="slider__control slider__control_prev" href="#" role="button" data-slide="prev"></a>
        <a class="slider__control slider__control_next" href="#" role="button" data-slide="next"></a>
        </div>');
        echo('<script type = "text/javascript">
            document.addEventListener("DOMContentLoaded", function () {
                // инициализация слайдера
                    new SimpleAdaptiveSlider(".slider", {
                        loop: true,
                        autoplay: false,
                        interval: 5000,
                        swipe: true,
                    });
                });
        </script>');
    }
?>
