<html>
<head>
<title>Новый отчет</title>
</head>
<body>
<h1>Новый отчет</h1>
<form method="post" action="index.php" enctype="multipart/form-data">
<label for="inputfile">Сформировать отчет</label></br></br></br>
<label for="rkk">РКК</label> <input type="file" id="array1" name="array1"></br></br>
<label for="appeal">Обращения</label> <input type="file" id="array2" name="array2"></br></br>
<!--<label for="sort">Отсортировать</br>
<input type="radio" name="sort" value="name"> По имени</br>
<input type="radio" name="sort" value="RKK"> По РКК</br>
<input type="radio" name="sort" value="appeal">По обращениям</br>
<input type="radio" name="sort" value="all_doc">По общему количеству документов и обращений</label></br></br>-->
<input type="submit" value="Загрузить"></br></br></br>
<input type="button" name="unload" id="unload" value="Выгрузить отчет"></br></br></br>
<?php

$name1=$_FILES['array1']['name'];

$name2=$_FILES['array2']['name'];


	function name_format($str){
		$str= explode(" ", $str);
		$str1=strval($str[1]);
		$str2=strval($str[2]);
		$len1=ceil((strlen($str1))/2);
		$l1=1-$len1;
		$len2=ceil((strlen($str2))/2);
		$l2=1-$len2;
		$str1=mb_substr($str1,0, $l1);
		$str2=mb_substr($str2,0, $l2);
		$str=$str[0]." ".$str1.".".$str2.".";
		return $str;
	}
	
	function str_lenght($str,$lenght){
		$i=$lenght-mb_strlen($str);
		while($i>0){
			$str=$str." ";
			$i=$i-1;
		}
		return $str;
	}
	
	function Count_array($arr){
		$array1=[];
		foreach ($arr as $key => $value) {
			$arr[$key]=explode(" ", $value);
			$arr[$key][2]=$arr[$key][2].";";
			$array1[]=implode($arr[$key], " ");
		}
		$array2=[];
		foreach($array1 as $key => $value){
			$arr3[$key]=explode(";", $value);
			for($i=0;$i<count($arr3[$key]);$i++){
				trim($arr3[$key][$i]);
			}
			if($arr3[$key][0]=="Климов Сергей Александрович"){
				$arr3[$key][1]=str_replace("(Отв.)", " ", $arr3[$key][1]);
				$array2[]=trim($arr3[$key][1]);
			}
			else{
				$arr3[$key][0]=name_format($arr3[$key][0]);
				$array2[]=trim($arr3[$key][0]);
			}
					
		}
		$array2=array_count_values($array2);
		return $array2;
	}
	
	function Union_of_arrays($array1, $array2){
		$arr1=array_intersect_key($array1, $array2);
		$arr2=array_diff_key($array1, $array2);
		$arr3=array_diff_key($array2, $array1);
		$arr4=array_intersect_key($array2, $array1);
		$arr5=[];
		foreach($arr1 as $key => $value){
			$arr5[]=[$key,$value,$arr4[$key],$value+$arr4[$key]];
		}
		foreach($arr2 as $key => $value){
			$arr5[]=[$key,$value,0,$value];
		}
		foreach($arr3 as $key => $value){
			$arr5[]=[$key,0,$value,$value];
		}
		sort($arr5);
		//сортировка
		/*$array1=[];
		foreach($arr5 as $key => $value){
			$array1[$key]=$arr5[$key][3];
		}
		$array2=[];
		while(count($array1)>0){
			$a=max($array1);
			$key = array_search ($a, $array1);
			//print_r($key);
			echo $a." * ".$key;
			echo "<br>";
			$array2[]=$arr5[$key];
			unset($array1[$key]);
			in_array($array1[$key]);
			
		}
		
		
		//print_r($array2);*/
		return $arr5;
	}
	
	
	function Heading_create($arr){
		$PKK_count=0;
		$appeal_count=0;
		$all_doc=0;
		foreach($arr as $key => $value){
			$PKK_count=$PKK_count+$value[1];
			$appeal_count=$appeal_count+$value[2];
			$all_doc=$all_doc+$value[3];
		}
		$sort="по отвественному исполнителю";
		echo "<div align='center'><b>Справка о неисполненных документах и обращениях граждан</b></div>"."<br><br>";
		echo "Не исполнено в срок <b>".$all_doc."</b> документов, из них:"."<br><br>";
		echo "- количество неисполненных входящих документов: <b>".$PKK_count.";</b> "."<br><br>";
		echo "- количество неисполненных письменных обращений граждан: <b>".$appeal_count."</b><br><br>";
		echo "Сортировка: <b>.$sort.</b>"."<br><br>";
		
	}
	
	
	
	function Report_form($arr){
		
		echo "<table border='1'>";
		echo "<tr align='center'><td>№ п.п.</td><td>"."Ответственный<br> исполнитель"."</td><td>"."Количество неисполненных <br>входящих документов"."</td><td>"."Количество <br>неисполненных<br> письменных<br> обращений граждан"."</td><td>"."Общее количество<br> документов<br> и обращений"."</td></tr>";
		foreach($arr as $key => $value){
			$N=$key+1;
			echo "<tr align='center'><td>$N</td><td align='left'>".$value[0]."</td><td>".$value[1]."</td><td>".$value[2]."</td><td>".$value[3]."</td><tr>";
		}
		echo "</table>";
	}
	
	function Writing_to_file($arr,$filename){
		file_put_contents($filename, '');
		$PKK_count=0;
		$appeal_count=0;
		$all_doc=0;
		foreach($arr as $key => $value){
			$PKK_count=$PKK_count+$value[1];
			$appeal_count=$appeal_count+$value[2];
			$all_doc=$all_doc+$value[3];
		}
		$head="Справка о неисполненных документах и обращениях граждан"."\r\n".
		"Не исполнено в срок ".$all_doc." документов, из них:"."\r\n".
		"- количество неисполненных входящих документов: ".$PKK_count."; "."\r\n".
		"- количество неисполненных письменных обращений граждан: ".$appeal_count."\r\n\r\n";
		
		file_put_contents($filename, $head, FILE_APPEND);
		$table_head1= "Ответсвенный    Количество неисполненных   Количество неисполненных           Общее количество\r\n";
		$table_head2= "исполнитель     входящих документов        письменных обращений граждан       документов и обращений\r\n \r\n";
		$tr_head=$table_head1.$table_head2;
		file_put_contents($filename, $tr_head, FILE_APPEND);
		foreach($arr as $key => $value){
			$lenght1=mb_strlen("Ответсвенный"." "." "." "." "." "." "." ");
			$lenght2=mb_strlen("Количество неисполненных"." "." "." "." "." ");
			$lenght3=mb_strlen("письменных обращений граждан"." "." "." "." "." ");
			$lenght4=mb_strlen("документов и обращений"." "." "." "." "." ");
			$text1=str_lenght($value[0],$lenght1);
			$text2=str_lenght($value[1].";",$lenght2);
			$text3=str_lenght($value[2].";",$lenght3);
			$text4=str_lenght($value[3].";",$lenght4);
			$text=$text1.$text2.$text3.$text4."\r\n"."\r\n";
			file_put_contents($filename, $text, FILE_APPEND);
				
		}
		$today = date("j. m. Y"); 
		$date="Дата составления справки: ".$today;
		file_put_contents($filename, $date, FILE_APPEND);
	}
	
	
	
	
	function Report_create($array1, $array2){
		$array1=Count_array($array1);
	    $array2=Count_array($array2);
		$arr=Union_of_arrays($array1, $array2);
		Heading_create($arr);
		Report_form($arr);
		$today = date("j. m. Y"); 
		echo "Дата составления справки: ".$today;
		$filename="report.txt";
		Writing_to_file($arr,$filename);
		
	}
	
	if($name1){
		$array1=file($name1);
	} else{
		$array1=[];
	}
	if($name2){
		$array2=file($name2);
	} else {
		$array2=[];
	}
	if($name1||$name2){
	 
	Report_create($array1, $array2);
	}
	if(isset($_POST['unload'])){
		$filename="report.txt";
		Writing_to_file($arr,$filename);
	}
?>
</form>
</body>
</html>
