rm results-table.zip
npm run build
zip results-table \
    build/results-table/* \
    build/results-table/calc/* \
    build/results-table/calc/algorithms/* \
    results-table.php

docker cp ./build  gbsra-new-wordpress-1:/var/www/html/wp-content/plugins/results-table/
docker cp ./results-table.php  gbsra-new-wordpress-1:/var/www/html/wp-content/plugins/results-table/