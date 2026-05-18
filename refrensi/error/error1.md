# Symfony\Component\Routing\Exception\RouteNotFoundException - Internal Server Error

Route [profile.edit] not defined.

PHP 8.4.15
Laravel 13.9.0
127.0.0.1:8000

## Stack Trace

0 - vendor\laravel\framework\src\Illuminate\Routing\UrlGenerator.php:546
1 - vendor\laravel\framework\src\Illuminate\Foundation\helpers.php:870
2 - resources\views\components\desktop-user-menu.blade.php:1
3 - vendor\laravel\framework\src\Illuminate\Filesystem\Filesystem.php:123
4 - vendor\laravel\framework\src\Illuminate\Filesystem\Filesystem.php:124
5 - vendor\laravel\framework\src\Illuminate\View\Engines\PhpEngine.php:57
6 - vendor\livewire\livewire\src\Mechanisms\ExtendBlade\ExtendedCompilerEngine.php:22
7 - vendor\laravel\framework\src\Illuminate\View\Engines\CompilerEngine.php:76
8 - vendor\livewire\livewire\src\Mechanisms\ExtendBlade\ExtendedCompilerEngine.php:10
9 - vendor\laravel\framework\src\Illuminate\View\View.php:208
10 - vendor\laravel\framework\src\Illuminate\View\View.php:191
11 - vendor\laravel\framework\src\Illuminate\View\View.php:160
12 - vendor\laravel\framework\src\Illuminate\View\Concerns\ManagesComponents.php:103
13 - resources\views\layouts\app\sidebar.blade.php:109
14 - vendor\laravel\framework\src\Illuminate\Filesystem\Filesystem.php:123
15 - vendor\laravel\framework\src\Illuminate\Filesystem\Filesystem.php:124
16 - vendor\laravel\framework\src\Illuminate\View\Engines\PhpEngine.php:57
17 - vendor\livewire\livewire\src\Mechanisms\ExtendBlade\ExtendedCompilerEngine.php:22
18 - vendor\laravel\framework\src\Illuminate\View\Engines\CompilerEngine.php:76
19 - vendor\livewire\livewire\src\Mechanisms\ExtendBlade\ExtendedCompilerEngine.php:10
20 - vendor\laravel\framework\src\Illuminate\View\View.php:208
21 - vendor\laravel\framework\src\Illuminate\View\View.php:191
22 - vendor\laravel\framework\src\Illuminate\View\View.php:160
23 - vendor\laravel\framework\src\Illuminate\View\Concerns\ManagesComponents.php:103
24 - resources\views\pages\admin\dashboard.blade.php:1
25 - vendor\laravel\framework\src\Illuminate\Filesystem\Filesystem.php:123
26 - vendor\laravel\framework\src\Illuminate\Filesystem\Filesystem.php:124
27 - vendor\laravel\framework\src\Illuminate\View\Engines\PhpEngine.php:57
28 - vendor\livewire\livewire\src\Mechanisms\ExtendBlade\ExtendedCompilerEngine.php:22
29 - vendor\laravel\framework\src\Illuminate\View\Engines\CompilerEngine.php:76
30 - vendor\livewire\livewire\src\Mechanisms\ExtendBlade\ExtendedCompilerEngine.php:10
31 - vendor\laravel\framework\src\Illuminate\View\View.php:208
32 - vendor\laravel\framework\src\Illuminate\View\View.php:191
33 - vendor\laravel\framework\src\Illuminate\View\View.php:160
34 - vendor\laravel\framework\src\Illuminate\Http\Response.php:78
35 - vendor\laravel\framework\src\Illuminate\Http\Response.php:34
36 - vendor\laravel\framework\src\Illuminate\Routing\ResponseFactory.php:61
37 - vendor\laravel\framework\src\Illuminate\Routing\ResponseFactory.php:91
38 - vendor\laravel\framework\src\Illuminate\Routing\ViewController.php:40
39 - vendor\laravel\framework\src\Illuminate\Routing\ViewController.php:57
40 - vendor\laravel\framework\src\Illuminate\Routing\ControllerDispatcher.php:43
41 - vendor\laravel\framework\src\Illuminate\Routing\Route.php:269
42 - vendor\laravel\framework\src\Illuminate\Routing\Route.php:215
43 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:822
44 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:180
45 - vendor\laravel\boost\src\Middleware\InjectBoost.php:22
46 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
47 - vendor\laravel\framework\src\Illuminate\Routing\Middleware\SubstituteBindings.php:52
48 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
49 - vendor\laravel\framework\src\Illuminate\Auth\Middleware\Authenticate.php:63
50 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
51 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\PreventRequestForgery.php:104
52 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
53 - vendor\laravel\framework\src\Illuminate\View\Middleware\ShareErrorsFromSession.php:48
54 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
55 - vendor\laravel\framework\src\Illuminate\Session\Middleware\StartSession.php:120
56 - vendor\laravel\framework\src\Illuminate\Session\Middleware\StartSession.php:63
57 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
58 - vendor\laravel\framework\src\Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse.php:36
59 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
60 - vendor\laravel\framework\src\Illuminate\Cookie\Middleware\EncryptCookies.php:74
61 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
62 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:137
63 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:821
64 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:800
65 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:764
66 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:753
67 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Kernel.php:200
68 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:180
69 - vendor\livewire\livewire\src\Features\SupportDisablingBackButtonCache\DisableBackButtonCacheMiddleware.php:19
70 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
71 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\TransformsRequest.php:21
72 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull.php:31
73 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
74 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\TransformsRequest.php:21
75 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\TrimStrings.php:51
76 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
77 - vendor\laravel\framework\src\Illuminate\Http\Middleware\ValidatePostSize.php:27
78 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
79 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance.php:109
80 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
81 - vendor\laravel\framework\src\Illuminate\Http\Middleware\HandleCors.php:61
82 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
83 - vendor\laravel\framework\src\Illuminate\Http\Middleware\TrustProxies.php:58
84 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
85 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\InvokeDeferredCallbacks.php:22
86 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
87 - vendor\laravel\framework\src\Illuminate\Http\Middleware\ValidatePathEncoding.php:28
88 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
89 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:137
90 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Kernel.php:175
91 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Kernel.php:144
92 - vendor\laravel\framework\src\Illuminate\Foundation\Application.php:1220
93 - public\index.php:20
94 - vendor\laravel\framework\src\Illuminate\Foundation\resources\server.php:23


## Request

GET /admin/dashboard

## Headers

* **host**: 127.0.0.1:8000
* **user-agent**: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:150.0) Gecko/20100101 Firefox/150.0
* **accept**: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8
* **accept-language**: en-US,en;q=0.9
* **accept-encoding**: gzip, deflate, br, zstd
* **referer**: http://127.0.0.1:8000/
* **connection**: keep-alive
* **cookie**: XSRF-TOKEN=eyJpdiI6IkV1L3Z2eVl5U0MxM0FVSVFaUkI4VFE9PSIsInZhbHVlIjoiSUpxY0xERHFSNXFNL0drT3lLUkY4U04reWFhOXhJNit4MXpXUnRzdU53U3A2aUtiNHRUS3dDOUF5RDZXSkovVUZzS0g2dHlmaWcrMDBjVEVjYkluN2o5M0pjNTJCdFFQc2ROZnBxTTd0eUUxTzFsaEd5UHFnOUNpc29NSlU5RVMiLCJtYWMiOiIwMDc0N2ZjMTcwZDViYjg3YmI3NDAwOWI1Y2Y0ZDliNTNlZjNmOWViY2Y3MWE4YWQ3YWU4NmRlODI5YTljZDUxIiwidGFnIjoiIn0%3D; portal-absen-session=eyJpdiI6IjhiL0o5bWtaNm5sQTlMd3MwZm5VRGc9PSIsInZhbHVlIjoiOEwwVmF1d0tGbTAxUTFuUm1DeGNkL0NmNThnL0N3b0k2S1lqR3k2WVdwRjNKODRib3FCN1liSC82V0JKNm9XRlZuQjBxY2pTZTRQemk1Z0FobTVQc0h3MitWQWtIckJOSnAwMExiU1RjZTB4QXowQm94bStlcnd6Y0gvTHh4cVMiLCJtYWMiOiJkNmNiODgzZTNhYTExYzAxZDYzMzM2ZThhZjE3YzYyNmQ5YTlkN2MzNDNjMjc4YjlkNzI5MTliNjQ0ZmRhMTg2IiwidGFnIjoiIn0%3D
* **upgrade-insecure-requests**: 1
* **sec-fetch-dest**: document
* **sec-fetch-mode**: navigate
* **sec-fetch-site**: same-origin
* **sec-fetch-user**: ?1
* **priority**: u=0, i

## Route Context

controller: \Illuminate\Routing\ViewController
route name: admin.dashboard
middleware: web, auth

## Route Parameters

{
    "view": "pages.admin.dashboard",
    "data": [],
    "status": 200,
    "headers": []
}

## Database Queries

* mysql - select * from `sessions` where `id` = 'HX33ZI4BJCNwzHjGugxlRye66ndR4DYX6gzPbqkh' limit 1 (2.78 ms)
* mysql - select * from `users` where `id` = 2 limit 1 (2.13 ms)
* mysql - select count(*) as aggregate from `siswa` (0.61 ms)
* mysql - select count(*) as aggregate from `absensi` where date(`tanggal`) = '2026-05-17' and `status` = 'hadir' (0.72 ms)
* mysql - select count(distinct `user_id`) as aggregate from `absensi` where date(`tanggal`) = '2026-05-17' (0.69 ms)
* mysql - select count(*) as aggregate from `pengajuan_poin` where `status` = 'pending' (0.65 ms)
