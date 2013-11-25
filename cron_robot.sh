#!/bin/sh
# 图片搜索引擎 V1
# 2012-05-24 weifabing

PHP=/app/LAMP/php/bin/php
ROBOT=/www/img/robot.php
PREFIX="/pic/images/"
TODAY=$(date +%Y/%m/%d)
YESTERDAY=$(date --date='1 day ago' +%Y/%m/%d)
TODAY_DIR=today

# step 1:
if ! test -d "$PREFIX$TODAY"
then
    #echo "$PREFIX$TODAT"
    mkdir -p "$PREFIX$TODAY"
    chown ftpuser.ftpgroup -R "$PREFIX$TODAY"
fi

# step 2:
rm -f "$PREFIX$TODAY_DIR"
ln -sf "$PREFIX$TODAY" "$PREFIX$TODAY_DIR"

# step 3:
chmod -w -R "$PREFIX$YESTERDAY"

# step 4:
$PHP $ROBOT "$YESTERDAY"

