@echo off
echo type "commit" or "update"
cd "F:\htdocs\htdocs\laravel-projects\theme-generator-package__test\packages\laravel-theme-generator"

set GIT_PATH="C:\Program Files\Git\bin\git.exe"
set BRANCH = "origin master"

:P
set ACTION=
set /P ACTION=Action: %=%

if "%ACTION%"=="c" (
	%GIT_PATH% add -A
	%GIT_PATH% commit -am "Auto-committed on %date%"
	rem %GIT_PATH% pull %BRANCH%
	%GIT_PATH% push %BRANCH%
)

rem mettre a jour
if "%ACTION%"=="u" (
	%GIT_PATH% pull %BRANCH%
)

rem Quitter
if "%ACTION%"=="q" exit /b
goto P