#!/bin/bash
procs=$(pgrep -fa hunter.php| grep php| awk {'print $1'})
workers="40"
if [[ $procs < 1 ]]; then
	echo "starting hunter"
	/usr/bin/php ./hunter.php $workers
fi
