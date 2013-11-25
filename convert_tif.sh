#!/bin/bash
# 图片缩略图生成程序,将tiff文件生成jpg缩略图 V2
# 2012/11/26 weifabing
IFS_old=$IFS
IFS=$'\n'
TODAY=$(date +%Y/%m/%d)
ROOT="/pic/images/"
if [ $# -eq 1 ]; then
  PIC_ROOT="${ROOT}$1"
else
  PIC_ROOT="${ROOT}${TODAY}"
fi
if [ ! -e ${PIC_ROOT} ]; then
  echo ${PIC_ROOT}" not exits"
  exit 1
fi
i=0
for file in `find $PIC_ROOT -type f -iname '[^.]*.tif'`
do
	if [ ! -f "$file" ]; then
		continue
	fi
	i=$(($i+1))
	#step 1:ready info
	file_dir=$(dirname "$file")
	file_name=$(basename "$file"|sed -e "s/.tif/.jpg/" -e "s/.TIF/.jpg/")
	thum_dir=$file_dir/thum
	thum_file=$thum_dir/$file_name
	#setp2: make thum dir
	if [ ! -d "$thum_dir" ]; then
		mkdir "$thum_dir"
	fi
	#step 3: convert tif file and chown
	if [ -f "$thum_file" ]; then
		echo -e "${i}\t${thum_file}\t\tconvert over"
		continue
	fi
	convert -resize 400x300 -strip -quality 75% "$file" "$thum_file" 2>/dev/null
	if [ -f "$thum_file" ]; then
		chown ftpuser.ftpgroup "${thum_file}"
		echo -e "${i}\t${thum_file}\t\tconvert ok"
	else
		echo -e "${i}\t${file}\t\terror"
	fi
done
IFS=$IFS_old
