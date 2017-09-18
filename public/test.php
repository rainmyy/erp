<?php
$data[] = array('mingzi' => '张三', 'baifenbi' => 100 );
$data[] = array('mingzi' => '李四', 'baifenbi' => 25);
$data[] = array('mingzi' => '郑五', 'baifenbi' => 40);
//排序前
echo "<xmp>";
print_r($data);
echo "</xmp>";

//要求，把 baifenbi 升序排列。 

//现在有了包含有行的数组 $data，但是 array_multisort() 需要一个包含列的数组，因此用以下代码来取得列，然后排序。 

// 取得列的列表
foreach ($data as $key => $row) {
    $baifenbi[$key] = $row['baifenbi'];
}

// 根据 baifenbi 升序排列
// 把 $data 作为最后一个参数，以通用键排序
array_multisort($baifenbi,SORT_ASC, $data);
//排序后
echo "<xmp>";
print_r($data);
echo "</xmp>";
?> 