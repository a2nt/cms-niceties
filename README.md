# cms-niceties
Some useful CMS objects and extensions

## Usefull UNIX console utilities

### Code search  (find . -name "*.*" | xargs grep "some text" replacement)

ag "some text" ./
https://github.com/ggreer/the_silver_searcher

### File content with code hightlighting (cat replacement)

bat ./app/src/Pages/Page.php
https://github.com/sharkdp/bat

### File listing (ls replacement)

exa -aTL3 ./app
https://github.com/ogham/exa

### git diff tool with bat code hightlighting (git show)

https://github.com/dandavison/delta