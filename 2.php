<!DOCTYPE HTML>
<html>
<head>
	<link rel="icon" type="image/png" href="images/r0.jpg" />
	<link rel="stylesheet" href="2str.css" type="text/css"/>
	<meta name="description" content="Область"/>
	<meta name="keywords" content="Россия" />
	<meta charset="utf-8">
	<title>Города России: Добавление информации</title>	
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
			include("conn.php");//подключаемся к БД (подключаем файл conn.php, в котором прописан код для подключения к БД)
			
			if (isset($_POST['submit_city']))//проверяем, нажата ли кнопка отправки формы для добавления данных о городе
			{
				if (!empty($_POST['id_region']) && !empty($_POST['city']) && !empty($_POST['desc']))//если данные по области, названию города и описанию не пустые, то
				{
					$data = $conn->prepare('SELECT * FROM `cities` WHERE `city` LIKE :city AND `id_region`=:id_region');// вкл запрос на поиск введённого города в введённой области
					$data->execute(["city" => $_POST['city'], "id_region" => $_POST['id_region']]);//исполняем подготовленные данные
					$res = $data->fetchAll();//результат запроса записываем в переменную
					if (count($res) > 0)//если совпадения найдены, то
					{
						$err = '<p>ОШИБКА: уже есть такой город в данной области!</p>';//записываем в переменную ошибку для последующего вывода
					}
							else//иначе
							{
								//вставляем данные о новом городе в БД
								$sql = 'INSERT INTO `cities` (`id_region`, `city`, `description`, `picture`)
								VALUES (:id_region, :city, :description, :picture)';//запрос на вставку данных
								$data = $conn->prepare($sql);
								$data->execute(array(
									"id_region" => $_POST['id_region'], 
									"city" => $_POST['city'], 
									"description" => $_POST['desc'], 
									"picture" => 'images/cities/'.$_FILES['picture']['name']
								));//исполнение
								$count = $data->rowCount();//записываем в переменную кол-во изменённых строк
								if ($count == 1)//если кол-во этих строк равно 1, то
								{
									$err = '<p>Успешно добавлен новый город!</p>';//записываем в переменную положительный результат для последующего вывода
								}
								else//иначе
								{
									$err = '<p>ОШИБКА: неудачная попытка добавления новой записи (города) в БД!</p>';//записываем в переменную ошибку для последующего вывода
								}
							}
						
					
				}
				else//иначе
				{
					$err = '<p>ОШИБКА: присутствуют пустые поля при добавлении города!</p>';//записываем в переменную ошибку для последующего вывода
				}
			}
			if (isset($_POST['submit_region']))//проверяем нажата ли кнопка отправки формы для области
			{
				if (!empty($_POST['region']) && !empty($_POST['governor']))//проверяем, чтобы поля были не пустыми
				{
					//ищем, существует ли такая область в БД
					$data = $conn->prepare('SELECT * FROM `regions` WHERE `name` LIKE :name');
					$data->execute(["name" => $_POST['region']]);
					$res = $data->fetchAll();
					if (count($res) > 0)//если найдена запись в БД, то
					{
						$err = '<p>ОШИБКА: уже есть такая область в БД!</p>';//записываем в переменную ошибку для последующего вывода
					}
					else//иначе
					{
						//добавляем запись об области в БД
						$sql = 'INSERT INTO `regions` (`name`, `governor`)
						VALUES (:name, :governor)';
						$data = $conn->prepare($sql);
						$data->execute(array(
							"name" => $_POST['region'], 
							"governor" => $_POST['governor']
						));
						$count = $data->rowCount();//записываем в переменную кол-во изменённых строк
						if ($count == 1)//если кол-во этих строк равно 1, то
						{
							$err = '<p>Успешно добавлена новая область!</p>';//записываем в переменную положительный результат для последующего вывода
						}
						else//иначе
						{
							$err = '<p>ОШИБКА: неудачная попытка добавления новой записи (области) в БД!</p>';//записываем в переменную ошибку для последующего вывода
						}
					}
				}
				else//если поля пустые, то
				{
					$err = '<p>ОШИБКА: пустые поля при добавлении области!</p>';//записываем в переменную ошибку для последующего вывода
				}
			}
			
			//получаем список областей из БД
			$data = $conn->prepare('SELECT * FROM `regions`');
			$data->execute();
			$regions = $data->fetchAll();//записываем их в переменную
			if (count($regions) > 0)//если найдена хотя бы одна область, то
			{
				//добавляем в контент форму для добавления города
				$content = '<h3>Добавление данных по городу</h3><form enctype="multipart/form-data" method="POST">';
				$content .= '<p>Область: <br><select name="id_region">';
				foreach ($regions as $i)//в цикле добавляем теги для выпадающего списка (select)
				{
					$content .= '<option value="'.$i['id'].'">'.$i['name'].'</option>';
				}
				$content .= '</select></p>
					<p><label for="city">Название города: </label><br><input type="text" name="city" value=""></p>
					<p><label for="desc">Краткое описание: </label><br><textarea rows="10" cols="50" name="desc"></textarea>
					</p>
					<p><label for="picture">Изображение города: </label><br><input type="file" name="picture" accept="image/*"></p>
					<input type="submit" name="submit_city" value="Добавить">
				</form>';
			}
			//добавляем в контент форму для добавления области
			$content .= '<h3>Добавление данных по области</h3><form method="POST">
				<p><label for="region">Название области: </label><br><input type="text" name="region" value=""></p>
				<p><label for="governor">Имя и фамилия губернатора: </label><br><input type="text" name="governor" value=""></p>
				<input type="submit" name="submit_region" value="Добавить">
			</form>';
		?>
		<div>
			<?php 
				echo $content;//вывод 
				echo '<div id="errors">'.$err.'</div>';//вывод полученного результата (ошибки или подтверждение добавления данных в БД)
			?>
		</div>
	</div>
</body>
</html>