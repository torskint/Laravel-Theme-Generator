@echo off
echo Type "c" for commit, "u" for update, or "q" to quit.
cd "F:\htdocs\htdocs\laravel-projects\theme-generator-package__test\packages\laravel-theme-generator"

set GIT_PATH="C:\Program Files\Git\bin\git.exe"
set BRANCH="origin main"

:P
set ACTION=
set /P ACTION=Type action: 

rem Commit action
if "%ACTION%"=="c" (
    echo Adding changes to staging...
    %GIT_PATH% add -A

    rem Get the current date and time for commit message
    for /f "tokens=2 delims==" %%I in ('"echo %date% %time%"') do set COMMIT_DATE=%%I

    rem Commit with a message including the date and time
    echo Committing changes with message...
    %GIT_PATH% commit -am "Auto-committed on %COMMIT_DATE%"

    rem Ask the user for a custom tag
    set /P TAG_NAME=Enter a tag name for this commit: 

    rem Check if the tag name is empty
    if "%TAG_NAME%"=="" (
        echo No tag entered. Using default tag based on date and time.
        set TAG_NAME=commit_%COMMIT_DATE%
    )

    rem Add the tag to the commit
    echo Adding tag %TAG_NAME%...
    %GIT_PATH% tag %TAG_NAME%

    rem Optionally pull before pushing
    rem %GIT_PATH% pull %BRANCH%

    rem Push the changes to the remote branch
    echo Pushing changes to %BRANCH%...
    %GIT_PATH% push %BRANCH%

    rem Push the tag to the remote repository
    echo Pushing tag %TAG_NAME%...
    %GIT_PATH% push %BRANCH% %TAG_NAME%
)

rem Update action
if "%ACTION%"=="u" (
    echo Pulling latest changes from %BRANCH%...
    %GIT_PATH% pull %BRANCH%
)

rem Quit
if "%ACTION%"=="q" exit /b

rem Handle invalid input
echo Invalid action, please type "c", "u", or "q".
goto P
