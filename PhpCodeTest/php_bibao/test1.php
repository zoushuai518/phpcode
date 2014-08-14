<?php

// zs notes		php闭包 需要深入理解

/**
 * 下面提到的代码在PHP5.3以上版本运行通过.
 */

# ag:1

/*
function callback($callback) {
	$callback();
}

callback(function() {
	//print "This is a anonymous function.<br />/n";
	echo "This is a anonymous function.<br />";
});
*/



# ag:2

function callback($callback) {
	$callback();
}

//众所周知, 闭包: 内部函数使用了外部函数中定义的变量.
//在PHP新开放的闭包语法中, 我们就是用use来使用闭包外部定义的变量的.
//这里我们使用了外部变量$msg, 定义完之后, 又对其值进行了改变, 闭包被执行后输出的是原始值
//结论: 以传值方式传递的基础类型参数, 闭包use的值在闭包创建是就确定了.
$msg = "Hello, everyone";
$callback = function () use ($msg) {
	print "This is a closure use string value, msg is: $msg. <br />";
};
$msg = "Hello, everybody";
callback($callback);
//输出: This is a closure use string value lazy bind, msg is: Hello, everybody.<br />


//换一种引用方式, 我们使用引用的方式来use
//可以发现这次输出是闭包定义后的值...
//这个其实不难理解, 我们以引用方式use, 那闭包use的是$msg这个变量的地址
//当后面对$msg这个地址上的值进行了改变之后, 闭包内再输出这个地址的值时, 自然改变了.
$msg = "Hello, everyone";
$callback = function () use (&$msg) {
	print "This is a closure use string value lazy bind, msg is: $msg. <br />";
};
$msg = "Hello, everybody";
callback($callback);
//输出: This is a closure use object, msg is: Hello, everyone.<br />
//闭包中输出的是之前被拷贝的值为Hello, everyone的对象, 后面是对$obj这个名字的一个重新赋值.
//可以这样考虑
//1. obj是对象Hello, everyone的名字
//2. 对象Hello, everyone被闭包use, 闭包产生了一个对Hello, everyone对象的引用
//3. obj被修改为Hello, everybody这个对象的名字
//4. 注意, 是名字obj代表的实体变了, 而不是Hello, everyone对象, 那自然闭包的输出还是前面的Hello, everyone


$obj = (object) "Hello, everyone";
$callback = function () use ($obj) {
	print "This is a closure use object, msg is: {$obj->scalar}. <br />";
};
$obj = (object) "Hello, everybody";
callback($callback);
//输出: This is a closure use object, msg is: Hello, everybody.<br />
//还是按照上面的步骤, 按部就班的来吧:
//1. obj名字指向Hello, everyone对象
//2. 闭包产生一个引用指向Hello, everyone对象
//3. 修改obj名字指向的对象(即Hello, everyone对象)的scalar值
//4. 执行闭包, 输出的自然是Hello, everybody, 因为其实只有一个真正的对象
$obj = (object) "Hello, everyone";
$callback = function () use ($obj) {
	print "This is a closure use object, msg is: {$obj->scalar}. <br />";
};
$obj->scalar = "Hello, everybody";
callback($callback);
//输出: This is a closure use object lazy bind, msg is: Hello, everybody.<br />/n
//闭包引用的是什么呢? &$obj, 闭包产生的引用指向$obj这个名字所指向的地址.
//因此, 无论obj怎么变化, 都是逃不脱的....
//所以, 输出的就是改变后的值
$obj = (object) "Hello, everyone";
$callback = function () use (&$obj) {
	print "This is a closure use object lazy bind, msg is: {$obj->scalar}. <br />";
};
$obj = (object) "Hello, everybody";
callback($callback);


/**
* 一个利用闭包的计数器产生器
* 这里其实借鉴的是python中介绍闭包时的例子...
* 我们可以这样考虑:
*         1. counter函数每次调用, 创建一个局部变量$counter, 初始化为1.
*         2. 然后创建一个闭包, 闭包产生了对局部变量$counter的引用.
*         3. 函数counter返回创建的闭包, 并销毁局部变量, 但此时有闭包对$counter的引用,
*             它并不会被回收, 因此, 我们可以这样理解, 被函数counter返回的闭包, 携带了一个游离态的变量.
*         4. 由于每次调用counter都会创建独立的$counter和闭包, 因此返回的闭包相互之间是独立的.
*         5. 执行被返回的闭包, 对其携带的游离态变量自增并返回, 得到的就是一个计数器.
* 结论: 此函数可以用来生成相互独立的计数器.
*/
function counter() {
	$counter = 1;
	return function() use(&$counter) {return $counter ++;};
}
$counter1 = counter();
//var_dump($counter1);
//die;
$counter2 = counter();
echo "counter1: " . $counter1() . "<br />";
echo "counter1: " . $counter1() . "<br />";
echo "counter1: " . $counter1() . "<br />";
echo "counter1: " . $counter1() . "<br />";
echo "counter2: " . $counter2() . "<br />";
echo "counter2: " . $counter2() . "<br />";
echo "counter2: " . $counter2() . "<br />";
echo "counter2: " . $counter2() . "<br />";

?>
