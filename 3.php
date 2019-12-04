<!DOCTYPE HTML>
<html>
<head>
	<link rel="icon" type="image/png" href="images/r0.jpg" />
	<link rel="stylesheet" href="2str.css" type="text/css"/>
	<meta name="description" content="Область"/>
	<meta name="keywords" content="Россия" />
	<meta charset="utf-8">
	<title>Города России: Просмотр информации</title>	
<header>
</head>
<body>
	<div>
		<div class = "title">
			<p class ="stil">Города России</p>
		</div>
		<div>
			<section>
				<div class="menu">
					<ul class="main-menu">
						<li><a href="./index.html">Главная</a></li>
						<li><a href="./2.php">Добавление</a></li>
						<li><a href="./3.php">Просмотр</a></li>
					</ul>
				</div>
			</section>
		</div>
		<?php
			include("conn.php");//подключаемся к БД
			if (isset($_GET['query']) && !empty(trim($_GET['query'])))//если нажата кнопка поиска и запрос не является пустым, то
			{
				$query = trim($_GET['query']);//убираем из запроса лишние пробелы
				//проверка длины запроса
				if (strlen($query) < 3)
					$result = '<p>Слишком короткий поисковой запрос</p>';
				else if (strlen($query) > 128)
					$result = '<p>Слишком длинный поисковой запрос</p>';
				else
				{
					//получаем список городов и областей из БД, где есть совпадения по запросу
					$data = $conn->prepare('SELECT * FROM `regions` WHERE `name` LIKE :i OR `governor` LIKE :i');
					$data->execute(array("i" => '%'.$query.'%'));
					$res = $data->fetchAll();//записываем в переменную список совпадений по областям
					$data = $conn->prepare('SELECT * FROM `cities` WHERE `city` LIKE :i OR `description` LIKE :i');
					$data->execute(array("i" => '%'.$query.'%'));
					$res2 = $data->fetchAll();//записываем в переменную список совпадений по городам
					$result = '<div>';
					if (count($res) > 0 || count($res2) > 0)//если есть совпадения хотя бы в одной таблице, то
					{
						$count = count($res) + count($res2);//общее число совпадений
						$result .= '<h3>По запросу "'.$query.'" найдено совпадений ('.$count.'):</h3>';
						foreach ($res as $i)//вывод результатов поиска по областям
						{
							$result .= '<div><p>'.$i['name'].' область</p><p>Губернатор - '.$i['governor'].'</p></div>';
						}
						foreach ($res2 as $i)//вывод результата поиска по городам
						{
							$result .= '<div><h3>'.$i['city'].'</h3><p>'.$i['description'].'</p><img width="40%" height="auto" src='.$i['picture'].'></div>';
						}
					}
					else//иначе
						$result .= '<p>По запросу "'.$query.'" ничего не найдено</p>';//сообщение, что ничего не найдено
					$result .= '</div>';
				}
			}
			else//иначе
			{
				//вывод всех областей
				$sql = 'SELECT * FROM `regions`';
				$data = $conn->prepare($sql);
				$data->execute();
				$res = $data->fetchAll();//записываем в переменную список всех областей
				if (count($res) > 0)//если есть записи в таблице, то
				{
					foreach ($res as $i)//в цикле добавляем в переменную все области
					{
						$result .= '<div><h2>'.$i['name'].' область. Губернатор - '.$i['governor'].'</h2>';//выводим название области и губернатора
						//проверяем наличие городов в БД, которые относятся к области i
						$data = $conn->prepare('SELECT * FROM `cities` WHERE `id_region`=:id');
						$data->execute(array('id' => $i['id']));
						$res2 = $data->fetchAll();//заносим в переменную города данной области i
						if (count($res2) > 0)
						{
							foreach ($res2 as $j)//и для каждой области данные о городах этой области
							{
								$result .= '<div><h3>'.$j['city'].'</h3><p>'.$j['description'].'</p><img width="50%" height="auto" src='.$j['picture'].'></div>';
							}
						}
						$result .= '</div>';
					}
				}
				else//иначе
					$result = '<p>Нет данных по областям</p>';//выводим отсутствие данных в БД
			}
		?>
		<form id="search" name="search" method="get">
			<input type="search" name="query" placeholder="Поиск.." />
			<input type="submit" value="Найти" />
		</form>
		<div>
			<?=$result/*вывод результата поиска или списка всех областей и городов*/?>
		</div>
	</div>
</body>
</html>