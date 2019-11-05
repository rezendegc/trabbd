FROM mysql

COPY ./init_db.sh /docker-entrypoint-initdb.d
COPY ./custom-mysql.cnf /etc/mysql/conf.d/