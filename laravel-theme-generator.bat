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

    rem Get the current date and time for commit message
    set COMMIT_DATE="1.1.2"

    rem Commit with a message including the date and time
    echo Committing changes with message...
    %GIT_PATH% commit -am "Auto-committed on %COMMIT_DATE%"

    rem Ask the user for a custom tag
    set /P TAG_NAME=Enter a tag name for this commit: 

    rem rem Check if the tag name is empty
    if "%TAG_NAME%"=="" (
        echo No tag entered. Using default tag based on date and time.
        set TAG_NAME=commit_%COMMIT_DATE%
    )

    rem Add the tag to the commit
    echo Adding tag %TAG_NAME%...
    rem %GIT_PATH% tag %TAG_NAME%

    rem Push the changes to the remote branch
    %GIT_PATH% push %BRANCH% --tag %TAG_NAME%
)

rem Quit
if "%ACTION%"=="q" exit /b

rem Handle invalid input
rem echo Invalid action, please type "c", "u", or "q".
goto P
