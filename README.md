* README

install:
	: composer install

config:
	: $api_url = 'http://localhost:8384';
    : $APIKey = 'xfsRu4txHuydQUambxxxxxxxxxxxxx';
    : $folderid = 'folderid-name';

run:
	: php show-ignored.php > result.txt
	: grep "local ignored" result.txt    # find local ignored files/directories
	: grep "path excep" result.txt
