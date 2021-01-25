<?php


class PlayCode
{
    function siweitest(){
        //定义变量
//        $a = 5; $b = 6; $c = 0;

//        while($c!=3){
//            $c = $c+($b-$a);
//        }
//        echo $c;

//        $a = 1; $b = 1; $c = 1;
//        $aa = 0; $bb = 0; $cc = 0;
//        echo $a,$b,$c,$aa,$bb,$cc;
//        echo "\n";
//        $num = $a;
//        $a = $bb;
//        $bb = $num;
//        echo $b,$a,$c,$aa,$bb,$cc;

        //直角三角形
        for ($i=0;$i<=6;$i++){
            echo str_repeat("*",$i*2+1);
            echo "\n";
        }

        //全等三角形
        for ($i=0;$i<10;$i++){
//            //输出空格
            for ($j=10;$j>$i;$j--){
                echo " ";
            }
            //输出星号
            for ($k=0;$k<($i+1)*2-1;$k++){
                echo "*";
            }
            //换行
            echo "\n";
        }


        //************************菱形开始************//

        $n = 10;
        for($i=1;$i<=$n;$i++){
            for($j=1;$j<=$n-$i;$j++){
                echo "  ";
            }
            for($z=1;$z<=2*$i-1;$z++){
                echo "* ";
            }
            echo "\n";
        }
        for($i=$n-1;$i>=1;$i--){
            for($j=1;$j<=$n-$i;$j++){
                echo "  ";
            }
            for($z=1;$z<=2*$i-1;$z++){
                echo "* ";
            }
            echo "\n";
        }
//************************菱形结束************//
        for($i=1;$i<=10;$i++){
            for($j=10;$j>=$i;$j--){
                echo " ";
            }
            for($z=1;$z<=$j;$z++){
                echo "*"." ";
            }
            echo "\n";
        }
        for($i=10;$i>=1;$i--){
            for($j=10;$j>=$i;$j--){
                echo " ";
            }
            for ($z=1;$z<=$j;$z++){
                echo "*"." ";
            }
            echo "\n";
        }
    }
}