#rm -rf * .[^.]*
#rsync -av --exclude='/node_modules/**' --exclude='/vendor/**' --exclude='/dist/**' --exclude='/.git' --exclude='/dt-cr.zip' --exclude='README.md' --exclude='/.wp-env.json' . <destination>
git init

git remote add origin git@github.com:Dream-Theme/comprehensive-responsiveness-plugin.git

git pull origin master

git push --set-upstream origin master

find . -path ./.git -prune -o -exec rm -rf {} \; 2> /dev/null

rsync -av --exclude='/node_modules/**' --exclude='/vendor/**' --exclude='/dist/**' --exclude='/.git' --exclude='/dt-cr.zip' --exclude='README.md' --exclude='/.wp-env.json' <source> .

git add * .[^.]*

