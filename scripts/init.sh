rm -rf .git

CURRENT_PROJECT=$(basename "$PWD")

mv wp-content/themes/custom-theme wp-content/themes/"$CURRENT_PROJECT"

git init
git add .
git commit -m "init"

echo ""
echo "Initialization completed successfully"