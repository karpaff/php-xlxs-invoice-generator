<!doctype html>
<html>

<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=Edge">
	<title>Заказ мебели</title>
	<link rel="stylesheet" href="../css/styles.css" type="text/css">
    <link rel="stylesheet" href="./styles/invoice_form.css" type="text/css">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Oswald:400,300" type="text/css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

	<!--[if lt IE 9]>
	<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
</head>

<body>
	<div id="wrapper">
		<!-- <header>
			<nav>
				<ul id="top-menu">
					<li class="active">ПОРТФОЛИО</li>
					<li><a href="./pic.html">ЗАДАНИЕ 1</a></li>
					<li><a href="./heels.html">ЗАДАНИЕ 2</a></li>
					<li><a href="./brand.html">ЗАДАНИЕ 3</a></li>
					<li><a href="">ЗАДАНИЕ 4</a></li>
					<li><a href="./words.html">ЗАДАНИЕ 5</a></li>
					<li><a href="./game/html/lobby.html">КУРСОВАЯ РАБОТА</a></li>
				</ul>
			</nav>
		</header> -->


		<div class="content">
			<div id="heading">
				<h1>ЗАКАЗ МЕБЕЛИ</h1>
			</div>

            <form id="invoiceForm" enctype="multipart/form-data">
                <label class="header" for="surname">Фамилия:</label>
                <input type="text" id="surname" name="surname" required>

                <label class="header" for="city">Город доставки:</label>
                <select id="city" name="city" required>
                    <option value="Москва">Москва</option>
                    <option value="Санкт-Петербург">Санкт-Петербург</option>
                    <option value="Екатеринбург">Екатеринбург</option>
                </select>

                <label class="header" for="deliveryDate">Дата доставки:</label>
                <input type="date" id="deliveryDate" name="deliveryDate" required>

                <label class="header" for="address">Адрес:</label>
                <input type="text" id="address" name="address" required>

                <div class="columns">
                    <div class="choose-column">
                        <label class="header">Выберите цвет мебели:</label>
                        <div>
                            <label for="walnut">Орех</label>

                            <input type="radio" id="walnut" name="furnitureColor" value="Орех">

                            <label for="oak">Дуб мореный</label>
                            <input type="radio" id="oak" name="furnitureColor" value="Дуб мореный">

                            <label for="palisander">Палисандр</label>
                            <input type="radio" id="palisander" name="furnitureColor" value="Палисандр">

                            <label for="ebony">Эбеновое дерево</label>
                            <input type="radio" id="ebony" name="furnitureColor" value="Эбеновое дерево">

                            <label for="maple">Клен</label>
                            <input type="radio" id="maple" name="furnitureColor" value="Клен">

                            <label for="larch">Лиственница</label>
                            <input type="radio" id="larch" name="furnitureColor" value="Лиственница">

                        </div>
                    </div>

                    <div class="choose-column">
                        <label class="header">Выберите предметы мебели:</label>
                        <div>
                            <label for="bench">Банкетка</label>
                            <input type="checkbox" id="bench" name="itemName1" value="Банкетка">

                            <label for="bed">Кровать</label>
                            <input type="checkbox" id="bed" name="itemName2" value="Кровать">

                            <label for="chest">Комод</label>
                            <input type="checkbox" id="chest" name="itemName3" value="Комод">

                            <label for="wardrobe">Шкаф</label>
                            <input type="checkbox" id="wardrobe" name="itemName4" value="Шкаф">

                            <label for="chair">Стул</label>
                            <input type="checkbox" id="chair" name="itemName5" value="Стул">

                            <label for="table">Стол</label>
                            <input type="checkbox" id="table" name="itemName6" value="Стол">

                        </div>
                    </div>
                    <div class="choose-column">
                        <label class="header" for="quantity">Количество:</label>
                        <input type="number" id="quantity1" name="quantity1" min="0" value="0" required>
                        <input type="number" id="quantity2" name="quantity2" min="0" value="0" required>
                        <input type="number" id="quantity3" name="quantity3" min="0" value="0" required>
                        <input type="number" id="quantity4" name="quantity4" min="0" value="0" required>
                        <input type="number" id="quantity5" name="quantity5" min="0" value="0" required>
                        <input type="number" id="quantity6" name="quantity6" min="0" value="0" required>
                    </div>

                </div>
                <label for="priceFile">Выбрать файл с ценами (txt):</label>
                <input type="file" id="priceFile" name="priceFile" accept=".txt" required>

                <input type="submit" value="Отправить заказ">
            </form>

            <div id="result" style="display: none">
            <p id="totalPrice"></p>
            <a id="excelLink" href="#">Скачать Excel-файл</a>
            </div>


		</div>
	</div>
	<!-- <footer>
		<p>Борисов Андрей &copy; 2023</p>
	</footer> -->
</body>
<script src="./scripts/invoice_form.js"></script>

</html>
