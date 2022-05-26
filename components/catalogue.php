<div class="catalogue">
	<h2>Мы можем Вам предложить</h2>
	<div class="goodTables">
		<?php
			require_once("db.php");
			$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
			if(!empty($categories))foreach($categories as $cat){
				echo("<div>");
				echo("<table class='goodsTable'>\n");
				echo("<caption>" . $cat['categoryName'] . "</caption>\n");
				echo("<tr><th>Наименование товара</th><th>Цена</th></tr>\n");
				$goods = $pdo->query("SELECT * FROM goods WHERE goodCategory = " . $cat['categoryId'])->fetchAll();
				if(!empty($goods))foreach($goods as $g){
					echo("<tr><td>" . $g['goodName'] . "</td><td>" . $g['goodCost'] . "</tr>\n");
				}
				echo("</table>\n");
				echo("</div>");
			}
		?>
	</div>
</div>
<script type="text/javascript">
    document.querySelector("header").classList.add("headerDarker");
</script>