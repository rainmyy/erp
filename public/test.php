<?php
$data[] = array('mingzi' => '����', 'baifenbi' => 100 );
$data[] = array('mingzi' => '����', 'baifenbi' => 25);
$data[] = array('mingzi' => '֣��', 'baifenbi' => 40);
//����ǰ
echo "<xmp>";
print_r($data);
echo "</xmp>";

//Ҫ�󣬰� baifenbi �������С� 

//�������˰������е����� $data������ array_multisort() ��Ҫһ�������е����飬��������´�����ȡ���У�Ȼ������ 

// ȡ���е��б�
foreach ($data as $key => $row) {
    $baifenbi[$key] = $row['baifenbi'];
}

// ���� baifenbi ��������
// �� $data ��Ϊ���һ����������ͨ�ü�����
array_multisort($baifenbi,SORT_ASC, $data);
//�����
echo "<xmp>";
print_r($data);
echo "</xmp>";
?> 