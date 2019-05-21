* 路由举例

```
index.php?a=Test&m=get&doctorId=1234
```

参数:

	a           固定参数 Action下类名
	m           Action中类方法
	doctorId    可选参数
	···

* 系统方法

```
C()   获取配置文件中的值
M()   获取Model实例
A()   获取Action实例
```

* 接口返回举例

```
{
	"code": 1,               在Error中配置
	"message": "SUCCESS",    获取Erorr中字符串值
	"content": "			 返回自定义类型
}

```
* 其他

```
配置项中日志文件目录需要手动配置
www/index.php 为单一入口文件
ZSPHP 为框架内容
Lib 为应用业务代码
```