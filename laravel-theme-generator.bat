@echo off
echo Type "c" for commit, "u" for update, or "q" to quit.
cd "F:\htdocs\htdocs\laravel-projects\theme-generator-package__test\packages\laravel-theme-generator"

rem set GIT_PATH="C:\Program Files\Git\bin\git.exe"
rem set BRANCH="origin main"

set GIT_PATH="C:\Program Files\Git\bin\git.exe"
set BRANCH = "origin master"

:P
set ACTION=
set /P ACTION=Type action: 

rem Commit action
if "%ACTION%"=="c" (
    echo Adding changes to staging...
    %GIT_PATH% add -A

    rem Commit with a message including the date and time
    echo Committing changes with message...
    %GIT_PATH% commit -m "Auto-committed on %date%"

    rem Ask the user for a custom tag
    :D
    set /P TAG_NAME=Enter a tag name for this commit: 

    rem rem Check if the tag name is empty
    if "%TAG_NAME%"=="" (
        goto D
    )

    rem Add the tag to the commit
    echo Adding tag %TAG_NAME%...
    %GIT_PATH% tag %TAG_NAME%

    rem Push the changes to the remote branch
    %GIT_PATH% push %BRANCH% --tags
)

rem Quit
if "%ACTION%"=="q" exit /b

rem Handle invalid input
rem echo Invalid action, please type "c", "u", or "q".
goto P
